const express = require('express');
const { body, validationResult } = require('express-validator');
const { query, getClient } = require('../config/db');
const authenticate = require('../middleware/auth');
const authorize = require('../middleware/rbac');

const router = express.Router();

// GET /api/weekly-plans
router.get('/', authenticate, async (req, res, next) => {
  try {
    const { division_id, week_start, week_end } = req.query;
    let sql = `
      SELECT wp.*, u.name as submitted_by_name, d.name as division_name
      FROM weekly_plans wp
      JOIN users u ON wp.submitted_by = u.id
      JOIN divisions d ON wp.division_id = d.id
      WHERE 1=1
    `;
    const params = [];

    if (req.user.role === 'division_director') {
      params.push(req.user.division_id);
      sql += ` AND wp.division_id = $${params.length}`;
    } else if (division_id) {
      params.push(division_id);
      sql += ` AND wp.division_id = $${params.length}`;
    }

    if (week_start) {
      params.push(week_start);
      sql += ` AND wp.week_start >= $${params.length}`;
    }
    if (week_end) {
      params.push(week_end);
      sql += ` AND wp.week_end <= $${params.length}`;
    }

    sql += ' ORDER BY wp.created_at DESC';

    const result = await query(sql, params);
    res.json({ weeklyPlans: result.rows });
  } catch (err) {
    next(err);
  }
});

// POST /api/weekly-plans — Create a plan and auto-create an activity
router.post(
  '/',
  authenticate,
  authorize('division_director', 'admin'),
  [
    body('week_start').isDate(),
    body('week_end').isDate(),
    body('planned_activity').trim().notEmpty(),
    body('responsible_persons').isArray(),
    body('start_date').optional({ nullable: true }).isDate(),
    body('expected_completion_date').optional({ nullable: true }).isDate(),
    body('notes').optional().trim(),
  ],
  async (req, res, next) => {
    const client = await getClient();
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const {
        week_start, week_end, planned_activity,
        responsible_persons, start_date, expected_completion_date, notes,
      } = req.body;

      const divisionId = req.user.division_id;
      if (!divisionId) {
        return res.status(400).json({ error: 'You must be assigned to a division' });
      }

      await client.query('BEGIN');

      // 1. Create the trackable activity record
      const activityResult = await client.query(
        `INSERT INTO activities
         (division_id, title, description, assigned_users, start_date, deadline, status, created_from)
         VALUES ($1, $2, $3, $4, $5, $6, 'not_started', 'plan')
         RETURNING *`,
        [
          divisionId,
          planned_activity,
          notes || null,
          JSON.stringify(responsible_persons),
          start_date || week_start,
          expected_completion_date || week_end,
        ]
      );

      const activity = activityResult.rows[0];

      // 2. Create the weekly plan linked to the activity
      const planResult = await client.query(
        `INSERT INTO weekly_plans
         (division_id, submitted_by, week_start, week_end, planned_activity,
          responsible_persons, start_date, expected_completion_date, notes, activity_id)
         VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)
         RETURNING *`,
        [
          divisionId, req.user.id, week_start, week_end,
          planned_activity, JSON.stringify(responsible_persons),
          start_date || null, expected_completion_date || null,
          notes || null, activity.id,
        ]
      );

      await client.query('COMMIT');

      res.status(201).json({
        weeklyPlan: planResult.rows[0],
        activity,
      });
    } catch (err) {
      await client.query('ROLLBACK');
      next(err);
    } finally {
      client.release();
    }
  }
);

// PUT /api/weekly-plans/:id
router.put(
  '/:id',
  authenticate,
  authorize('division_director', 'admin'),
  async (req, res, next) => {
    try {
      const {
        planned_activity, responsible_persons,
        start_date, expected_completion_date, notes,
      } = req.body;

      const existing = await query('SELECT * FROM weekly_plans WHERE id = $1', [req.params.id]);
      if (existing.rows.length === 0) return res.status(404).json({ error: 'Not found' });

      if (req.user.role === 'division_director' && existing.rows[0].submitted_by !== req.user.id) {
        return res.status(403).json({ error: 'Access denied' });
      }

      const result = await query(
        `UPDATE weekly_plans SET
           planned_activity = COALESCE($1, planned_activity),
           responsible_persons = COALESCE($2, responsible_persons),
           start_date = COALESCE($3, start_date),
           expected_completion_date = COALESCE($4, expected_completion_date),
           notes = COALESCE($5, notes)
         WHERE id = $6 RETURNING *`,
        [
          planned_activity,
          responsible_persons ? JSON.stringify(responsible_persons) : null,
          start_date, expected_completion_date, notes, req.params.id,
        ]
      );

      // Sync to linked activity
      if (result.rows[0].activity_id) {
        await query(
          `UPDATE activities SET
             title = COALESCE($1, title),
             assigned_users = COALESCE($2, assigned_users),
             deadline = COALESCE($3, deadline),
             last_updated = NOW()
           WHERE id = $4`,
          [
            planned_activity,
            responsible_persons ? JSON.stringify(responsible_persons) : null,
            expected_completion_date,
            result.rows[0].activity_id,
          ]
        );
      }

      res.json({ weeklyPlan: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

// DELETE /api/weekly-plans/:id
router.delete(
  '/:id',
  authenticate,
  authorize('division_director', 'admin'),
  async (req, res, next) => {
    try {
      const result = await query(
        'DELETE FROM weekly_plans WHERE id = $1 RETURNING *',
        [req.params.id]
      );

      if (result.rows.length === 0) return res.status(404).json({ error: 'Not found' });

      res.json({ message: 'Weekly plan deleted' });
    } catch (err) {
      next(err);
    }
  }
);

module.exports = router;

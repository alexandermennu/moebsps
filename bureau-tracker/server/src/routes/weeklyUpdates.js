const express = require('express');
const { body, validationResult } = require('express-validator');
const { query } = require('../config/db');
const authenticate = require('../middleware/auth');
const authorize = require('../middleware/rbac');

const router = express.Router();

// GET /api/weekly-updates
router.get('/', authenticate, async (req, res, next) => {
  try {
    const { division_id, week_start, week_end, status } = req.query;
    let sql = `
      SELECT wu.*, u.name as submitted_by_name, d.name as division_name
      FROM weekly_updates wu
      JOIN users u ON wu.submitted_by = u.id
      JOIN divisions d ON wu.division_id = d.id
      WHERE 1=1
    `;
    const params = [];

    // Division directors can only see their own division
    if (req.user.role === 'division_director') {
      params.push(req.user.division_id);
      sql += ` AND wu.division_id = $${params.length}`;
    } else if (division_id) {
      params.push(division_id);
      sql += ` AND wu.division_id = $${params.length}`;
    }

    if (week_start) {
      params.push(week_start);
      sql += ` AND wu.week_start >= $${params.length}`;
    }
    if (week_end) {
      params.push(week_end);
      sql += ` AND wu.week_end <= $${params.length}`;
    }
    if (status) {
      params.push(status);
      sql += ` AND wu.status = $${params.length}`;
    }

    sql += ' ORDER BY wu.created_at DESC';

    const result = await query(sql, params);
    res.json({ weeklyUpdates: result.rows });
  } catch (err) {
    next(err);
  }
});

// GET /api/weekly-updates/:id
router.get('/:id', authenticate, async (req, res, next) => {
  try {
    const result = await query(
      `SELECT wu.*, u.name as submitted_by_name, d.name as division_name
       FROM weekly_updates wu
       JOIN users u ON wu.submitted_by = u.id
       JOIN divisions d ON wu.division_id = d.id
       WHERE wu.id = $1`,
      [req.params.id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Weekly update not found' });
    }

    // Division directors can only see their own
    const update = result.rows[0];
    if (req.user.role === 'division_director' && update.division_id !== req.user.division_id) {
      return res.status(403).json({ error: 'Access denied' });
    }

    res.json({ weeklyUpdate: update });
  } catch (err) {
    next(err);
  }
});

// POST /api/weekly-updates — Submit a weekly update (division_director)
router.post(
  '/',
  authenticate,
  authorize('division_director', 'admin'),
  [
    body('week_start').isDate(),
    body('week_end').isDate(),
    body('activity_description').trim().notEmpty(),
    body('responsible_persons').isArray(),
    body('status').isIn(['not_started', 'ongoing', 'completed', 'not_applicable']),
    body('deadline').optional({ nullable: true }).isDate(),
    body('challenges').optional().trim(),
    body('comments').optional().trim(),
  ],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const {
        week_start, week_end, activity_description,
        responsible_persons, status, deadline, challenges, comments,
        activity_id,
      } = req.body;

      const divisionId = req.user.division_id;
      if (!divisionId) {
        return res.status(400).json({ error: 'You must be assigned to a division' });
      }

      // If linked to an existing activity, update its status
      if (activity_id) {
        await query(
          `UPDATE activities SET status = $1, last_updated = NOW(),
           carry_forward_count = CASE WHEN $1 != 'completed' THEN carry_forward_count + 1 ELSE carry_forward_count END
           WHERE id = $2`,
          [status, activity_id]
        );
      }

      const result = await query(
        `INSERT INTO weekly_updates
         (division_id, submitted_by, position, week_start, week_end,
          activity_description, responsible_persons, status, deadline,
          challenges, comments, activity_id)
         VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)
         RETURNING *`,
        [
          divisionId, req.user.id, req.user.position,
          week_start, week_end, activity_description,
          JSON.stringify(responsible_persons), status,
          deadline || null, challenges || null, comments || null,
          activity_id || null,
        ]
      );

      res.status(201).json({ weeklyUpdate: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

// PUT /api/weekly-updates/:id
router.put(
  '/:id',
  authenticate,
  authorize('division_director', 'admin'),
  async (req, res, next) => {
    try {
      const {
        activity_description, responsible_persons, status,
        deadline, challenges, comments,
      } = req.body;

      // Verify ownership
      const existing = await query('SELECT * FROM weekly_updates WHERE id = $1', [req.params.id]);
      if (existing.rows.length === 0) return res.status(404).json({ error: 'Not found' });

      if (req.user.role === 'division_director' && existing.rows[0].submitted_by !== req.user.id) {
        return res.status(403).json({ error: 'Access denied' });
      }

      const result = await query(
        `UPDATE weekly_updates SET
           activity_description = COALESCE($1, activity_description),
           responsible_persons = COALESCE($2, responsible_persons),
           status = COALESCE($3, status),
           deadline = COALESCE($4, deadline),
           challenges = COALESCE($5, challenges),
           comments = COALESCE($6, comments)
         WHERE id = $7 RETURNING *`,
        [
          activity_description, responsible_persons ? JSON.stringify(responsible_persons) : null,
          status, deadline, challenges, comments, req.params.id,
        ]
      );

      // Sync status to linked activity
      if (result.rows[0].activity_id && status) {
        await query(
          'UPDATE activities SET status = $1, last_updated = NOW() WHERE id = $2',
          [status, result.rows[0].activity_id]
        );
      }

      res.json({ weeklyUpdate: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

module.exports = router;

const express = require('express');
const { body, validationResult } = require('express-validator');
const { query } = require('../config/db');
const authenticate = require('../middleware/auth');
const authorize = require('../middleware/rbac');

const router = express.Router();

// GET /api/activities
router.get('/', authenticate, async (req, res, next) => {
  try {
    const { division_id, status, is_overdue, is_repeated, assigned_user } = req.query;
    let sql = `
      SELECT a.*, d.name as division_name
      FROM activities a
      JOIN divisions d ON a.division_id = d.id
      WHERE 1=1
    `;
    const params = [];

    // Division directors see only their division
    if (req.user.role === 'division_director') {
      params.push(req.user.division_id);
      sql += ` AND a.division_id = $${params.length}`;
    } else if (division_id) {
      params.push(division_id);
      sql += ` AND a.division_id = $${params.length}`;
    }

    if (status) {
      params.push(status);
      sql += ` AND a.status = $${params.length}`;
    }
    if (is_overdue === 'true') {
      sql += ` AND a.is_overdue = TRUE`;
    }
    if (is_repeated === 'true') {
      sql += ` AND a.is_repeated = TRUE`;
    }
    if (assigned_user) {
      params.push(assigned_user);
      sql += ` AND a.assigned_users @> $${params.length}::jsonb`;
    }

    sql += ' ORDER BY a.deadline ASC NULLS LAST, a.created_at DESC';

    const result = await query(sql, params);
    res.json({ activities: result.rows });
  } catch (err) {
    next(err);
  }
});

// GET /api/activities/:id
router.get('/:id', authenticate, async (req, res, next) => {
  try {
    const result = await query(
      `SELECT a.*, d.name as division_name
       FROM activities a
       JOIN divisions d ON a.division_id = d.id
       WHERE a.id = $1`,
      [req.params.id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Activity not found' });
    }

    const activity = result.rows[0];

    // Fetch comments
    const commentsResult = await query(
      `SELECT ac.*, u.name as user_name, u.role as user_role
       FROM activity_comments ac
       JOIN users u ON ac.user_id = u.id
       WHERE ac.activity_id = $1
       ORDER BY ac.created_at ASC`,
      [req.params.id]
    );

    activity.comments = commentsResult.rows;

    res.json({ activity });
  } catch (err) {
    next(err);
  }
});

// PUT /api/activities/:id/status — Update activity status
router.put(
  '/:id/status',
  authenticate,
  authorize('division_director', 'admin'),
  [body('status').isIn(['not_started', 'ongoing', 'completed', 'not_applicable'])],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { status } = req.body;

      // Verify division director owns this activity
      if (req.user.role === 'division_director') {
        const check = await query(
          'SELECT division_id FROM activities WHERE id = $1',
          [req.params.id]
        );
        if (check.rows.length === 0) return res.status(404).json({ error: 'Not found' });
        if (check.rows[0].division_id !== req.user.division_id) {
          return res.status(403).json({ error: 'Access denied' });
        }
      }

      const isOverdue = status !== 'completed';

      const result = await query(
        `UPDATE activities SET
           status = $1,
           last_updated = NOW(),
           is_overdue = CASE
             WHEN $1 = 'completed' THEN FALSE
             WHEN deadline < CURRENT_DATE AND $1 != 'completed' THEN TRUE
             ELSE is_overdue
           END
         WHERE id = $2 RETURNING *`,
        [status, req.params.id]
      );

      if (result.rows.length === 0) return res.status(404).json({ error: 'Not found' });

      res.json({ activity: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

// POST /api/activities/:id/comments — Add a comment (bureau_head, admin)
router.post(
  '/:id/comments',
  authenticate,
  authorize('bureau_head', 'minister', 'admin', 'division_director'),
  [body('comment').trim().notEmpty()],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      // Verify activity exists
      const actCheck = await query('SELECT id FROM activities WHERE id = $1', [req.params.id]);
      if (actCheck.rows.length === 0) return res.status(404).json({ error: 'Activity not found' });

      const result = await query(
        `INSERT INTO activity_comments (activity_id, user_id, comment)
         VALUES ($1, $2, $3) RETURNING *`,
        [req.params.id, req.user.id, req.body.comment]
      );

      // Fetch with user name
      const full = await query(
        `SELECT ac.*, u.name as user_name, u.role as user_role
         FROM activity_comments ac
         JOIN users u ON ac.user_id = u.id
         WHERE ac.id = $1`,
        [result.rows[0].id]
      );

      res.status(201).json({ comment: full.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

// GET /api/activities/:id/comments
router.get('/:id/comments', authenticate, async (req, res, next) => {
  try {
    const result = await query(
      `SELECT ac.*, u.name as user_name, u.role as user_role
       FROM activity_comments ac
       JOIN users u ON ac.user_id = u.id
       WHERE ac.activity_id = $1
       ORDER BY ac.created_at ASC`,
      [req.params.id]
    );
    res.json({ comments: result.rows });
  } catch (err) {
    next(err);
  }
});

module.exports = router;

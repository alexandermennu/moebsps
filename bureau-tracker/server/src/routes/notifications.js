const express = require('express');
const { query } = require('../config/db');
const authenticate = require('../middleware/auth');

const router = express.Router();

// GET /api/notifications — Get user's notifications
router.get('/', authenticate, async (req, res, next) => {
  try {
    const { unread_only } = req.query;
    let sql = `
      SELECT * FROM notifications
      WHERE user_id = $1
    `;
    const params = [req.user.id];

    if (unread_only === 'true') {
      sql += ' AND is_read = FALSE';
    }

    sql += ' ORDER BY created_at DESC LIMIT 50';

    const result = await query(sql, params);

    // Get unread count
    const countResult = await query(
      'SELECT COUNT(*) as count FROM notifications WHERE user_id = $1 AND is_read = FALSE',
      [req.user.id]
    );

    res.json({
      notifications: result.rows,
      unreadCount: parseInt(countResult.rows[0].count),
    });
  } catch (err) {
    next(err);
  }
});

// PUT /api/notifications/:id/read — Mark notification as read
router.put('/:id/read', authenticate, async (req, res, next) => {
  try {
    const result = await query(
      'UPDATE notifications SET is_read = TRUE WHERE id = $1 AND user_id = $2 RETURNING *',
      [req.params.id, req.user.id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Notification not found' });
    }

    res.json({ notification: result.rows[0] });
  } catch (err) {
    next(err);
  }
});

// PUT /api/notifications/read-all — Mark all as read
router.put('/read-all', authenticate, async (req, res, next) => {
  try {
    await query(
      'UPDATE notifications SET is_read = TRUE WHERE user_id = $1 AND is_read = FALSE',
      [req.user.id]
    );

    res.json({ message: 'All notifications marked as read' });
  } catch (err) {
    next(err);
  }
});

module.exports = router;

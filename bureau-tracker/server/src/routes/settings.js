const express = require('express');
const { body, validationResult } = require('express-validator');
const { query } = require('../config/db');
const authenticate = require('../middleware/auth');
const authorize = require('../middleware/rbac');

const router = express.Router();

// GET /api/settings — Get all system settings
router.get('/', authenticate, authorize('admin'), async (_req, res, next) => {
  try {
    const result = await query('SELECT * FROM system_settings ORDER BY key');
    res.json({ settings: result.rows });
  } catch (err) {
    next(err);
  }
});

// PUT /api/settings — Update settings (admin only)
router.put(
  '/',
  authenticate,
  authorize('admin'),
  [body('settings').isArray()],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { settings } = req.body;

      for (const setting of settings) {
        await query(
          `UPDATE system_settings SET value = $1, updated_at = NOW() WHERE key = $2`,
          [String(setting.value), setting.key]
        );
      }

      const result = await query('SELECT * FROM system_settings ORDER BY key');
      res.json({ settings: result.rows, message: 'Settings updated successfully' });
    } catch (err) {
      next(err);
    }
  }
);

// GET /api/settings/:key — Get a single setting
router.get('/:key', authenticate, async (req, res, next) => {
  try {
    const result = await query(
      'SELECT * FROM system_settings WHERE key = $1',
      [req.params.key]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Setting not found' });
    }

    res.json({ setting: result.rows[0] });
  } catch (err) {
    next(err);
  }
});

module.exports = router;

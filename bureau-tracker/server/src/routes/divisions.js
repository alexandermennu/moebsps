const express = require('express');
const { body, validationResult } = require('express-validator');
const { query } = require('../config/db');
const authenticate = require('../middleware/auth');
const authorize = require('../middleware/rbac');

const router = express.Router();

// GET /api/divisions
router.get('/', authenticate, async (_req, res, next) => {
  try {
    const result = await query('SELECT * FROM divisions ORDER BY name');
    res.json({ divisions: result.rows });
  } catch (err) {
    next(err);
  }
});

// GET /api/divisions/:id
router.get('/:id', authenticate, async (req, res, next) => {
  try {
    const result = await query('SELECT * FROM divisions WHERE id = $1', [req.params.id]);
    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Division not found' });
    }
    res.json({ division: result.rows[0] });
  } catch (err) {
    next(err);
  }
});

// POST /api/divisions (admin only)
router.post(
  '/',
  authenticate,
  authorize('admin'),
  [
    body('name').trim().notEmpty(),
    body('description').optional().trim(),
  ],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { name, description } = req.body;
      const result = await query(
        'INSERT INTO divisions (name, description) VALUES ($1, $2) RETURNING *',
        [name, description || null]
      );

      res.status(201).json({ division: result.rows[0] });
    } catch (err) {
      if (err.code === '23505') {
        return res.status(409).json({ error: 'Division name already exists' });
      }
      next(err);
    }
  }
);

// PUT /api/divisions/:id (admin only)
router.put(
  '/:id',
  authenticate,
  authorize('admin'),
  [
    body('name').optional().trim().notEmpty(),
    body('description').optional().trim(),
  ],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { name, description } = req.body;
      const result = await query(
        `UPDATE divisions SET
           name = COALESCE($1, name),
           description = COALESCE($2, description),
           updated_at = NOW()
         WHERE id = $3 RETURNING *`,
        [name, description, req.params.id]
      );

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'Division not found' });
      }

      res.json({ division: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

// DELETE /api/divisions/:id (admin only)
router.delete(
  '/:id',
  authenticate,
  authorize('admin'),
  async (req, res, next) => {
    try {
      const result = await query(
        'DELETE FROM divisions WHERE id = $1 RETURNING id, name',
        [req.params.id]
      );

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'Division not found' });
      }

      res.json({ message: 'Division deleted', division: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

module.exports = router;

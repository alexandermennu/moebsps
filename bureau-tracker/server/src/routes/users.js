const express = require('express');
const bcrypt = require('bcryptjs');
const { body, validationResult } = require('express-validator');
const { query } = require('../config/db');
const authenticate = require('../middleware/auth');
const authorize = require('../middleware/rbac');

const router = express.Router();

// GET /api/users — List all users (admin, bureau_head, minister)
router.get(
  '/',
  authenticate,
  authorize('admin', 'bureau_head', 'minister'),
  async (req, res, next) => {
    try {
      const { role, division_id, is_active } = req.query;
      let sql = `
        SELECT u.id, u.name, u.email, u.role, u.division_id, u.position,
               u.is_active, u.created_at, d.name as division_name
        FROM users u
        LEFT JOIN divisions d ON u.division_id = d.id
        WHERE 1=1
      `;
      const params = [];

      if (role) {
        params.push(role);
        sql += ` AND u.role = $${params.length}`;
      }
      if (division_id) {
        params.push(division_id);
        sql += ` AND u.division_id = $${params.length}`;
      }
      if (is_active !== undefined) {
        params.push(is_active === 'true');
        sql += ` AND u.is_active = $${params.length}`;
      }

      sql += ' ORDER BY u.name';
      const result = await query(sql, params);
      res.json({ users: result.rows });
    } catch (err) {
      next(err);
    }
  }
);

// GET /api/users/:id
router.get('/:id', authenticate, async (req, res, next) => {
  try {
    const result = await query(
      `SELECT u.id, u.name, u.email, u.role, u.division_id, u.position,
              u.is_active, u.created_at, d.name as division_name
       FROM users u
       LEFT JOIN divisions d ON u.division_id = d.id
       WHERE u.id = $1`,
      [req.params.id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'User not found' });
    }

    res.json({ user: result.rows[0] });
  } catch (err) {
    next(err);
  }
});

// POST /api/users — Create user (admin only)
router.post(
  '/',
  authenticate,
  authorize('admin'),
  [
    body('name').trim().notEmpty(),
    body('email').isEmail().normalizeEmail(),
    body('password').isLength({ min: 8 }),
    body('role').isIn(['division_director', 'bureau_head', 'minister', 'admin']),
    body('position').optional().trim(),
    body('division_id').optional({ nullable: true }).isUUID(),
  ],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { name, email, password, role, division_id, position } = req.body;

      // Check duplicate email
      const existing = await query('SELECT id FROM users WHERE email = $1', [email]);
      if (existing.rows.length > 0) {
        return res.status(409).json({ error: 'Email already registered' });
      }

      const passwordHash = await bcrypt.hash(password, 12);

      const result = await query(
        `INSERT INTO users (name, email, password_hash, role, division_id, position)
         VALUES ($1, $2, $3, $4, $5, $6)
         RETURNING id, name, email, role, division_id, position, is_active, created_at`,
        [name, email, passwordHash, role, division_id || null, position || null]
      );

      res.status(201).json({ user: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

// PUT /api/users/:id — Update user (admin only)
router.put(
  '/:id',
  authenticate,
  authorize('admin'),
  [
    body('name').optional().trim().notEmpty(),
    body('email').optional().isEmail().normalizeEmail(),
    body('role').optional().isIn(['division_director', 'bureau_head', 'minister', 'admin']),
    body('position').optional().trim(),
    body('division_id').optional({ nullable: true }),
    body('is_active').optional().isBoolean(),
  ],
  async (req, res, next) => {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { name, email, role, division_id, position, is_active, password } = req.body;

      const fields = [];
      const params = [];
      let idx = 1;

      if (name !== undefined) { fields.push(`name = $${idx++}`); params.push(name); }
      if (email !== undefined) { fields.push(`email = $${idx++}`); params.push(email); }
      if (role !== undefined) { fields.push(`role = $${idx++}`); params.push(role); }
      if (division_id !== undefined) { fields.push(`division_id = $${idx++}`); params.push(division_id || null); }
      if (position !== undefined) { fields.push(`position = $${idx++}`); params.push(position); }
      if (is_active !== undefined) { fields.push(`is_active = $${idx++}`); params.push(is_active); }
      if (password) {
        const hash = await bcrypt.hash(password, 12);
        fields.push(`password_hash = $${idx++}`);
        params.push(hash);
      }

      if (fields.length === 0) {
        return res.status(400).json({ error: 'No fields to update' });
      }

      fields.push('updated_at = NOW()');
      params.push(req.params.id);

      const result = await query(
        `UPDATE users SET ${fields.join(', ')} WHERE id = $${idx}
         RETURNING id, name, email, role, division_id, position, is_active, created_at`,
        params
      );

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'User not found' });
      }

      res.json({ user: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

// DELETE /api/users/:id — Deactivate user (admin only)
router.delete(
  '/:id',
  authenticate,
  authorize('admin'),
  async (req, res, next) => {
    try {
      const result = await query(
        `UPDATE users SET is_active = FALSE, updated_at = NOW() WHERE id = $1
         RETURNING id, name, email`,
        [req.params.id]
      );

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'User not found' });
      }

      res.json({ message: 'User deactivated', user: result.rows[0] });
    } catch (err) {
      next(err);
    }
  }
);

module.exports = router;

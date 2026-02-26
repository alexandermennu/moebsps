const express = require('express');
const { query } = require('../config/db');
const authenticate = require('../middleware/auth');
const authorize = require('../middleware/rbac');

const router = express.Router();

// GET /api/dashboard/director — Division Director dashboard
router.get(
  '/director',
  authenticate,
  authorize('division_director', 'admin'),
  async (req, res, next) => {
    try {
      const divId = req.user.division_id;

      const [active, overdue, completionRate, recentUpdates] = await Promise.all([
        // Active activities
        query(
          `SELECT * FROM activities
           WHERE division_id = $1 AND status IN ('not_started', 'ongoing')
           ORDER BY deadline ASC NULLS LAST`,
          [divId]
        ),
        // Overdue activities
        query(
          `SELECT * FROM activities
           WHERE division_id = $1 AND is_overdue = TRUE AND status != 'completed'
           ORDER BY deadline ASC`,
          [divId]
        ),
        // Completion rate
        query(
          `SELECT
             COUNT(*) FILTER (WHERE status = 'completed') as completed,
             COUNT(*) as total
           FROM activities
           WHERE division_id = $1`,
          [divId]
        ),
        // Recent weekly updates
        query(
          `SELECT wu.*, d.name as division_name
           FROM weekly_updates wu
           JOIN divisions d ON wu.division_id = d.id
           WHERE wu.division_id = $1
           ORDER BY wu.created_at DESC LIMIT 10`,
          [divId]
        ),
      ]);

      const rate = completionRate.rows[0];
      const pct = rate.total > 0
        ? Math.round((parseInt(rate.completed) / parseInt(rate.total)) * 100)
        : 0;

      res.json({
        activeActivities: active.rows,
        overdueActivities: overdue.rows,
        completionRate: pct,
        totalActivities: parseInt(rate.total),
        completedActivities: parseInt(rate.completed),
        recentUpdates: recentUpdates.rows,
      });
    } catch (err) {
      next(err);
    }
  }
);

// GET /api/dashboard/bureau-head — Bureau Head dashboard
router.get(
  '/bureau-head',
  authenticate,
  authorize('bureau_head', 'admin'),
  async (req, res, next) => {
    try {
      const [
        allActive,
        overdueByDivision,
        overdueByIndividual,
        pendingUpdates,
        divisionSummary,
      ] = await Promise.all([
        // All active activities
        query(
          `SELECT a.*, d.name as division_name
           FROM activities a
           JOIN divisions d ON a.division_id = d.id
           WHERE a.status IN ('not_started', 'ongoing')
           ORDER BY a.deadline ASC NULLS LAST`
        ),
        // Overdue by division
        query(
          `SELECT d.name as division_name, d.id as division_id,
                  COUNT(*) as overdue_count
           FROM activities a
           JOIN divisions d ON a.division_id = d.id
           WHERE a.is_overdue = TRUE AND a.status != 'completed'
           GROUP BY d.id, d.name
           ORDER BY overdue_count DESC`
        ),
        // Overdue by individual (from assigned_users JSONB)
        query(
          `SELECT
             jsonb_array_elements_text(a.assigned_users) as person,
             COUNT(*) as overdue_count
           FROM activities a
           WHERE a.is_overdue = TRUE AND a.status != 'completed'
             AND jsonb_array_length(a.assigned_users) > 0
           GROUP BY person
           ORDER BY overdue_count DESC`
        ),
        // Divisions without updates this week
        query(
          `SELECT d.id, d.name
           FROM divisions d
           WHERE d.id NOT IN (
             SELECT DISTINCT division_id FROM weekly_updates
             WHERE week_start >= date_trunc('week', CURRENT_DATE)
           )
           ORDER BY d.name`
        ),
        // Division summary
        query(
          `SELECT d.name as division_name, d.id as division_id,
                  COUNT(*) FILTER (WHERE a.status IN ('not_started', 'ongoing')) as active,
                  COUNT(*) FILTER (WHERE a.status = 'completed') as completed,
                  COUNT(*) FILTER (WHERE a.is_overdue = TRUE AND a.status != 'completed') as overdue,
                  COUNT(*) as total
           FROM divisions d
           LEFT JOIN activities a ON a.division_id = d.id
           GROUP BY d.id, d.name
           ORDER BY d.name`
        ),
      ]);

      res.json({
        allActiveActivities: allActive.rows,
        overdueByDivision: overdueByDivision.rows,
        overdueByIndividual: overdueByIndividual.rows,
        pendingUpdates: pendingUpdates.rows,
        divisionSummary: divisionSummary.rows,
      });
    } catch (err) {
      next(err);
    }
  }
);

// GET /api/dashboard/minister — Minister dashboard
router.get(
  '/minister',
  authenticate,
  authorize('minister', 'admin'),
  async (req, res, next) => {
    try {
      const [
        summary,
        overdue,
        repeated,
        divisionPerformance,
        byPerson,
      ] = await Promise.all([
        // Summary counts
        query(
          `SELECT
             COUNT(*) FILTER (WHERE status IN ('not_started', 'ongoing')) as total_active,
             COUNT(*) FILTER (WHERE is_overdue = TRUE AND status != 'completed') as total_overdue,
             COUNT(*) FILTER (WHERE is_repeated = TRUE) as total_repeated,
             COUNT(*) FILTER (WHERE status = 'completed') as total_completed,
             COUNT(*) as grand_total
           FROM activities`
        ),
        // Overdue activities
        query(
          `SELECT a.*, d.name as division_name
           FROM activities a
           JOIN divisions d ON a.division_id = d.id
           WHERE a.is_overdue = TRUE AND a.status != 'completed'
           ORDER BY a.deadline ASC`
        ),
        // Repeated activities
        query(
          `SELECT a.*, d.name as division_name
           FROM activities a
           JOIN divisions d ON a.division_id = d.id
           WHERE a.is_repeated = TRUE
           ORDER BY a.carry_forward_count DESC`
        ),
        // Division performance
        query(
          `SELECT d.name as division_name, d.id as division_id,
                  COUNT(*) as total,
                  COUNT(*) FILTER (WHERE a.status = 'completed') as completed,
                  COUNT(*) FILTER (WHERE a.is_overdue = TRUE AND a.status != 'completed') as overdue,
                  ROUND(
                    COUNT(*) FILTER (WHERE a.status = 'completed')::numeric /
                    NULLIF(COUNT(*), 0) * 100
                  ) as completion_pct
           FROM divisions d
           LEFT JOIN activities a ON a.division_id = d.id
           GROUP BY d.id, d.name
           ORDER BY completion_pct DESC NULLS LAST`
        ),
        // By responsible person
        query(
          `SELECT
             jsonb_array_elements_text(a.assigned_users) as person,
             COUNT(*) as total,
             COUNT(*) FILTER (WHERE a.status = 'completed') as completed,
             COUNT(*) FILTER (WHERE a.is_overdue = TRUE AND a.status != 'completed') as overdue
           FROM activities a
           WHERE jsonb_array_length(a.assigned_users) > 0
           GROUP BY person
           ORDER BY overdue DESC, total DESC`
        ),
      ]);

      res.json({
        summary: summary.rows[0],
        overdueActivities: overdue.rows,
        repeatedActivities: repeated.rows,
        divisionPerformance: divisionPerformance.rows,
        byResponsiblePerson: byPerson.rows,
      });
    } catch (err) {
      next(err);
    }
  }
);

module.exports = router;

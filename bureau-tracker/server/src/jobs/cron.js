const cron = require('node-cron');
const { query } = require('../config/db');
const {
  sendDeadlineWarning,
  sendOverdueNotification,
  sendEscalationNotification,
} = require('../utils/email');

/**
 * Fetch a system setting value.
 */
async function getSetting(key, defaultValue) {
  const result = await query('SELECT value FROM system_settings WHERE key = $1', [key]);
  return result.rows.length > 0 ? result.rows[0].value : defaultValue;
}

/**
 * Create an in-app notification.
 */
async function createNotification(userId, title, message, type = 'general') {
  await query(
    'INSERT INTO notifications (user_id, title, message, type) VALUES ($1, $2, $3, $4)',
    [userId, title, message, type]
  );
}

/**
 * Job 1: Mark overdue activities and send notifications.
 */
async function processOverdueActivities() {
  console.log('[CRON] Processing overdue activities...');

  try {
    const overdueThreshold = parseInt(await getSetting('overdue_threshold_days', '1'));
    const escalationThreshold = parseInt(await getSetting('escalation_threshold_days', '7'));
    const escalationEnabled = (await getSetting('escalation_enabled', 'true')) === 'true';
    const deadlineWarningDays = parseInt(await getSetting('deadline_warning_days', '3'));

    // 1. Mark overdue activities
    await query(
      `UPDATE activities
       SET is_overdue = TRUE, last_updated = NOW()
       WHERE deadline < CURRENT_DATE
         AND status NOT IN ('completed', 'not_applicable')
         AND is_overdue = FALSE`
    );

    // 2. Send deadline approaching warnings (activities due within warning days)
    const approachingResult = await query(
      `SELECT a.*, d.name as division_name
       FROM activities a
       JOIN divisions d ON a.division_id = d.id
       WHERE a.deadline BETWEEN CURRENT_DATE AND CURRENT_DATE + $1 * INTERVAL '1 day'
         AND a.status NOT IN ('completed', 'not_applicable')`,
      [deadlineWarningDays]
    );

    for (const activity of approachingResult.rows) {
      const assignedUsers = activity.assigned_users || [];
      for (const personName of assignedUsers) {
        // Find user by name for email
        const userResult = await query(
          'SELECT id, email FROM users WHERE name = $1 AND is_active = TRUE',
          [personName]
        );
        for (const user of userResult.rows) {
          await createNotification(
            user.id,
            'Deadline Approaching',
            `Activity "${activity.title}" is due on ${new Date(activity.deadline).toLocaleDateString()}`,
            'deadline_approaching'
          );
          await sendDeadlineWarning(user.email, activity.title, activity.deadline);
        }
      }
    }

    // 3. Send overdue notifications
    const overdueResult = await query(
      `SELECT a.*, d.name as division_name,
              (CURRENT_DATE - a.deadline) as days_overdue
       FROM activities a
       JOIN divisions d ON a.division_id = d.id
       WHERE a.is_overdue = TRUE
         AND a.status NOT IN ('completed', 'not_applicable')
         AND a.overdue_notified = FALSE
         AND (CURRENT_DATE - a.deadline) >= $1`,
      [overdueThreshold]
    );

    for (const activity of overdueResult.rows) {
      const assignedUsers = activity.assigned_users || [];
      for (const personName of assignedUsers) {
        const userResult = await query(
          'SELECT id, email FROM users WHERE name = $1 AND is_active = TRUE',
          [personName]
        );
        for (const user of userResult.rows) {
          await createNotification(
            user.id,
            'Activity Overdue',
            `Activity "${activity.title}" is ${activity.days_overdue} day(s) overdue`,
            'overdue'
          );
          await sendOverdueNotification(
            user.email, activity.title, activity.deadline, activity.days_overdue
          );
        }
      }

      await query(
        'UPDATE activities SET overdue_notified = TRUE WHERE id = $1',
        [activity.id]
      );
    }

    // 4. Escalation notifications
    if (escalationEnabled) {
      const escalationResult = await query(
        `SELECT a.*, d.name as division_name,
                (CURRENT_DATE - a.deadline) as days_overdue
         FROM activities a
         JOIN divisions d ON a.division_id = d.id
         WHERE a.is_overdue = TRUE
           AND a.status NOT IN ('completed', 'not_applicable')
           AND a.escalation_notified = FALSE
           AND (CURRENT_DATE - a.deadline) >= $1`,
        [escalationThreshold]
      );

      // Get bureau heads and ministers for escalation
      const leaders = await query(
        "SELECT id, email, role FROM users WHERE role IN ('bureau_head', 'minister') AND is_active = TRUE"
      );

      for (const activity of escalationResult.rows) {
        for (const leader of leaders.rows) {
          await createNotification(
            leader.id,
            'Escalation Alert',
            `Activity "${activity.title}" (${activity.division_name}) is ${activity.days_overdue} day(s) overdue`,
            'escalation'
          );
          await sendEscalationNotification(
            leader.email, activity.title, activity.division_name, activity.days_overdue
          );
        }

        await query(
          'UPDATE activities SET escalation_notified = TRUE WHERE id = $1',
          [activity.id]
        );
      }
    }

    console.log('[CRON] Overdue processing complete.');
  } catch (err) {
    console.error('[CRON] Error processing overdue activities:', err);
  }
}

/**
 * Job 2: Detect repeated activities (ongoing for 3+ consecutive weeks).
 */
async function detectRepeatedActivities() {
  console.log('[CRON] Detecting repeated activities...');

  try {
    // Activities with carry_forward_count >= 3 and still ongoing
    await query(
      `UPDATE activities
       SET is_repeated = TRUE, last_updated = NOW()
       WHERE carry_forward_count >= 3
         AND status = 'ongoing'
         AND is_repeated = FALSE`
    );

    // Also detect by checking weekly updates
    const repeatedResult = await query(
      `SELECT activity_id, COUNT(DISTINCT week_start) as week_count
       FROM weekly_updates
       WHERE status = 'ongoing'
         AND activity_id IS NOT NULL
       GROUP BY activity_id
       HAVING COUNT(DISTINCT week_start) >= 3`
    );

    for (const row of repeatedResult.rows) {
      await query(
        `UPDATE activities
         SET is_repeated = TRUE, carry_forward_count = GREATEST(carry_forward_count, $1), last_updated = NOW()
         WHERE id = $2 AND is_repeated = FALSE`,
        [row.week_count, row.activity_id]
      );
    }

    console.log('[CRON] Repeated activity detection complete.');
  } catch (err) {
    console.error('[CRON] Error detecting repeated activities:', err);
  }
}

/**
 * Start all cron jobs.
 */
function startCronJobs() {
  // Run overdue check every hour
  cron.schedule('0 * * * *', () => {
    processOverdueActivities();
  });

  // Run repeated activity detection every 6 hours
  cron.schedule('0 */6 * * *', () => {
    detectRepeatedActivities();
  });

  // Run both once on startup after a brief delay
  setTimeout(() => {
    processOverdueActivities();
    detectRepeatedActivities();
  }, 5000);

  console.log('[CRON] Background jobs scheduled.');
}

module.exports = { startCronJobs, processOverdueActivities, detectRepeatedActivities };

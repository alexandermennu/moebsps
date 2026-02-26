const nodemailer = require('nodemailer');

let transporter = null;

function getTransporter() {
  if (!transporter) {
    transporter = nodemailer.createTransport({
      host: process.env.SMTP_HOST,
      port: parseInt(process.env.SMTP_PORT || '587'),
      secure: process.env.SMTP_PORT === '465',
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
      },
    });
  }
  return transporter;
}

/**
 * Send an email notification.
 * @param {string} to - Recipient email
 * @param {string} subject - Email subject
 * @param {string} html - HTML body
 */
async function sendEmail(to, subject, html) {
  try {
    const transport = getTransporter();
    await transport.sendMail({
      from: process.env.SMTP_FROM || 'Bureau Tracker <noreply@bureau.gov>',
      to,
      subject,
      html,
    });
    console.log(`Email sent to ${to}: ${subject}`);
  } catch (err) {
    console.error(`Failed to send email to ${to}:`, err.message);
  }
}

/**
 * Send deadline approaching email
 */
async function sendDeadlineWarning(email, activityTitle, deadline) {
  const subject = `⚠️ Deadline Approaching: ${activityTitle}`;
  const html = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
      <div style="background: #f59e0b; color: white; padding: 20px; border-radius: 8px 8px 0 0;">
        <h2 style="margin: 0;">Deadline Approaching</h2>
      </div>
      <div style="padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <p>The following activity deadline is approaching:</p>
        <p><strong>Activity:</strong> ${activityTitle}</p>
        <p><strong>Deadline:</strong> ${new Date(deadline).toLocaleDateString()}</p>
        <p>Please take necessary action to complete this activity before the deadline.</p>
      </div>
    </div>
  `;
  await sendEmail(email, subject, html);
}

/**
 * Send overdue notification email
 */
async function sendOverdueNotification(email, activityTitle, deadline, daysPastDue) {
  const subject = `🔴 Overdue Activity: ${activityTitle}`;
  const html = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
      <div style="background: #ef4444; color: white; padding: 20px; border-radius: 8px 8px 0 0;">
        <h2 style="margin: 0;">Activity Overdue</h2>
      </div>
      <div style="padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <p>The following activity is <strong>${daysPastDue} day(s)</strong> overdue:</p>
        <p><strong>Activity:</strong> ${activityTitle}</p>
        <p><strong>Deadline:</strong> ${new Date(deadline).toLocaleDateString()}</p>
        <p>Please update the status or take immediate action.</p>
      </div>
    </div>
  `;
  await sendEmail(email, subject, html);
}

/**
 * Send escalation notification email
 */
async function sendEscalationNotification(email, activityTitle, divisionName, daysPastDue) {
  const subject = `🚨 Escalation: ${activityTitle} (${daysPastDue} days overdue)`;
  const html = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
      <div style="background: #7c3aed; color: white; padding: 20px; border-radius: 8px 8px 0 0;">
        <h2 style="margin: 0;">Activity Escalation</h2>
      </div>
      <div style="padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <p>An activity has exceeded the escalation threshold:</p>
        <p><strong>Activity:</strong> ${activityTitle}</p>
        <p><strong>Division:</strong> ${divisionName}</p>
        <p><strong>Days Overdue:</strong> ${daysPastDue}</p>
        <p>This requires immediate management attention.</p>
      </div>
    </div>
  `;
  await sendEmail(email, subject, html);
}

module.exports = {
  sendEmail,
  sendDeadlineWarning,
  sendOverdueNotification,
  sendEscalationNotification,
};

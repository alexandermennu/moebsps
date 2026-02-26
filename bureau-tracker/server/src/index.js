require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');

const authRoutes = require('./routes/auth');
const userRoutes = require('./routes/users');
const divisionRoutes = require('./routes/divisions');
const weeklyUpdateRoutes = require('./routes/weeklyUpdates');
const weeklyPlanRoutes = require('./routes/weeklyPlans');
const activityRoutes = require('./routes/activities');
const notificationRoutes = require('./routes/notifications');
const settingsRoutes = require('./routes/settings');
const dashboardRoutes = require('./routes/dashboard');
const { startCronJobs } = require('./jobs/cron');

const app = express();
const PORT = process.env.PORT || 5000;

// --------------- Middleware ---------------
app.use(helmet());
app.use(cors({
  origin: process.env.CLIENT_URL || 'http://localhost:5173',
  credentials: true,
}));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// --------------- Health Check ---------------
app.get('/api/health', (_req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

// --------------- Routes ---------------
app.use('/api/auth', authRoutes);
app.use('/api/users', userRoutes);
app.use('/api/divisions', divisionRoutes);
app.use('/api/weekly-updates', weeklyUpdateRoutes);
app.use('/api/weekly-plans', weeklyPlanRoutes);
app.use('/api/activities', activityRoutes);
app.use('/api/notifications', notificationRoutes);
app.use('/api/settings', settingsRoutes);
app.use('/api/dashboard', dashboardRoutes);

// --------------- Error Handler ---------------
app.use((err, _req, res, _next) => {
  console.error('Unhandled error:', err);
  res.status(500).json({
    error: process.env.NODE_ENV === 'production'
      ? 'Internal server error'
      : err.message,
  });
});

// --------------- Start Server ---------------
app.listen(PORT, () => {
  console.log(`Bureau Tracker API running on port ${PORT}`);
  startCronJobs();
});

module.exports = app;

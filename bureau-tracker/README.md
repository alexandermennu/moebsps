# Bureau Activity Tracking System

A secure, internal web-based system for tracking weekly updates, weekly plans, and activities across bureau divisions. Features deadline monitoring, overdue alerts, escalation notifications, and role-based dashboards.

---

## Tech Stack

| Layer          | Technology                          |
|----------------|-------------------------------------|
| Frontend       | React 18, Vite, Tailwind CSS, Recharts |
| Backend        | Node.js, Express                    |
| Database       | PostgreSQL                          |
| Authentication | JWT-based role authentication       |
| Background Jobs| node-cron                           |
| Email          | Nodemailer (SMTP)                   |

---

## Prerequisites

- **Node.js** ≥ 18
- **PostgreSQL** ≥ 14
- **npm** ≥ 9

---

## Quick Start

### 1. Clone & Install

```bash
cd bureau-tracker

# Install server dependencies
cd server
cp .env.example .env    # Edit .env with your database and SMTP credentials
npm install

# Install client dependencies
cd ../client
npm install
```

### 2. Setup Database

Create the PostgreSQL database:

```bash
createdb bureau_tracker
```

Run migrations and seed data:

```bash
cd server
npm run migrate
```

### 3. Start Development Servers

**Backend** (runs on port 5000):
```bash
cd server
npm run dev
```

**Frontend** (runs on port 5173, proxies API to 5000):
```bash
cd client
npm run dev
```

Open **http://localhost:5173** in your browser.

---

## Default Login Credentials

| Role              | Email             | Password     |
|-------------------|-------------------|--------------|
| System Admin      | admin@bureau.gov  | Admin@123    |
| Division Director | jane@bureau.gov   | Admin@123    |
| Bureau Head       | bob@bureau.gov    | Admin@123    |
| Minister          | mary@bureau.gov   | Admin@123    |

> ⚠️ **Change all default passwords after first login!**

---

## System Roles & Permissions

### Division Director
- Submit Weekly Updates and Weekly Plans
- View only their own division's activities
- Update activity status

### Bureau Head
- View all division activities
- Filter by division, status, deadline
- View overdue list
- Comment on activities

### Minister
- View executive summary dashboard
- See overdue and repeated activities
- View by division and responsible person

### System Admin
- Manage users (create, edit, deactivate)
- Configure system settings
- Full access to all features

---

## Core Features

### Weekly Updates
Submit weekly status reports with:
- Reporting week range
- Activity description
- Responsible persons (multi-select)
- Status (Not Started / Ongoing / Completed / N/A)
- Deadline, challenges, and comments
- Optional link to an existing tracked activity

### Weekly Plans
Create forward-looking weekly plans that **automatically generate trackable activity records** in the Activities table.

### Activity Tracking
- Full activity lifecycle management
- Status updates with audit trail
- Comments from Bureau Head and Minister
- Carry-forward counting for repeated activities

### Overdue Logic
- Activities past deadline with status ≠ Completed are marked **Overdue**
- Configurable overdue threshold triggers email/in-app notifications
- Configurable escalation threshold notifies Bureau Head and Minister

### Repeated Activity Detection
- Activities remaining "Ongoing" across **3+ consecutive weeks** are flagged as **Repeated**
- Displayed prominently on the Minister dashboard

### Notification System
- **In-app notification panel** with unread count badge
- **Email alerts** for:
  - Deadline approaching (configurable days before)
  - Activity overdue
  - Escalation triggered

### Role-Based Dashboards
- **Director**: Active/overdue activities, completion rate
- **Bureau Head**: Cross-division overview, overdue by division/person, performance chart
- **Minister**: Executive summary, pie/bar charts, repeated activities, responsible person breakdown

---

## Configuration (Admin Settings)

| Setting                 | Default | Description                                    |
|-------------------------|---------|------------------------------------------------|
| Overdue Threshold       | 1 day   | Days past deadline to trigger overdue alert     |
| Escalation Threshold    | 7 days  | Days past deadline to escalate to leadership    |
| Reminder Frequency      | 24 hrs  | How often to re-send overdue reminders          |
| Escalation Enabled      | true    | Toggle escalation notifications on/off          |
| Deadline Warning         | 3 days  | Days before deadline to send warning            |

---

## Project Structure

```
bureau-tracker/
├── server/
│   ├── package.json
│   ├── .env.example
│   ├── migrations/
│   │   ├── 001_schema.sql       # Full database schema
│   │   └── 002_seed.sql         # Seed data
│   └── src/
│       ├── index.js             # Express server entry
│       ├── config/
│       │   ├── db.js            # PostgreSQL pool
│       │   └── migrate.js       # Migration runner
│       ├── middleware/
│       │   ├── auth.js          # JWT authentication
│       │   └── rbac.js          # Role-based access control
│       ├── routes/
│       │   ├── auth.js          # Login, me, change password
│       │   ├── users.js         # User CRUD
│       │   ├── divisions.js     # Division CRUD
│       │   ├── weeklyUpdates.js # Weekly update submissions
│       │   ├── weeklyPlans.js   # Weekly plans (auto-creates activities)
│       │   ├── activities.js    # Activity management & comments
│       │   ├── dashboard.js     # Role-based dashboard data
│       │   ├── notifications.js # In-app notifications
│       │   └── settings.js      # System settings
│       ├── jobs/
│       │   └── cron.js          # Overdue detection & reminder jobs
│       └── utils/
│           └── email.js         # Email templates & sending
│
└── client/
    ├── package.json
    ├── index.html
    ├── vite.config.js
    ├── tailwind.config.js
    └── src/
        ├── main.jsx
        ├── App.jsx              # Route definitions
        ├── index.css            # Tailwind + custom components
        ├── api/
        │   └── client.js        # API client with auth
        ├── context/
        │   └── AuthContext.jsx   # Auth state management
        ├── components/
        │   ├── Layout.jsx       # Dashboard shell
        │   ├── Sidebar.jsx      # Role-based navigation
        │   ├── Navbar.jsx       # Top bar with notifications
        │   ├── ProtectedRoute.jsx
        │   └── StatCard.jsx     # Dashboard stat cards
        ├── pages/
        │   ├── Login.jsx
        │   ├── Dashboard.jsx    # Role-based router
        │   ├── WeeklyUpdates.jsx
        │   ├── WeeklyPlans.jsx
        │   ├── Activities.jsx   # With filters, detail panel, comments
        │   ├── AdminUsers.jsx
        │   └── AdminSettings.jsx
        │   └── dashboard/
        │       ├── DirectorDashboard.jsx
        │       ├── BureauHeadDashboard.jsx
        │       └── MinisterDashboard.jsx
        └── utils/
            └── helpers.jsx      # Status badges, date formatting
```

---

## API Endpoints

| Method | Endpoint                          | Auth       | Description                    |
|--------|-----------------------------------|------------|--------------------------------|
| POST   | /api/auth/login                   | Public     | User login                     |
| GET    | /api/auth/me                      | All        | Get current user               |
| POST   | /api/auth/change-password         | All        | Change password                |
| GET    | /api/users                        | Admin+     | List users                     |
| POST   | /api/users                        | Admin      | Create user                    |
| PUT    | /api/users/:id                    | Admin      | Update user                    |
| DELETE | /api/users/:id                    | Admin      | Deactivate user                |
| GET    | /api/divisions                    | All        | List divisions                 |
| POST   | /api/divisions                    | Admin      | Create division                |
| GET    | /api/weekly-updates               | All        | List weekly updates            |
| POST   | /api/weekly-updates               | Director   | Submit weekly update           |
| GET    | /api/weekly-plans                 | All        | List weekly plans              |
| POST   | /api/weekly-plans                 | Director   | Create plan (+ activity)       |
| GET    | /api/activities                   | All        | List activities (filterable)   |
| PUT    | /api/activities/:id/status        | Director   | Update activity status         |
| POST   | /api/activities/:id/comments      | All        | Add comment                    |
| GET    | /api/notifications                | All        | Get notifications              |
| PUT    | /api/notifications/:id/read       | All        | Mark as read                   |
| PUT    | /api/notifications/read-all       | All        | Mark all as read               |
| GET    | /api/settings                     | Admin      | Get system settings            |
| PUT    | /api/settings                     | Admin      | Update settings                |
| GET    | /api/dashboard/director           | Director   | Director dashboard data        |
| GET    | /api/dashboard/bureau-head        | Bureau Head| Bureau Head dashboard data     |
| GET    | /api/dashboard/minister           | Minister   | Minister dashboard data        |

---

## Background Jobs (Cron)

| Job                      | Schedule      | Description                                          |
|--------------------------|---------------|------------------------------------------------------|
| Overdue Detection        | Every hour    | Mark overdue activities, send notifications           |
| Deadline Warnings        | Every hour    | Warn about approaching deadlines                     |
| Escalation               | Every hour    | Notify leadership about severely overdue activities  |
| Repeated Detection       | Every 6 hours | Flag activities ongoing 3+ consecutive weeks         |

---

## Production Deployment

1. Build the client: `cd client && npm run build`
2. Serve the `client/dist` folder via nginx or Express static middleware
3. Set `NODE_ENV=production` and use strong `JWT_SECRET`
4. Configure real SMTP credentials
5. Use connection pooling for PostgreSQL
6. Set up process manager (PM2) for the Node.js server
7. Enable HTTPS via reverse proxy

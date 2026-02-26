-- ============================================================
-- Bureau Activity Tracking System - Database Schema
-- ============================================================

-- Extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Enum types
CREATE TYPE user_role AS ENUM ('division_director', 'bureau_head', 'minister', 'admin');
CREATE TYPE activity_status AS ENUM ('not_started', 'ongoing', 'completed', 'not_applicable');
CREATE TYPE notification_type AS ENUM ('deadline_approaching', 'overdue', 'escalation', 'general');
CREATE TYPE created_from_type AS ENUM ('plan', 'update');

-- ============================================================
-- Divisions
-- ============================================================
CREATE TABLE divisions (
    id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name        VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMPTZ DEFAULT NOW(),
    updated_at  TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- Users
-- ============================================================
CREATE TABLE users (
    id            UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name          VARCHAR(255) NOT NULL,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          user_role NOT NULL DEFAULT 'division_director',
    division_id   UUID REFERENCES divisions(id) ON DELETE SET NULL,
    position      VARCHAR(255),
    is_active     BOOLEAN DEFAULT TRUE,
    created_at    TIMESTAMPTZ DEFAULT NOW(),
    updated_at    TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- Activities
-- ============================================================
CREATE TABLE activities (
    id                   UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    division_id          UUID NOT NULL REFERENCES divisions(id) ON DELETE CASCADE,
    title                VARCHAR(500) NOT NULL,
    description          TEXT,
    assigned_users       JSONB DEFAULT '[]',
    start_date           DATE,
    deadline             DATE,
    status               activity_status DEFAULT 'not_started',
    last_updated         TIMESTAMPTZ DEFAULT NOW(),
    carry_forward_count  INT DEFAULT 0,
    created_from         created_from_type,
    is_overdue           BOOLEAN DEFAULT FALSE,
    is_repeated          BOOLEAN DEFAULT FALSE,
    overdue_notified     BOOLEAN DEFAULT FALSE,
    escalation_notified  BOOLEAN DEFAULT FALSE,
    created_at           TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_activities_division ON activities(division_id);
CREATE INDEX idx_activities_status ON activities(status);
CREATE INDEX idx_activities_deadline ON activities(deadline);
CREATE INDEX idx_activities_overdue ON activities(is_overdue);

-- ============================================================
-- Weekly Updates
-- ============================================================
CREATE TABLE weekly_updates (
    id                   UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    division_id          UUID NOT NULL REFERENCES divisions(id) ON DELETE CASCADE,
    submitted_by         UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    position             VARCHAR(255),
    week_start           DATE NOT NULL,
    week_end             DATE NOT NULL,
    submission_date      DATE DEFAULT CURRENT_DATE,
    activity_description TEXT NOT NULL,
    responsible_persons  JSONB DEFAULT '[]',
    status               activity_status DEFAULT 'not_started',
    deadline             DATE,
    challenges           TEXT,
    comments             TEXT,
    activity_id          UUID REFERENCES activities(id) ON DELETE SET NULL,
    created_at           TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_weekly_updates_division ON weekly_updates(division_id);
CREATE INDEX idx_weekly_updates_submitted_by ON weekly_updates(submitted_by);
CREATE INDEX idx_weekly_updates_week ON weekly_updates(week_start, week_end);

-- ============================================================
-- Weekly Plans
-- ============================================================
CREATE TABLE weekly_plans (
    id                       UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    division_id              UUID NOT NULL REFERENCES divisions(id) ON DELETE CASCADE,
    submitted_by             UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    week_start               DATE NOT NULL,
    week_end                 DATE NOT NULL,
    planned_activity         TEXT NOT NULL,
    responsible_persons      JSONB DEFAULT '[]',
    start_date               DATE,
    expected_completion_date DATE,
    notes                    TEXT,
    activity_id              UUID REFERENCES activities(id) ON DELETE SET NULL,
    created_at               TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_weekly_plans_division ON weekly_plans(division_id);
CREATE INDEX idx_weekly_plans_submitted_by ON weekly_plans(submitted_by);

-- ============================================================
-- Activity Comments
-- ============================================================
CREATE TABLE activity_comments (
    id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    activity_id UUID NOT NULL REFERENCES activities(id) ON DELETE CASCADE,
    user_id     UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    comment     TEXT NOT NULL,
    created_at  TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_comments_activity ON activity_comments(activity_id);

-- ============================================================
-- Notifications
-- ============================================================
CREATE TABLE notifications (
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id    UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title      VARCHAR(500) NOT NULL,
    message    TEXT,
    type       notification_type DEFAULT 'general',
    is_read    BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);

-- ============================================================
-- System Settings
-- ============================================================
CREATE TABLE system_settings (
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    key        VARCHAR(255) NOT NULL UNIQUE,
    value      TEXT NOT NULL,
    label      VARCHAR(255),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- Default Settings
-- ============================================================
INSERT INTO system_settings (key, value, label) VALUES
    ('overdue_threshold_days', '1', 'Overdue Threshold (days)'),
    ('escalation_threshold_days', '7', 'Escalation Threshold (days)'),
    ('reminder_frequency_hours', '24', 'Reminder Frequency (hours)'),
    ('escalation_enabled', 'true', 'Enable Escalation'),
    ('deadline_warning_days', '3', 'Deadline Warning (days before)');

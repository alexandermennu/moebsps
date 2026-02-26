-- ============================================================
-- Seed Data for Bureau Activity Tracking System
-- ============================================================

-- Default Divisions
INSERT INTO divisions (id, name, description) VALUES
    ('a1000000-0000-0000-0000-000000000001', 'Finance Division', 'Handles financial operations and budgeting'),
    ('a1000000-0000-0000-0000-000000000002', 'Operations Division', 'Manages day-to-day operations'),
    ('a1000000-0000-0000-0000-000000000003', 'Human Resources Division', 'Manages personnel and recruitment'),
    ('a1000000-0000-0000-0000-000000000004', 'ICT Division', 'Information and communication technology');

-- Default Admin User (password: Admin@123)
INSERT INTO users (id, name, email, password_hash, role, position) VALUES
    ('b1000000-0000-0000-0000-000000000001', 'System Admin', 'admin@bureau.gov', '$2a$12$LJ3m4ys3Lg0YpQtPrYMjf.wGX6bFHJzPQQBjKj6xE.fVfJqvG1VXy', 'admin', 'System Administrator');

-- Sample Users (password: Password@123 for all)
INSERT INTO users (id, name, email, password_hash, role, division_id, position) VALUES
    ('b1000000-0000-0000-0000-000000000002', 'Jane Director', 'jane@bureau.gov', '$2a$12$LJ3m4ys3Lg0YpQtPrYMjf.wGX6bFHJzPQQBjKj6xE.fVfJqvG1VXy', 'division_director', 'a1000000-0000-0000-0000-000000000001', 'Division Director'),
    ('b1000000-0000-0000-0000-000000000003', 'Bob Bureau', 'bob@bureau.gov', '$2a$12$LJ3m4ys3Lg0YpQtPrYMjf.wGX6bFHJzPQQBjKj6xE.fVfJqvG1VXy', 'bureau_head', NULL, 'Bureau Head'),
    ('b1000000-0000-0000-0000-000000000004', 'Mary Minister', 'mary@bureau.gov', '$2a$12$LJ3m4ys3Lg0YpQtPrYMjf.wGX6bFHJzPQQBjKj6xE.fVfJqvG1VXy', 'minister', NULL, 'Minister');

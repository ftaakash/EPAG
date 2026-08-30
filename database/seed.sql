-- EPAG Seed Data
USE epag;

-- Departments
INSERT INTO departments (name) VALUES ('Engineering'), ('Administration');

-- Users (passwords are all "password123" hashed with password_hash())
-- Pre-generated PHP password_hash values for 'password123'
INSERT INTO users (name, email, password_hash, role, dept_id) VALUES
('Alice Employee',  'employee@epag.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee',  1),
('Bob Manager',     'manager@epag.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',   1),
('Carol Finance',   'finance@epag.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finance',   2),
('Dave Materials',  'materials@epag.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'materials', 2),
('Eve Admin',       'admin@epag.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',     2);

-- Vendors
INSERT INTO vendors (name, approved) VALUES ('TechSupply Co.', 1), ('Office World Ltd.', 1);

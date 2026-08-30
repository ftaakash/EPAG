-- EPAG Database Schema
CREATE DATABASE IF NOT EXISTS epag;
USE epag;

CREATE TABLE IF NOT EXISTS departments (
  dept_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('employee','manager','finance','materials','admin') NOT NULL,
  dept_id INT,
  FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
);

CREATE TABLE IF NOT EXISTS vendors (
  vendor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  approved BOOLEAN DEFAULT 0
);

CREATE TABLE IF NOT EXISTS requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  item_desc VARCHAR(255) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending_manager','pending_finance','pending_materials','fulfilled','rejected') DEFAULT 'pending_manager',
  vendor_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id)
);

CREATE TABLE IF NOT EXISTS approvals (
  approval_id INT AUTO_INCREMENT PRIMARY KEY,
  request_id INT NOT NULL,
  approver_id INT NOT NULL,
  stage ENUM('manager','finance','materials') NOT NULL,
  decision ENUM('approved','rejected') NOT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES requests(request_id),
  FOREIGN KEY (approver_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS audit_log (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  request_id INT,
  actor_id INT,
  action VARCHAR(255) NOT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES requests(request_id),
  FOREIGN KEY (actor_id) REFERENCES users(user_id)
);

# EPAG — End-to-End Development Plan
### Enterprise Procurement Approval Gateway — build, debug, deploy

This plan is written so you can hand it, phase by phase, to an AI coding assistant (e.g. Claude Code) and get a working project out the other end. Each phase has: what gets built, the exact prompt to give the AI, and how to verify it before moving on. Do not skip verification steps — a broken Phase 2 makes Phase 5 unexplainable in the viva.

---

## 0. Stack Decision (locked in for this plan)

| Layer | Choice | Why |
|---|---|---|
| Server-side language | **PHP 8** | Fastest to set up on Windows (XAMPP, zero JDK/servlet config), and it's an explicit syllabus alternative to JSP/.NET |
| Database | **MySQL** (via XAMPP) | Standard RDBMS, matches "Database Management Systems" syllabus topic |
| Frontend | HTML + CSS + vanilla **JavaScript** (fetch API for status updates) | Syllabus lists JavaScript explicitly; no framework needed |
| Architecture | Manual **MVC** (no framework) | Keeps every line explainable in a viva — no "the framework did it" answers |
| Local server | **XAMPP** (Apache + MySQL) | One installer, works on your Windows PC, no Docker/cloud needed |

If your professor specifically wants JSP or .NET instead, the plan structure below is identical — only the syntax in Phase 2–4 changes. Say the word and I'll regenerate those phases for Java/Tomcat.

---

## 1. Prerequisites (do this once, ~20 minutes)

1. Install **XAMPP** (Apache + MySQL + PHP) for Windows.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Confirm `http://localhost` loads the XAMPP dashboard.
4. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) — confirm it loads.
5. Install **VS Code** (or your editor of choice) with the PHP extension.
6. Create the project folder at `C:\xampp\htdocs\epag` — everything from here on lives there.

---

## 2. Project Structure

```
epag/
├── config/
│   └── db.php                # DB connection
├── models/
│   ├── User.php
│   ├── Request.php
│   ├── Approval.php
│   ├── Vendor.php
│   └── AuditLog.php
├── controllers/
│   ├── AuthController.php
│   ├── RequestController.php
│   ├── ApprovalController.php
│   └── AuditController.php
├── views/
│   ├── login.php
│   ├── employee_dashboard.php
│   ├── manager_dashboard.php
│   ├── finance_dashboard.php
│   ├── materials_dashboard.php
│   └── audit_log.php
├── public/
│   ├── css/style.css
│   └── js/app.js
├── database/
│   └── schema.sql
└── index.php                 # router
```

---

## 3. Database Schema (build this first — everything depends on it)

Save as `database/schema.sql` and import via phpMyAdmin:

```sql
CREATE DATABASE epag;
USE epag;

CREATE TABLE departments (
  dept_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('employee','manager','finance','materials','admin') NOT NULL,
  dept_id INT,
  FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
);

CREATE TABLE vendors (
  vendor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  approved BOOLEAN DEFAULT 0
);

CREATE TABLE requests (
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

CREATE TABLE approvals (
  approval_id INT AUTO_INCREMENT PRIMARY KEY,
  request_id INT NOT NULL,
  approver_id INT NOT NULL,
  stage ENUM('manager','finance','materials') NOT NULL,
  decision ENUM('approved','rejected') NOT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES requests(request_id),
  FOREIGN KEY (approver_id) REFERENCES users(user_id)
);

CREATE TABLE audit_log (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  request_id INT,
  actor_id INT,
  action VARCHAR(255) NOT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES requests(request_id),
  FOREIGN KEY (actor_id) REFERENCES users(user_id)
);
```

**Verify:** open phpMyAdmin → `epag` database → confirm all 5 tables exist with the right columns.

---

## 4. Build Phases (feed each prompt to your AI coding assistant in order)

### Phase 1 — DB connection + seed data
**Prompt to give the AI:**
> Using the schema in `database/schema.sql`, write `config/db.php` with a PDO MySQL connection (host localhost, db epag, user root, no password — XAMPP default). Then write a `database/seed.sql` that inserts 2 departments, 5 users (one per role: employee, manager, finance, materials, admin — passwords hashed with PHP's `password_hash`), and 2 approved vendors.

**Verify:** Run seed.sql, confirm `SELECT * FROM users` shows 5 rows with hashed passwords.

---

### Phase 2 — Authentication + RBAC
**Prompt:**
> Build `controllers/AuthController.php` with login (email+password, verify against `password_hash`) and logout, using PHP sessions to store `user_id` and `role`. Build `views/login.php`. Add a `requireRole($roles[])` helper that any controller can call to block access if the session role isn't in the allowed list.

**Verify:** Log in as each of the 5 seeded users; confirm session persists and wrong-role access to another role's dashboard is blocked (test by visiting the URL directly).

---

### Phase 3 — Request creation (Employee)
**Prompt:**
> Build `models/Request.php` (create, findById, findByUser, updateStatus) and `controllers/RequestController.php` with a `create` action. Build `views/employee_dashboard.php`: a form to submit a request (item description + amount) and a table listing the logged-in employee's own requests with current status.

**Verify:** Log in as employee, submit 2–3 requests, confirm they appear in the table with status `pending_manager`.

---

### Phase 4 — Approval chain (Manager → Finance → Materials)
**Prompt:**
> Build `models/Approval.php` and `controllers/ApprovalController.php` with an `approve` and `reject` action. On approve: insert an `approvals` row for that stage, advance `requests.status` to the next stage (manager→finance→materials→fulfilled), and insert an `audit_log` row. On reject: set status to `rejected` and log it. Build `views/manager_dashboard.php`, `finance_dashboard.php`, `materials_dashboard.php` — each shows only requests currently at that role's stage, with Approve/Reject buttons. The materials dashboard's approve action must also let the user pick a vendor from the `vendors` table before marking fulfilled.

**Verify:** Push one request through all three stages logged in as each role in turn; confirm status advances correctly at each step and a rejected request stops the chain.

---

### Phase 5 — Audit trail view
**Prompt:**
> Build `controllers/AuditController.php` and `views/audit_log.php`, restricted to the `admin` role, showing every `audit_log` row joined with the requester's name, the action, and the timestamp, most recent first.

**Verify:** Log in as admin, confirm every action from Phases 3–4 appears with correct actor and timestamp.

---

### Phase 6 — Router + polish
**Prompt:**
> Build `index.php` as a simple front controller: read a `?action=` query param, route to the right controller method, redirect to login if no session exists. Add `public/css/style.css` for a clean, professional look (simple cards/tables, no heavy design). Add `public/js/app.js` to disable the submit button after click (prevent double-submit) and show a small toast on status change.

**Verify:** Navigate the whole app through `index.php` links only — no direct file access needed.

---

## 5. Debugging Checklist (use this whenever something breaks)

| Symptom | Likely cause | Fix |
|---|---|---|
| "Connection refused" to MySQL | XAMPP MySQL not started | Start it from XAMPP Control Panel |
| Login always fails | Password hash mismatch | Re-run seed.sql; confirm `password_verify()` is used, not `==` |
| Role sees wrong dashboard | Session role not checked before render | Confirm `requireRole()` is called at the top of every controller action |
| Request stuck at one stage | Status enum value typo | Compare `requests.status` enum values against what `ApprovalController` writes |
| Audit log missing entries | Insert not wrapped with the status-update transaction | Wrap approval + status-update + audit-log insert in one function so they always happen together |
| Foreign key errors on insert | Referenced row doesn't exist yet | Confirm seed data ran before any request/approval inserts |

When something breaks and you're stuck, describe the exact error message and which phase you're on — that's enough for the AI to debug without re-explaining the whole project.

---

## 6. Deployment (for the demo)

1. **Local demo (simplest, recommended):** run entirely on your machine via XAMPP — `http://localhost/epag`. This is what most evaluators expect for a college project.
2. **LAN demo (if asked to show "deployment"):** on the demo day, find your machine's LAN IP (`ipconfig`), open the Windows Firewall for port 80, and have the evaluator hit `http://<your-ip>/epag` from another device on the same network. This directly demonstrates the syllabus's LAN/deployment concepts.
3. **Talking points for the "deployment issues" marks:** session handling across requests, DB connection limits under concurrent approvals, moving to a split app-server/DB-server setup, and firewall rules restricting DB access to only the app server — all already written up in the proposal document's deployment section.

---

## 7. Pre-Demo Checklist

- [ ] All 5 seeded logins work
- [ ] A request can be pushed end-to-end (employee → manager → finance → materials → fulfilled)
- [ ] A rejection at any stage correctly stops the chain and shows on the employee's dashboard
- [ ] Audit log shows every action with correct actor/timestamp
- [ ] Wrong-role access is blocked when tested by direct URL
- [ ] App runs cleanly from a fresh XAMPP restart (no leftover state assumptions)
- [ ] You can explain every file in `controllers/` and `models/` without reading from notes

---

## 8. How to Use This With an AI Coding Assistant

Work through Section 4 in order, one phase per session. For each phase: paste the phase's prompt, let the AI generate the files, then run the phase's **Verify** step yourself before moving on. If a phase fails verification, tell the AI what you tested and what happened instead of what you expected — that's a debugging prompt, not a new-feature prompt, and it'll fix in place rather than rewriting the phase from scratch.

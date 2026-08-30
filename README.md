# EPAG — Enterprise Procurement Approval Gateway

A PHP 8 MVC web application for managing multi-stage procurement approval workflows.

## Stack
- **Backend**: PHP 8 (no framework, manual MVC)
- **Database**: MySQL via XAMPP
- **Frontend**: HTML + CSS + Vanilla JavaScript
- **Server**: XAMPP (Apache + MySQL)

## Project Structure
```
epag/
├── config/db.php               # PDO MySQL connection
├── models/                     # Request, Approval, Vendor, AuditLog, User
├── controllers/                # Auth, Request, Approval, Audit
├── views/                      # login, dashboards (employee/manager/finance/materials), audit_log
├── public/css/style.css        # Dark-mode professional UI
├── public/js/app.js            # Double-submit guard + toast notifications
├── database/schema.sql         # Full DB schema
├── database/seed.sql           # Seed: 2 depts, 5 users (one/role), 2 vendors
└── index.php                   # Front controller router
```

## Approval Workflow
```
Employee submits → Manager approves → Finance approves → Materials fulfills (picks vendor)
                                                       ↘ Any stage can Reject (stops chain)
```

## Setup (XAMPP)

### 1. Place files in XAMPP
```
Copy this entire folder to: C:\xampp\htdocs\epag
```

### 2. Import Database
1. Start Apache + MySQL in XAMPP Control Panel
2. Open `http://localhost/phpmyadmin`
3. Run `database/schema.sql` (creates the `epag` database + all tables)

### 3. Generate & Import Seed Data
```bash
cd C:\xampp\htdocs\epag\database
php generate_hashes.php > seed_generated.sql
# Then import seed_generated.sql in phpMyAdmin
# OR manually run seed.sql (uses pre-computed hash for "password")
```

### 4. Access the App
```
http://localhost/epag
```

## Demo Logins (password: `password`)
| Role      | Email                 |
|-----------|-----------------------|
| Employee  | employee@epag.com     |
| Manager   | manager@epag.com      |
| Finance   | finance@epag.com      |
| Materials | materials@epag.com    |
| Admin     | admin@epag.com        |

## Development Plan
See [DEVELOPMENT_PLAN.md](DEVELOPMENT_PLAN.md) for the full phase-by-phase build guide.

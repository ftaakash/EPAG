<?php
// config/db.php — Dual PDO connection (MySQL with automatic SQLite fallback)
define('DB_HOST', 'localhost');
define('DB_NAME', 'epag');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('SQLITE_FILE', __DIR__ . '/../database/epag.sqlite');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // Try MySQL first
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $pdo;
        } catch (PDOException $e) {
            // MySQL unavailable, fall back to SQLite seamlessly
            $sqlitePath = SQLITE_FILE;
            $needsInit = !file_exists($sqlitePath);
            
            $pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            // Enable Foreign Keys in SQLite
            $pdo->exec('PRAGMA foreign_keys = ON;');

            if ($needsInit) {
                initSQLiteDatabase($pdo);
            }
        }
    }
    return $pdo;
}

function initSQLiteDatabase(PDO $pdo): void {
    $schema = "
    CREATE TABLE IF NOT EXISTS departments (
        dept_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL
    );

    CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        role TEXT CHECK(role IN ('employee','manager','finance','materials','admin')) NOT NULL,
        dept_id INTEGER,
        FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
    );

    CREATE TABLE IF NOT EXISTS vendors (
        vendor_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        approved INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS requests (
        request_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        item_desc TEXT NOT NULL,
        amount REAL NOT NULL,
        status TEXT DEFAULT 'pending_manager' CHECK(status IN ('pending_manager','pending_finance','pending_materials','fulfilled','rejected')),
        vendor_id INTEGER NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id),
        FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id)
    );

    CREATE TABLE IF NOT EXISTS approvals (
        approval_id INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id INTEGER NOT NULL,
        approver_id INTEGER NOT NULL,
        stage TEXT CHECK(stage IN ('manager','finance','materials')) NOT NULL,
        decision TEXT CHECK(decision IN ('approved','rejected')) NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES requests(request_id),
        FOREIGN KEY (approver_id) REFERENCES users(user_id)
    );

    CREATE TABLE IF NOT EXISTS audit_log (
        log_id INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id INTEGER,
        actor_id INTEGER,
        action TEXT NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES requests(request_id),
        FOREIGN KEY (actor_id) REFERENCES users(user_id)
    );
    ";
    
    $pdo->exec($schema);

    // Seed data
    $defaultPassHash = password_hash('password', PASSWORD_BCRYPT);

    $seed = "
    INSERT INTO departments (name) VALUES ('Engineering'), ('Administration');

    INSERT INTO users (name, email, password_hash, role, dept_id) VALUES
    ('Alice Employee',  'employee@epag.com',  '$defaultPassHash', 'employee',  1),
    ('Bob Manager',     'manager@epag.com',   '$defaultPassHash', 'manager',   1),
    ('Carol Finance',   'finance@epag.com',   '$defaultPassHash', 'finance',   2),
    ('Dave Materials',  'materials@epag.com', '$defaultPassHash', 'materials', 2),
    ('Eve Admin',       'admin@epag.com',     '$defaultPassHash', 'admin',     2);

    INSERT INTO vendors (name, approved) VALUES ('TechSupply Co.', 1), ('Office World Ltd.', 1);
    ";

    $pdo->exec($seed);
}

<?php
/**
 * generate_hashes.php
 * Run this once from command line: php generate_hashes.php
 * It prints the correct INSERT statements for seed.sql
 * with fresh bcrypt hashes for the password "password"
 */

$roles = [
    ['Alice Employee',  'employee@epag.com',  'employee',  1],
    ['Bob Manager',     'manager@epag.com',   'manager',   1],
    ['Carol Finance',   'finance@epag.com',   'finance',   2],
    ['Dave Materials',  'materials@epag.com', 'materials', 2],
    ['Eve Admin',       'admin@epag.com',     'admin',     2],
];

echo "-- Paste these INTO seed.sql, replacing the existing INSERT INTO users block\n";
echo "INSERT INTO users (name, email, password_hash, role, dept_id) VALUES\n";
$rows = [];
foreach ($roles as [$name, $email, $role, $dept]) {
    $hash = password_hash('password', PASSWORD_BCRYPT);
    $rows[] = "('$name', '$email', '$hash', '$role', $dept)";
}
echo implode(",\n", $rows) . ";\n";

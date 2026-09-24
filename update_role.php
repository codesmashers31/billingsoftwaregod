<?php
$mysqli = new mysqli("localhost", "root", "", "god_statue_erp");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Create a new role
$role_name = "Custom Warehouse Manager";
$role_slug = "custom-warehouse-manager";
$role_desc = "Custom role for warehouse manager with specific navigations removed";

$stmt = $mysqli->prepare("INSERT INTO roles (name, slug, description, is_system, status) VALUES (?, ?, ?, 0, 'active')");
$stmt->bind_param("sss", $role_name, $role_slug, $role_desc);
$stmt->execute();
$role_id = $mysqli->insert_id;

// 2. Fetch permissions to grant (excluding accounts, reports, users, roles, settings, audit)
$excluded_modules = ['accounts', 'reports', 'users', 'roles', 'settings', 'audit'];
$placeholders = implode(',', array_fill(0, count($excluded_modules), '?'));

$sql = "SELECT id FROM permissions WHERE module NOT IN ($placeholders)";
$stmt_perms = $mysqli->prepare($sql);
$stmt_perms->bind_param(str_repeat('s', count($excluded_modules)), ...$excluded_modules);
$stmt_perms->execute();
$result = $stmt_perms->get_result();

while ($row = $result->fetch_assoc()) {
    $perm_id = $row['id'];
    $mysqli->query("INSERT INTO role_permissions (role_id, permission_id) VALUES ($role_id, $perm_id)");
}

// 3. Update the warehousemanager user to use this new role
$mysqli->query("UPDATE users SET role_id = $role_id WHERE username IN ('warehousemanager', 'warhouse manager')");

echo "Role created and user updated successfully!";
$mysqli->close();
?>

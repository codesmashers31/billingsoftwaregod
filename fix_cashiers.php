<?php
$mysqli = new mysqli("localhost", "root", "", "god_statue_erp");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Create a new custom role 'Custom Cashier'
$role_name = "Custom Cashier";
$role_slug = "custom-cashier";
$role_desc = "Cashier role without product catalog access";

$stmt = $mysqli->prepare("INSERT INTO roles (name, slug, description, is_system, status) VALUES (?, ?, ?, 0, 'active')");
$stmt->bind_param("sss", $role_name, $role_slug, $role_desc);
$stmt->execute();
$new_role_id = $mysqli->insert_id;

// 2. Copy permissions from role_id = 4 (Billing Staff / Cashier) BUT exclude 'products' and 'catalog' modules
$sql = "SELECT permission_id FROM role_permissions rp 
        JOIN permissions p ON rp.permission_id = p.id 
        WHERE rp.role_id = 4 AND p.module NOT IN ('products', 'catalog')";

$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
    $perm_id = $row['permission_id'];
    $mysqli->query("INSERT INTO role_permissions (role_id, permission_id) VALUES ($new_role_id, $perm_id)");
}

// 3. Update anand and priya to use this new role
$mysqli->query("UPDATE users SET role_id = $new_role_id WHERE username IN ('anand', 'priya')");

// 4. Delete the 'cashier' user
$mysqli->query("DELETE FROM users WHERE username = 'cashier'");

echo "Custom Cashier role created, assigned to Anand and Priya, and original cashier user deleted successfully!\n";

$mysqli->close();
?>

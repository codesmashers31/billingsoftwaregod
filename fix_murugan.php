<?php
$mysqli = new mysqli("localhost", "root", "", "god_statue_erp");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Get the role_id of 'Custom Warehouse Manager'
$result = $mysqli->query("SELECT id FROM roles WHERE slug = 'custom-warehouse-manager' LIMIT 1");
$role_row = $result->fetch_assoc();

if ($role_row) {
    $custom_warehouse_role_id = $role_row['id'];
    
    // 2. Assign the 'Custom Warehouse Manager' role to murugan
    $stmt = $mysqli->prepare("UPDATE users SET role_id = ? WHERE username = 'murugan'");
    $stmt->bind_param("i", $custom_warehouse_role_id);
    $stmt->execute();
    
    echo "Updated 'murugan' to use the Custom Warehouse Manager role.\n";
} else {
    echo "Could not find 'Custom Warehouse Manager' role.\n";
}

// 3. Delete the temporary warehousemanager users we created earlier
$mysqli->query("DELETE FROM users WHERE username IN ('warehousemanager', 'warhouse manager')");
echo "Deleted the temporary 'warehousemanager' and 'warhouse manager' users.\n";

$mysqli->close();
?>

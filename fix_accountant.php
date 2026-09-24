<?php
$mysqli = new mysqli("localhost", "root", "", "god_statue_erp");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Get the role_id of 'Custom Accountant'
$result = $mysqli->query("SELECT id FROM roles WHERE slug = 'custom-accountant' LIMIT 1");
$role_row = $result->fetch_assoc();
if ($role_row) {
    $custom_accountant_role_id = $role_row['id'];
    
    // 2. Assign the 'Custom Accountant' role to venkatesh
    $stmt = $mysqli->prepare("UPDATE users SET role_id = ? WHERE username = 'venkatesh'");
    $stmt->bind_param("i", $custom_accountant_role_id);
    $stmt->execute();
    
    echo "Updated 'venkatesh' to use the Custom Accountant role.\n";
} else {
    echo "Could not find 'Custom Accountant' role.\n";
}

// 3. Delete the temporary 'accountant' user we created earlier
$mysqli->query("DELETE FROM users WHERE username = 'accountant'");
echo "Deleted the temporary 'accountant' user.\n";

$mysqli->close();
?>

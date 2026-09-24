<?php
$mysqli = new mysqli("localhost", "root", "", "god_statue_erp");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Ensure the correct hash from user 1 is used
$result = $mysqli->query("SELECT password FROM users WHERE id = 1");
$row = $result->fetch_assoc();
$hash = $row['password'];

// Update the warehousemanager user
$stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE username = 'warehousemanager'");
$stmt->bind_param("s", $hash);
$stmt->execute();

// Also create "warhouse manager" in case they literally meant that as username
$username2 = "warhouse manager";
$email2 = "warhouse@godstatueerp.com";
$name = "Warehouse Manager";
$role_id = 1;
$dept_id = 1;
$status = "active";
$mobile = "+91 0000000001";

$stmt2 = $mysqli->prepare("INSERT IGNORE INTO users (name, username, email, mobile, password, role_id, department_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt2->bind_param("sssssiis", $name, $username2, $email2, $mobile, $hash, $role_id, $dept_id, $status);
$stmt2->execute();

echo "User updated successfully!";
$mysqli->close();
?>

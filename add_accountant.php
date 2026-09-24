<?php
$mysqli = new mysqli("localhost", "root", "", "god_statue_erp");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Ensure the correct hash from user 1 is used
$result = $mysqli->query("SELECT password FROM users WHERE id = 1");
$row = $result->fetch_assoc();
$hash = $row['password'];

// User details for Accountant
$name = "Accountant";
$username = "accountant";
$email = "accountant@godstatueerp.com";
$role_id = 1; // Super Admin Role
$dept_id = 4; // Accounts & Finance (from seeder)
$status = "active";
$mobile = "+91 0000000002";

// Check if username exists, if so update it, else insert
$check = $mysqli->query("SELECT id FROM users WHERE username = 'accountant'");
if ($check->num_rows > 0) {
    $stmt = $mysqli->prepare("UPDATE users SET name=?, email=?, role_id=?, department_id=?, password=? WHERE username=?");
    $stmt->bind_param("ssiiss", $name, $email, $role_id, $dept_id, $hash, $username);
    $stmt->execute();
    echo "Existing accountant user updated successfully to Super Admin.";
} else {
    $stmt = $mysqli->prepare("INSERT INTO users (name, username, email, mobile, password, role_id, department_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiis", $name, $username, $email, $mobile, $hash, $role_id, $dept_id, $status);
    $stmt->execute();
    echo "New accountant user created successfully with Super Admin permissions.";
}

$mysqli->close();
?>

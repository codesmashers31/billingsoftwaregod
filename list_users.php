<?php
$mysqli = new mysqli("localhost", "root", "", "god_statue_erp");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = "SELECT u.name, u.username, u.email, r.name as role_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.id 
        WHERE u.status = 'active'
        ORDER BY u.id ASC";

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Name: " . $row["name"] . "\n";
        echo "Username: " . $row["username"] . "\n";
        echo "Email: " . $row["email"] . "\n";
        echo "Role: " . $row["role_name"] . "\n";
        echo str_repeat("-", 40) . "\n";
    }
} else {
    echo "No active users found.";
}

$mysqli->close();
?>

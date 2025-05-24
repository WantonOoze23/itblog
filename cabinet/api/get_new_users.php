<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
$res = $conn->query("SELECT * FROM New_User");
$new_users = [];
while ($row = $res->fetch_assoc()) {
    $new_users[] = $row;
}
echo json_encode(['success' => true, 'users' => $new_users]);
$conn->close();
?>
<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
$user_id = intval($_GET['user_id'] ?? 0);

$res = $conn->query("SELECT u.*, r.name as role_name FROM User u LEFT JOIN Role r ON u.role_id = r.role_id WHERE u.user_id = $user_id");
if ($user = $res->fetch_assoc()) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Користувача не знайдено']);
}
$conn->close();
?>
<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизовано']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;

$sql = "SELECT user_id, username, full_name, email FROM User WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $user['is_admin'] = $is_admin;
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Користувача не знайдено']);
}
$stmt->close();
$conn->close();
?>
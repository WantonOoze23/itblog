<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
$new_user_id = intval($_GET['new_user_id'] ?? 0);
$res = $conn->query("SELECT * FROM New_User WHERE new_user_id = $new_user_id");
if ($user = $res->fetch_assoc()) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Користувача не знайдено']);
}
$conn->close();
?>
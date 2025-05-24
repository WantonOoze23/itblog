<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
$user_id = intval($_POST['user_id'] ?? 0);
if ($user_id) {
    if $user_id = 1) {
        echo json_encode(['success' => false, 'message' => 'Не можна видалити головного адміністратора']);
        exit();
    }
    if ($conn->query("DELETE FROM User WHERE user_id = $user_id")) {
        echo json_encode(['success' => true, 'message' => 'Користувача видалено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Не вдалося видалити користувача']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Некоректний ID']);
}
$conn->close();
?>
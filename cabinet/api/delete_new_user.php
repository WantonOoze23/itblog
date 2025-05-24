<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
$new_user_id = intval($_POST['new_user_id'] ?? 0);
if ($conn->query("DELETE FROM New_User WHERE new_user_id = $new_user_id")) {
    echo json_encode(['success' => true, 'message' => 'Користувача видалено']);
} else {
    echo json_encode(['success' => false, 'message' => 'Не вдалося видалити користувача']);
}
$conn->close();
?>
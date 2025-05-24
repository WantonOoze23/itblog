<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

// Перевірка, чи це адміністратор
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Доступ заборонено']);
    exit();
}

$user_id = intval($_POST['user_id'] ?? 0);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Некоректний ID']);
    exit();
}

// Оновлення ролі користувача на "адміністратор" (role_id = 1)
$stmt = $conn->prepare("UPDATE User SET role_id = 1 WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Користувач став адміністратором']);
} else {
    echo json_encode(['success' => false, 'message' => 'Помилка при зміні ролі']);
}
$stmt->close();
$conn->close();
?>
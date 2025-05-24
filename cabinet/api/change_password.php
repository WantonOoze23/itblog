<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизовано']);
    exit();
}

$user_id = $_SESSION['user_id'];
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (empty($old_password) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'Введіть всі поля']);
    exit();
}

// Отримуємо поточний хеш пароля
$stmt = $conn->prepare("SELECT password FROM User WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($hash);
if ($stmt->fetch()) {
    if (!password_verify($old_password, $hash)) {
        echo json_encode(['success' => false, 'message' => 'Старий пароль невірний']);
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Оновлюємо пароль
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt2 = $conn->prepare("UPDATE User SET password = ? WHERE user_id = ?");
    $stmt2->bind_param("si", $new_hash, $user_id);
    if ($stmt2->execute()) {
        echo json_encode(['success' => true, 'message' => 'Пароль успішно змінено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Помилка при зміні пароля']);
    }
    $stmt2->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Користувача не знайдено']);
}
$conn->close();
?>
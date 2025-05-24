<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизовано']);
    exit();
}

$category_id = intval($_POST['category_id'] ?? 0);
if (!$category_id) {
    echo json_encode(['success' => false, 'message' => 'Некоректний ID']);
    exit();
}

// Перевірка, чи є пости з цією категорією
$check_stmt = $conn->prepare("SELECT COUNT(*) FROM Post_Category WHERE category_id = ?");
$check_stmt->bind_param("i", $category_id);
$check_stmt->execute();
$check_stmt->bind_result($post_count);
$check_stmt->fetch();
$check_stmt->close();

if ($post_count > 0) {
    echo json_encode(['success' => false, 'message' => 'Категорія містить пости']);
    $conn->close();
    exit();
}

$stmt = $conn->prepare("DELETE FROM Category WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Помилка при видаленні']);
}
$stmt->close();
$conn->close();
?>
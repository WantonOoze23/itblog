<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизовано']);
    exit();
}

$name = trim($_POST['name'] ?? '');
if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Назва не може бути порожньою']);
    exit();
}

$checkStmt = $conn->prepare("SELECT category_id FROM Category WHERE name = ?");
$checkStmt->bind_param("s", $name);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Категорія вже існує']);
    $checkStmt->close();
    $conn->close();
    exit();
}
$checkStmt->close();

$stmt = $conn->prepare("INSERT INTO Category (name) VALUES (?)");
$stmt->bind_param("s", $name);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'category_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Помилка при додаванні']);
}
$stmt->close();
$conn->close();
?>
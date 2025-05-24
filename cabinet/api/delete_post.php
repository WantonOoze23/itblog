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
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Невірний ID поста']);
    exit();
}

$conn->query("DELETE FROM Post_Category WHERE post_id=$post_id");

if ($is_admin) {
    $stmt = $conn->prepare("DELETE FROM Post WHERE post_id=?");
    $stmt->bind_param("i", $post_id);
} else {
    $stmt = $conn->prepare("DELETE FROM Post WHERE post_id=? AND writer_id=?");
    $stmt->bind_param("ii", $post_id, $user_id);
}

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Пост видалено']);
} else {
    echo json_encode(['success' => false, 'message' => 'Не вдалося видалити пост']);
}
$stmt->close();
$conn->close();
?>
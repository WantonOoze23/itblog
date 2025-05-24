<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизовано']);
    exit();
}

$post_id = intval($_GET['post_id'] ?? 0);
if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Невірний ID поста']);
    exit();
}

$sql = "SELECT p.*, u.full_name FROM Post p
        JOIN User u ON p.writer_id = u.user_id
        WHERE p.post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$res = $stmt->get_result();
if ($post = $res->fetch_assoc()) {
    // Отримати id та назви категорій
    $cat_res = $conn->query(
        "SELECT c.category_id, c.name FROM Post_Category pc 
         JOIN Category c ON pc.category_id = c.category_id 
         WHERE pc.post_id = " . intval($post_id)
    );
    $category_ids = [];
    $category_names = [];
    while ($row = $cat_res->fetch_assoc()) {
        $category_ids[] = (int)$row['category_id'];
        $category_names[] = $row['name'];
    }
    $post['category_ids'] = $category_ids; // для форми
    $post['categories'] = $category_names; // для виводу
    echo json_encode(['success' => true, 'post' => $post]);
} else {
    echo json_encode(['success' => false, 'message' => 'Пост не знайдено']);
}
$stmt->close();
$conn->close();
?>
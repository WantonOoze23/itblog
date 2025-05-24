<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизований доступ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'];

if ($is_admin) {
    $sql = "SELECT p.post_id, p.title, p.description, p.image, p.writer_id, p.created_at, u.username,
                   GROUP_CONCAT(c.name) as categories
            FROM Post p
            JOIN User u ON p.writer_id = u.user_id
            LEFT JOIN Post_Category pc ON p.post_id = pc.post_id
            LEFT JOIN Category c ON pc.category_id = c.category_id
            GROUP BY p.post_id
            ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT p.post_id, p.title, p.description, p.image, p.writer_id, p.created_at, u.username,
                   GROUP_CONCAT(c.name) as categories
            FROM Post p
            JOIN User u ON p.writer_id = u.user_id
            LEFT JOIN Post_Category pc ON p.post_id = pc.post_id
            LEFT JOIN Category c ON pc.category_id = c.category_id
            WHERE p.writer_id = ?
            GROUP BY p.post_id
            ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    // categories буде строкою через GROUP_CONCAT, перетворимо у масив
    $row['categories'] = $row['categories'] ? explode(',', $row['categories']) : [];
    $posts[] = $row;
}

echo json_encode(['success' => true, 'posts' => $posts]);

$stmt->close();
$conn->close();
?>
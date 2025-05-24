<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Невірний ID поста']);
    exit();
}

$sql = "SELECT p.post_id, p.title, p.description, p.image, p.created_at, u.full_name 
        FROM Post p
        LEFT JOIN User u ON p.writer_id = u.user_id
        WHERE p.post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($post = $result->fetch_assoc()) {
    // Отримати категорії
    $cat_sql = "SELECT c.name FROM Category c 
                JOIN Post_Category pc ON c.category_id = pc.category_id 
                WHERE pc.post_id = " . intval($post_id);
    $cat_res = $conn->query($cat_sql);
    $categories = [];
    if ($cat_res) {
        while ($cat = $cat_res->fetch_assoc()) {
            $categories[] = $cat['name'];
        }
    }
    $post['categories'] = $categories;

    echo json_encode(['success' => true, 'post' => $post]);
} else {
    echo json_encode(['success' => false, 'message' => 'Пост не знайдено']);
}
$stmt->close();
$conn->close();
?>
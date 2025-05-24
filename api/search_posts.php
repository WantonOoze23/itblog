<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (!$q) {
    echo json_encode(['success' => false, 'message' => 'Порожній запит']);
    exit();
}

// Пошук у назві, описі та категоріях
$sql = "
SELECT DISTINCT p.post_id, p.title, p.description, p.image, p.created_at, u.full_name
FROM Post p
LEFT JOIN User u ON p.writer_id = u.user_id
LEFT JOIN Post_Category pc ON p.post_id = pc.post_id
LEFT JOIN Category c ON pc.category_id = c.category_id
WHERE p.title LIKE ? OR p.description LIKE ? OR c.name LIKE ?
ORDER BY p.created_at DESC
";

$like = '%' . $q . '%';
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    // Отримати категорії для кожного поста
    $cat_sql = "SELECT c.name FROM Category c 
                JOIN Post_Category pc ON c.category_id = pc.category_id 
                WHERE pc.post_id = ?";
    $cat_stmt = $conn->prepare($cat_sql);
    $cat_stmt->bind_param("i", $row['post_id']);
    $cat_stmt->execute();
    $cat_res = $cat_stmt->get_result();
    $categories = [];
    while ($cat = $cat_res->fetch_assoc()) {
        $categories[] = $cat['name'];
    }
    $row['categories'] = $categories;
    $cat_stmt->close();

    $posts[] = $row;
}

echo json_encode(['success' => true, 'posts' => $posts]);
$stmt->close();
$conn->close();
?>
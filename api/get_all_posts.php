<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

$sql = "SELECT p.post_id, p.title, p.description, p.image, p.created_at, u.full_name 
        FROM Post p
        LEFT JOIN User u ON p.writer_id = u.user_id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);

$posts = [];
while ($row = $result->fetch_assoc()) {
    $cat_sql = "SELECT c.name FROM Category c 
                JOIN Post_Category pc ON c.category_id = pc.category_id 
                WHERE pc.post_id = " . intval($row['post_id']);
    $cat_res = $conn->query($cat_sql);
    $categories = [];
    if ($cat_res) {
        while ($cat = $cat_res->fetch_assoc()) {
            $categories[] = $cat['name'];
        }
    }
    $row['categories'] = $categories;
    $posts[] = $row;
}

echo json_encode(['success' => true, 'posts' => $posts]);
$conn->close();
?>
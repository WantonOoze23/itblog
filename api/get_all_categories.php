<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

$res = $conn->query("SELECT category_id as id, name FROM Category ORDER BY id");
$categories = [];
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}
echo json_encode(['categories' => $categories]);
$conn->close();
?>
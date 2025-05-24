<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Доступ заборонено']);
    exit();
}

$sql = "SELECT user_id, username FROM User";
$result = $conn->query($sql);

$authors = [];
while ($row = $result->fetch_assoc()) {
    $authors[] = $row;
}

echo json_encode(['success' => true, 'authors' => $authors]);
$conn->close();
?>
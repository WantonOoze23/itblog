<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Авторизуйтесь, будь ласка']);   
    exit();
}

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

$image = '/images/default.jpg';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $newName = uniqid('post_', true) . '.' . $ext;
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/images/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $uploadPath = $uploadDir . $newName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $image = '/images/' . $newName;
    }
} else if (isset($_POST['image']) && trim($_POST['image']) !== '') {
    $image = trim($_POST['image']);
} else if (isset($_POST['post_id']) && $_POST['post_id']) {
    $stmt_img = $conn->prepare("SELECT image FROM Post WHERE post_id=?");
    $stmt_img->bind_param("i", $_POST['post_id']);
    $stmt_img->execute();
    $stmt_img->bind_result($old_image);
    if ($stmt_img->fetch() && $old_image) {
        $image = $old_image;
    }
    $stmt_img->close();
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$categories = isset($_POST['categories']) ? json_decode($_POST['categories'], true) : [];

if (!$title || !$description) {
    echo json_encode(['success' => false, 'message' => 'Всі поля обовʼязкові']);
    exit();
}

if ($post_id) {
    $is_admin = $_SESSION['is_admin'] ?? 0;
    if ($is_admin) {
        $stmt = $conn->prepare("UPDATE Post SET title=?, description=?, image=? WHERE post_id=?");
        $stmt->bind_param("sssi", $title, $description, $image, $post_id);
    } else {
        $stmt = $conn->prepare("UPDATE Post SET title=?, description=?, image=? WHERE post_id=? AND writer_id=?");
        $stmt->bind_param("sssii", $title, $description, $image, $post_id, $user_id);
    }
    $stmt->execute();

    // Оновлюємо категорії
    $conn->query("DELETE FROM Post_Category WHERE post_id=$post_id");
    $categories_changed = false;
    if (!empty($categories)) {
        $ins_stmt = $conn->prepare("INSERT INTO Post_Category (post_id, category_id) VALUES (?, ?)");
        foreach ($categories as $cat_id) {
            $ins_stmt->bind_param("ii", $post_id, $cat_id);
            $ins_stmt->execute();
            $categories_changed = true;
        }
        $ins_stmt->close();
    }

    if ($stmt->affected_rows > 0 || $categories_changed) {
        echo json_encode(['success' => true, 'message' => 'Пост оновлено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Не вдалося оновити пост']);
    }
    $stmt->close();
} else {
    // Додавання нового поста
    $stmt = $conn->prepare("INSERT INTO Post (title, description, image, writer_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $image, $user_id);
    if ($stmt->execute()) {
        $new_post_id = $stmt->insert_id;
        // Додаємо категорії
        if (!empty($categories)) {
            $ins_stmt = $conn->prepare("INSERT INTO Post_Category (post_id, category_id) VALUES (?, ?)");
            foreach ($categories as $cat_id) {
                $ins_stmt->bind_param("ii", $new_post_id, $cat_id);
                $ins_stmt->execute();
            }
            $ins_stmt->close();
        }
        echo json_encode(['success' => true, 'message' => 'Пост додано']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Помилка при додаванні']);
    }
    $stmt->close();
}
$conn->close();
?>
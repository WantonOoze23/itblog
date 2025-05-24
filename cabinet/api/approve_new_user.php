<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
$new_user_id = intval($_POST['new_user_id'] ?? 0);
$is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] == 1 ? 1 : 0;
$res = $conn->query("SELECT * FROM New_User WHERE new_user_id = $new_user_id");
if ($user = $res->fetch_assoc()) {
    // Перевірка унікальності username/email у User
    $stmt = $conn->prepare("SELECT user_id FROM User WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user['username'], $user['email']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Користувач з таким username або email вже існує']);
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Якщо is_admin, то role_id = 1, інакше з New_User
    $role_id = $is_admin ? 1 : $user['role_id'];

    $stmt = $conn->prepare("INSERT INTO User (username, password, full_name, email, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $user['username'], $user['password'], $user['full_name'], $user['email'], $role_id);
    if ($stmt->execute()) {
        $conn->query("DELETE FROM New_User WHERE new_user_id = $new_user_id");
        echo json_encode(['success' => true, 'message' => 'Користувача додано']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Помилка при додаванні']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Користувача не знайдено']);
}
$conn->close();
?>
<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');

// Отримуємо дані з POST
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$role_id = 2; 
$work_experience = $_POST['work_experience'] ?? null;
$description = $_POST['description'] ?? null;


if (empty($username) || empty($password) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Введіть всі обовʼязкові поля']);
    exit();
}

// Перевірка на унікальність username та email у User та New_User
$stmt = $conn->prepare("SELECT user_id FROM User WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Користувач з таким username або email вже існує']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

$stmt = $conn->prepare("SELECT new_user_id FROM New_User WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Користувач з таким username або email вже існує']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Хешування пароля
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Додаємо користувача у New_User
$stmt = $conn->prepare("INSERT INTO New_User (username, password, full_name, email, role_id, work_experience, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssis", $username, $hashed_password, $full_name, $email, $role_id, $work_experience, $description);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Реєстрація успішна!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Помилка при реєстрації']);
}

$stmt->close();
$conn->close();
?>
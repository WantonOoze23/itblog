<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once 'db_connection.php'; 

// Перевірка підключення
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Помилка підключення до БД']);
    exit();
}

// Отримуємо дані з POST
$email_or_username = $_POST['login_username_or_email'] ?? '';
$password = $_POST['login_password'] ?? '';

if (empty($email_or_username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Введіть усі поля']);
    exit();
}

// Пошук користувача
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, u.password, u.email, r.name AS role_name, r.is_admin
    FROM User u
    JOIN Role r ON u.role_id = r.role_id
    WHERE u.email = ? OR u.username = ?
");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Помилка запиту: ' . $conn->error]);
    exit();
}

$stmt->bind_param("ss", $email_or_username, $email_or_username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['is_admin'] = $user['is_admin'];

        $redirect = $user['is_admin'] ? '/cabinet/admin/index.php' : '/cabinet/writer/index.php';

        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Невірний пароль']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Користувача не знайдено']);
}

$stmt->close();
$conn->close();
<?php
$servername = "localhost";
$username = "root"; // Ваш логін MySQL
$password = ""; // Ваш пароль MySQL
$dbname = "itblog";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}
?>
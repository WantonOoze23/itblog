<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin'])) {
    if ($_SESSION['is_admin']) {
        header("Location: /cabinet/admin/index.php");
        exit();
    } else {
        header("Location: /cabinet/writer/index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аутентифікація</title>

    <link rel="stylesheet" href="style/login.css">
    <link rel="stylesheet" href="style/navigation.css">
</head>
<body>
    <section class="navigation">
        <nav>
            <ul>
                <li><a href="/index.html">Головна</a></li>
                <li><a href="/blog.html">Блог</a></li>
                <li><a href="/contact.html">Контакти</a></li>
            </ul>
        </nav>
    </section>

    <section class="autentification">
        
        <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
            <button id="btn-login" type="button" class="tab-btn active">Вхід</button>
            <button id="btn-register" type="button" class="tab-btn">Реєстрація</button>
        </div>

        <section class="login-form">
            <!-- Вхід -->
            <form action="login_process.php" method="post">
                <label for="login-username_or_email">Username або пошта:</label>
                <input type="text" id="login-username_or_email" name="login_username_or_email" required><br>
                <label for="login-password">Пароль:</label>
                <input type="password" id="login-password" name="login_password" required><br>
                <button type="submit" class="btn btn-primary">Увійти</button>
            </form>
            <p>Ще не маєте акаунта? <a href="#" id="show-register">Зареєструватися</a></p>
        </section>

        <!-- Форма для реєстрації користувача -->
        <section class="register-form" style="display:none;">
            <!-- Реєстрація -->
            <form action="register_process.php" method="post">
                <label for="register-username">Username:</label>
                <input type="text" id="register-username" name="registeration_username" required><br>
                <label for="register-fullname">Повне ім'я:</label>
                <input type="text" id="register-fullname" name="registeration_full_name" required><br>
                <label for="register-email">Email:</label>
                <input type="email" id="register-email" name="register-email" required><br>
                <label for="register-password">Пароль:</label>
                <input type="password" id="register-password" name="registeration_password" required><br>
                <label for="register-confirm">Підтвердіть пароль:</label>
                <input type="password" id="register-confirm" name="registeration_confirm_password" required><br>
                <label for="register-workexp">Стаж роботи (років):</label>
                <input type="number" id="register-workexp" name="registeration_work_experience" min="0" max="100" class="styled-input" >
                <label for="register-description">Опис (чим хочете займатись):</label>
                <textarea id="register-description" name="registeration_description" rows="3" class="styled-input" ></textarea>
                <button type="submit" class="btn btn-primary">Зареєструватися</button>
            </form>
            <p>Вже маєте акаунт? <a href="#" id="show-login">Увійти</a></p>
        </section>
    </section>

    <script src="/cabinet/scripts/login.js"></script>
    <script src="/cabinet/scripts/register.js"></script>
    
</body>
</html>
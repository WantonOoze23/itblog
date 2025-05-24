<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin']) {
    header("Location: /cabinet/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабінет</title>

    <link rel="stylesheet" href="/cabinet/style/cabinet_main_styles.css">
    <link rel="stylesheet" href="/cabinet/style/navigation.css">
</head>
<body>
    <section class="navigation">
        <nav>
            <ul>
                <li><a href="/index.html">Головна</a></li>
                <li><a href="/blog.html">Блог</a></li>
                <li><a href="/contact.html">Контакти</a></li>
                <li class="nav-login-btn" style="margin-left:auto;">
                    <a href="/cabinet/api/logout.php" class="btn-nav-login">Вийти</a>
                </li>
            </ul>
        </nav>
    </section>

    <div class="main-wrapper">
        <section class="left_side_menu">
            <div class="left-menu-block profile-block">
                <h3>Особистий кабінет</h3>
                <ul class="menu-list">
                    <li><b>Імʼя:</b> <span id="profile-fullname">—</span></li>
                    <li id="menu-profile" style="cursor:pointer;"><b>Мій профіль</b></li>
                </ul>
            </div>
            <div class="left-menu-block category-block">
                <h3>Категорії</h3>
                <ul class="menu-list">
                    <li id="menu-categories" style="cursor:pointer;"><b>Перегляд категорій</b></li>
                </ul>
            </div>
            <div class="left-menu-block posts-block">
                <h3>Мої пости</h3>
                <ul id="posts-list" class="menu-list"></ul>
            </div>
        </section>

        <section class="content">

            
        </section>
    </div>
    <button id="fab-add-post" title="Додати пост">+</button>
    

    <script src="/cabinet/scripts/get_info.js"></script>
    <script src="/cabinet/scripts/profile.js"></script>
    <script src="/cabinet/scripts/add_new_post.js"></script>
    <script src="/cabinet/scripts/category.js"></script>
</body>
</html>
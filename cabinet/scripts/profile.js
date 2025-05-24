document.addEventListener('DOMContentLoaded', function () {
    // Підтягуємо ім'я у меню
    fetch('/cabinet/api/get_profile_info.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            const user = data.user;
            document.getElementById('profile-fullname').textContent = user.full_name || user.username || '—';
        });

    // Обробник для "Мій профіль"
    const menuProfile = document.getElementById('menu-profile');
    if (menuProfile) {
        menuProfile.addEventListener('click', function () {
            fetch('/cabinet/api/get_profile_info.php')
                .then(res => res.json())
                .then(data => {
                    const content = document.querySelector('.content');
                    if (!content) return;
                    if (!data.success) {
                        content.innerHTML = '<p style="color:red;">Помилка завантаження профілю</p>';
                        return;
                    }
                    const user = data.user;
                    content.innerHTML = `
                        <div class="profile-view">
                            <h2>Мій профіль</h2>
                            <p><b>Пошта:</b> ${user.email || '—'}</p>
                            <p><b>Логін:</b> ${user.username || '—'}</p>
                            <p><b>Роль:</b> ${user.is_admin ? 'Адмін' : 'Журналіст'}</p>
                            <form id="change-password-form" style="margin-top:15px;max-width:350px;">
                                <input type="password" id="old-password" placeholder="Старий пароль" required style="width:100%;margin-bottom:5px;">
                                <input type="password" id="new-password" placeholder="Новий пароль" required style="width:100%;margin-bottom:5px;">
                                <input type="password" id="confirm-password" placeholder="Підтвердіть новий пароль" required style="width:100%;margin-bottom:5px;">
                                <button type="submit" class="btn btn-primary" style="margin:auto;">Змінити пароль</button>
                                <div id="change-password-message" style="font-size:0.95em;margin-top:5px;"></div>
                            </form>
                        </div>
                    `;
                    addChangePasswordHandler();
                });
        });
    }

    // Додаємо обробник для форми зміни пароля (після рендеру профілю)
    function addChangePasswordHandler() {
        const form = document.getElementById('change-password-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const oldPass = document.getElementById('old-password').value;
                const newPass = document.getElementById('new-password').value;
                const confirmPass = document.getElementById('confirm-password').value;
                const msg = document.getElementById('change-password-message');

                if (newPass.length < 6) {
                    msg.textContent = 'Новий пароль має бути не менше 6 символів.';
                    msg.style.color = 'red';
                    return;
                }
                if (newPass !== confirmPass) {
                    msg.textContent = 'Паролі не співпадають.';
                    msg.style.color = 'red';
                    return;
                }

                fetch('/cabinet/api/change_password.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `old_password=${encodeURIComponent(oldPass)}&new_password=${encodeURIComponent(newPass)}`
                })
                .then(res => res.json())
                .then(data => {
                    msg.textContent = data.message;
                    msg.style.color = data.success ? 'green' : 'red';
                    if (data.success) form.reset();
                })
                .catch(() => {
                    msg.textContent = 'Сталася помилка при з’єднанні з сервером.';
                    msg.style.color = 'red';
                });
            });
        }
    }
});
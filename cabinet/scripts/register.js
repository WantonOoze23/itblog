document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.querySelector('.register-form form');
    if (!registerForm) return;

    registerForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Отримуємо значення полів
        const username = document.getElementById('register-username').value.trim();
        const full_name = document.getElementById('register-fullname').value.trim();
        const password = document.getElementById('register-password').value;
        const confirm = document.getElementById('register-confirm').value;
        const email = document.getElementById('register-email').value.trim();
        const work_experience = document.getElementById('register-workexp').value.trim();
        const description = document.getElementById('register-description').value.trim();

        if (password !== confirm) {
            alert('Паролі не співпадають!');
            return;
        }

        // Формуємо url-encoded рядок
        const body = `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&full_name=${encodeURIComponent(full_name)}&email=${encodeURIComponent(email)}&work_experience=${encodeURIComponent(work_experience)}&description=${encodeURIComponent(description)}`;

        fetch('/cabinet/api/add_new_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Реєстрація успішна! Зачекайте доки адміністратор підтвердить вашу реєстрацію.');
                // Переключаємо на форму входу
                document.getElementById('btn-login').click();
            } else if (data.message && data.message.includes('username або email вже існує')) {
                alert('Користувач з таким username або email вже існує!');
            } else {
                alert(data.message || 'Помилка при реєстрації');
            }
        })
        .catch(() => {
            alert('Сталася помилка при з’єднанні з сервером.');
        });
    });
});
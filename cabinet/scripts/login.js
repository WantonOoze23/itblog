document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.querySelector('.login-form form');

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Отримуємо значення полів
        const usernameOrEmail = document.getElementById('login-username_or_email').value;
        const password = document.getElementById('login-password').value;

        // Формуємо url-encoded рядок
        const body = `login_username_or_email=${encodeURIComponent(usernameOrEmail)}&login_password=${encodeURIComponent(password)}`;

        fetch('/cabinet/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Помилка входу');
            }
        })
        .catch(error => {
            console.error('Помилка при відправці:', error);
            alert('Сталася помилка при з’єднанні з сервером.');
        });
    });

    // Обробка перемикача Вхід / Реєстрація
    const loginFormSection = document.querySelector('.login-form');
    const registerFormSection = document.querySelector('.register-form');
    const btnLogin = document.getElementById('btn-login');
    const btnRegister = document.getElementById('btn-register');

    function showLogin() {
        loginFormSection.style.display = 'block';
        registerFormSection.style.display = 'none';
        btnLogin.classList.add('active');
        btnRegister.classList.remove('active');
    }
    function showRegister() {
        loginFormSection.style.display = 'none';
        registerFormSection.style.display = 'block';
        btnLogin.classList.remove('active');
        btnRegister.classList.add('active');
    }

    btnLogin.addEventListener('click', showLogin);
    btnRegister.addEventListener('click', showRegister);

    const showRegisterLink = document.getElementById('show-register');
    const showLoginLink = document.getElementById('show-login');
    if (showRegisterLink) showRegisterLink.onclick = function (e) { e.preventDefault(); showRegister(); };
    if (showLoginLink) showLoginLink.onclick = function (e) { e.preventDefault(); showLogin(); };
});

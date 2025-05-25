document.addEventListener('DOMContentLoaded', function () {
    // Вивід нових користувачів у меню
    async function loadNewUsers() {
        const res = await fetch('/cabinet/api/get_new_users.php');
        const data = await res.json();
        const list = document.getElementById('new-authors-list');
        if (!list) return;
        list.innerHTML = '';
        if (data.success && data.users.length) {
            data.users.forEach(user => {
                const li = document.createElement('li');
                li.style.cursor = 'pointer';
                li.textContent = user.full_name ? user.full_name : user.username;
                li.dataset.id = user.new_user_id;
                li.onclick = () => showNewUserInfo(user.new_user_id);
                list.appendChild(li);
            });
        } else {
            list.innerHTML = '<li>Немає нових користувачів</li>';
        }
    }

    // Вивід інформації про нового користувача у секцію content
    async function showNewUserInfo(new_user_id) {
        const res = await fetch('/cabinet/api/get_new_user_info.php?new_user_id=' + encodeURIComponent(new_user_id));
        const data = await res.json();
        const content = document.querySelector('.content');
        if (!content) return;
        if (!data.success) {
            content.innerHTML = `<p style="color:red;">${data.message || 'Користувача не знайдено'}</p>`;
            return;
        }
        const user = data.user;
            function getWorkExperienceText(years) {
                years = Number(years);
                if (!years || isNaN(years)) return '—';
                if (years % 10 === 1 && years % 100 !== 11) return `${years} рік`;
                if ([2,3,4].includes(years % 10) && ![12,13,14].includes(years % 100)) return `${years} роки`;
                return `${years} років`;
            }

            content.innerHTML = `
                <div class="user-info-block">
                    <h2>Новий користувач</h2>
                    <p><b>Ім'я:</b> ${user.full_name || '—'}</p>
                    <p><b>Логін:</b> ${user.username}</p>
                    <p><b>Роль (id):</b> ${user.role_id}</p>
                    <p><b>Стаж роботи:</b> ${getWorkExperienceText(user.work_experience)}</p>
                    <p><b>Опис:</b> ${user.description ? user.description : '—'}</p>
                    <div style="margin-top:15px;">
                        <label style="margin-right:10px; width: 100%;">
                            <input type="checkbox" id="make-admin-checkbox"> Зробити адміністратором
                        </label>
                    </div>
                    <div style="margin-right:10px; margin-top:15px; width: 100%;">
                        <button id="approve-user-btn" class="btn btn-primary">Додати до основної бази</button>
                        <button id="delete-user-btn" class="btn btn-danger">Видалити</button>
                    </div>
                    <div id="user-action-message" style="margin-top:10px;font-size:0.97em;"></div>
                </div>
            `;
        document.getElementById('approve-user-btn').onclick = async function () {
            if (!confirm('Додати цього користувача до основної бази?')) return;
            const isAdmin = document.getElementById('make-admin-checkbox').checked ? 1 : 0;
            const res = await fetch('/cabinet/api/approve_new_user.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'new_user_id=' + encodeURIComponent(new_user_id) + '&is_admin=' + isAdmin
            });
            const data = await res.json();
            document.getElementById('user-action-message').textContent = data.message;
            document.getElementById('user-action-message').style.color = data.success ? 'green' : 'red';
            if (data.success) {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        };
        document.getElementById('delete-user-btn').onclick = async function () {
            if (!confirm('Видалити цього користувача?')) return;
            const res = await fetch('/cabinet/api/delete_new_user.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'new_user_id=' + encodeURIComponent(new_user_id)
            });
            const data = await res.json();
            document.getElementById('user-action-message').textContent = data.message;
            document.getElementById('user-action-message').style.color = data.success ? 'green' : 'red';
            if (data.success) {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        };
    }

    loadNewUsers();
});
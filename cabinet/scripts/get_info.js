document.addEventListener('DOMContentLoaded', async function () {
    // Отримати id поточного користувача
    let currentUserId = null;
    try {
        const profileRes = await fetch('/cabinet/api/get_profile_info.php');
        const profileData = await profileRes.json();
        if (profileData.success && profileData.user) {
            currentUserId = profileData.user.user_id;
        }
    } catch (e) {}

    // Вивід постів
    fetch('/cabinet/api/get_all_posts.php')
        .then(res => res.json())
        .then(data => {
            const postsList = document.getElementById('posts-list');
            if (!postsList) return;
            if (!data.success || !data.posts.length) {
                postsList.innerHTML = '<li>Пости відсутні</li>';
                return;
            }
            postsList.innerHTML = '';
            data.posts.forEach(post => {
                const li = document.createElement('li');
                let cats = post.categories && post.categories.length ? ` <span style="color:#888;font-size:0.95em;">[${post.categories.join(', ')}]</span>` : '';
                li.innerHTML = (post.title || `Пост #${post.post_id}`) + cats;
                li.style.cursor = 'pointer';
                li.addEventListener('click', function () {
                    showPostInfo(post.post_id);
                });
                postsList.appendChild(li);
            });
        })
        .catch(() => {
            const postsList = document.getElementById('posts-list');
            if (postsList) postsList.innerHTML = '<li style="color:red;">Помилка завантаження постів</li>';
        });

    // Вивід авторів (тільки якщо є блок)
    const authorsList = document.getElementById('authors-list');
    if (authorsList) {
        fetch('/cabinet/api/get_authors.php')
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.authors.length) {
                    authorsList.innerHTML = '<li>Автори відсутні</li>';
                    return;
                }
                authorsList.innerHTML = '';
                data.authors.forEach(author => {
                    const li = document.createElement('li');
                    li.textContent = author.full_name || author.username || `User #${author.user_id}`;
                    li.style.cursor = 'pointer';
                    li.onclick = () => showAuthorInfo(author.user_id);
                    authorsList.appendChild(li);
                });
            })
            .catch(() => {
                authorsList.innerHTML = '<li style="color:red;">Помилка завантаження авторів</li>';
            });
    }

    // Функція для показу інформації про пост
    window.showPostInfo = function(postId) {
        fetch(`/cabinet/api/get_post.php?post_id=${postId}`)
            .then(res => res.json())
            .then(data => {
                const content = document.querySelector('.content');
                if (!content) return;
                if (!data.success) {
                    content.innerHTML = `<p style="color:red;">${data.message || 'Пост не знайдено'}</p>`;
                    return;
                }
                const post = data.post;
                content.innerHTML = `
                    <div class="post-info">
                        <h2>${post.title || 'Без назви'}</h2>
                        <p><b>Автор:</b> ${post.full_name || 'Невідомо'}</p>
                        <p><b>Дата:</b> ${post.created_at || ''}</p>
                        <p><b>Категорії:</b> ${post.categories && post.categories.length ? post.categories.join(', ') : '—'}</p>
                        <p>${post.description || ''}</p>
                        ${post.image ? `<img src="${post.image}" alt="Зображення поста" style="max-width:100%;">` : ''}
                        <div style="margin-top:15px;">
                            <button id="edit-post-btn" class="btn btn-primary">Редагувати</button>
                            <button id="delete-post-btn" class="btn btn-danger">Видалити</button>
                        </div>
                        <div id="delete-post-message" style="margin-top:10px;font-size:0.97em;"></div>
                    </div>
                `;
                //"Редагувати"
                const editBtn = document.getElementById('edit-post-btn');
                if (editBtn && window.renderPostForm) {
                    editBtn.addEventListener('click', function () {
                        window.renderPostForm(post);
                    });
                }
                // "Видалити"
                const deleteBtn = document.getElementById('delete-post-btn');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function () {
                        if (!confirm('Ви дійсно хочете видалити цей пост?')) return;
                        fetch('/cabinet/api/delete_post.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: `post_id=${encodeURIComponent(post.post_id)}`
                        })
                        .then(res => res.json())
                        .then(data => {
                            const msg = document.getElementById('delete-post-message');
                            msg.textContent = data.message;
                            msg.style.color = data.success ? 'green' : 'red';
                            if (data.success) setTimeout(() => location.reload(), 1000);
                        })
                        .catch(() => {
                            const msg = document.getElementById('delete-post-message');
                            msg.textContent = 'Сталася помилка при з’єднанні з сервером.';
                            msg.style.color = 'red';
                        });
                    });
                }
            })
            .catch(() => {
                const content = document.querySelector('.content');
                if (content) content.innerHTML = '<p style="color:red;">Помилка завантаження поста</p>';
            });
    }

    // Функція для показу інформації про автора та видалення
    window.showAuthorInfo = function(userId) {
    fetch(`/cabinet/api/get_author_info.php?user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            const content = document.querySelector('.content');
            if (!content) return;
            if (!data.success) {
                content.innerHTML = `<p style="color:red;">${data.message || 'Користувача не знайдено'}</p>`;
                return;
            }
            const user = data.user;
            let adminBtnHtml = '';
            if (user.role_id != 1) { // 1 — це адміністратор
                adminBtnHtml = `<button id="make-admin-btn" class="btn btn-primary" style="margin-right:10px;">Зробити адміністратором</button>`;
            }
            let deleteBtnHtml = '';
            if (currentUserId && user.user_id != currentUserId) {
                deleteBtnHtml = `<button id="delete-user-btn" class="btn btn-danger">Видалити користувача</button>`;
            } else {
                deleteBtnHtml = `<div style="color:#888;margin-top:10px;">Ви не можете видалити себе</div>`;
            }
            content.innerHTML = `
                <div class="user-info-block">
                    <h2>Користувач</h2>
                    <p><b>Ім'я:</b> ${user.full_name || '—'}</p>
                    <p><b>Логін:</b> ${user.username}</p>
                    <p><b>Роль:</b> ${user.role_name || user.role_id}</p>
                    <p><b>Пошта:</b> ${user.email}</p>
                    <div style="margin-top:15px;">
                        ${adminBtnHtml}
                        ${deleteBtnHtml}
                    </div>
                    <div id="user-action-message" style="margin-top:10px;font-size:0.97em;"></div>
                </div>
            `;

            if (user.role_id != 1) {
                document.getElementById('make-admin-btn').onclick = async function () {
                    if (!confirm('Зробити цього користувача адміністратором?')) return;
                    const res = await fetch('/cabinet/api/make_admin.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'user_id=' + encodeURIComponent(userId)
                    });
                    const data = await res.json();
                    document.getElementById('user-action-message').textContent = data.message;
                    document.getElementById('user-action-message').style.color = data.success ? 'green' : 'red';
                    if (data.success) {
                        setTimeout(() => window.showAuthorInfo(userId), 700);
                    }
                };
            }

            if (currentUserId && user.user_id != currentUserId) {
                document.getElementById('delete-user-btn').onclick = async function () {
                    if (!confirm('Видалити цього користувача?')) return;
                    const res = await fetch('/cabinet/api/delete_user.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'user_id=' + encodeURIComponent(userId)
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
        });
}
});
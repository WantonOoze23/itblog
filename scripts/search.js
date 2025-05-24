document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    const input = document.getElementById('searchInput');
    const dropdown = document.getElementById('searchDropdown');

    // Live search (випадаюче меню)
    if (input && dropdown) {
        input.addEventListener('input', function() {
            const query = input.value.trim();
            if (!query) {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
                return;
            }
            fetch('api/search_posts.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !data.posts.length) {
                        dropdown.innerHTML = '<div class="dropdown-item">Нічого не знайдено</div>';
                        dropdown.style.display = 'block';
                        return;
                    }
                    dropdown.innerHTML = data.posts.map(post => `
                        <div class="dropdown-item" data-id="${post.post_id}">
                            <b>${post.title}</b><br>
                            <small>${post.description.substring(0, 60)}...</small>
                        </div>
                    `).join('');
                    dropdown.style.display = 'block';
                })
                .catch(() => {
                    dropdown.innerHTML = '<div class="dropdown-item">Помилка пошуку</div>';
                    dropdown.style.display = 'block';
                });
        });

        // Перехід до поста при кліку на результат
        dropdown.addEventListener('click', function(e) {
            const item = e.target.closest('.dropdown-item');
            if (item && item.dataset.id) {
                window.location.href = '/post/post.html?id=' + item.dataset.id;
            }
        });

        // Закриття меню при втраті фокусу
        input.addEventListener('blur', function() {
            setTimeout(() => { dropdown.style.display = 'none'; }, 200);
        });
        input.addEventListener('focus', function() {
            if (dropdown.innerHTML.trim()) dropdown.style.display = 'block';
        });
    }

    // Стандартний пошук по submit
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = input.value.trim();
            if (!query) return;
            if (!window.location.pathname.endsWith('blog.html')) {
                window.location.href = 'blog.html?q=' + encodeURIComponent(query);
                return;
            }
            // AJAX-пошук на blog.html
            fetch('api/search_posts.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    const loader = document.querySelector('.news_loader');
                    if (!loader) return;
                    if (!data.success || !data.posts.length) {
                        loader.innerHTML = '<p>Нічого не знайдено.</p>';
                        return;
                    }
                    loader.innerHTML = data.posts.map(post => `
                        <div class="post" id="post-${post.post_id}">
                            <h3>${post.title}</h3>
                            <p>${post.description}</p>
                            <div><b>Категорії:</b> ${post.categories.join(', ')}</div>
                            <div><i>Автор: ${post.full_name || 'Невідомо'}</i></div>
                            <div><i>Дата: ${post.created_at}</i></div>
                        </div>
                    `).join('');
                })
                .catch(() => {
                    const loader = document.querySelector('.news_loader');
                    if (loader) loader.innerHTML = '<p>Помилка пошуку.</p>';
                });
        });

        // Якщо на blog.html і є ?q=, автоматично виконуємо пошук
        if (window.location.pathname.endsWith('blog.html')) {
            const params = new URLSearchParams(window.location.search);
            const q = params.get('q');
            if (q) {
                input.value = q;
                form.dispatchEvent(new Event('submit'));
            }
        }
    }
});
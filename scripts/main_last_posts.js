document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/get_all_posts.php')
        .then(res => res.json())
        .then(data => {
            const loader = document.querySelector('.news_loader');
            if (!loader) return;

            if (!data.success || !data.posts.length) {
                loader.innerHTML = '<p>Пости відсутні</p>';
                return;
            }

            const posts = data.posts.slice(0, 4);

            let html = '';
            if (posts[0]) {
                html += `
                    <div class="news-featured" style="margin-top: 100px;">
                        <h1 style="text-align: center;">Останні пости</h1>
                        <a href="/post/post.html?id=${posts[0].post_id}" class="post-link">
                            <div class="news-post featured">
                                ${posts[0].image ? `<img src="${posts[0].image}" alt="" style="max-width:100%;">` : ''}
                                <h2>${posts[0].title || 'Без назви'}</h2>
                                <p><b>Автор:</b> ${posts[0].full_name || '—'}</p>
                                <p><b>Дата:</b> ${posts[0].created_at || ''}</p>
                            </div>
                        </a>
                    </div>
                `;
            }

            if (posts.length > 1) {
                html += `<div class="news-row">`;
                posts.slice(1).forEach(post => {
                    html += `
                        <a href="/post/post.html?id=${post.post_id}" class="post-link">
                            <div class="news-post small">
                                ${post.image ? `<img src="${post.image}" alt="" style="max-width:100%;">` : ''}
                                <h3>${post.title || 'Без назви'}</h3>
                                <p><b>Автор:</b> ${post.full_name || '—'}</p>
                                <p><b>Дата:</b> ${post.created_at || ''}</p>
                            </div>
                        </a>
                    `;
                });
                html += `</div>`;
            }

            loader.innerHTML = html;
        })
        .catch(() => {
            const loader = document.querySelector('.news_loader');
            if (loader) loader.innerHTML = '<p style="color:red;">Сталася помилка при з’єднанні з сервером.</p>';
        });
});
document.addEventListener('DOMContentLoaded', function () {

    const params = new URLSearchParams(window.location.search);
    const postId = params.get('id');
    const postSection = document.querySelector('.post');

    if (!postId || !postSection) {
        postSection.innerHTML = '<p style="color:red;">Пост не знайдено</p>';
        return;
    }

    fetch(`/api/get_post.php?id=${encodeURIComponent(postId)}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                postSection.innerHTML = `<p style="color:red;">${data.message || 'Пост не знайдено'}</p>`;
                return;
            }
            const post = data.post;
            postSection.innerHTML = `
                <div class="post">
                    ${post.image ? `<img src="${post.image}" alt="Зображення поста" style="max-width:100%;">` : ''} 
                    <h1>${post.title || 'Без назви'}</h1>
                    <p><b>Автор:</b> ${post.full_name || '—'}</p>
                    <p><b>Дата:</b> ${post.created_at || ''}</p>
                    ${post.categories && post.categories.length ? `<p><b>Категорії:</b> ${post.categories.map(c => `<a href="../blog.html?category=${encodeURIComponent(c)}" class="post-category" style="cursor:pointer;">${c}</a>`).join(' ')}</p>` : ''}
                    <div class="post-description">${post.description}</div>
                </div>
            `;
        })
        .catch(() => {
            const loader = document.querySelector('.post_loader');
            postSection.innerHTML = '<p style="color:red;">Сталася помилка при з’єднанні з сервером.</p>';
        });
});
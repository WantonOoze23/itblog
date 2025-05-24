document.addEventListener('DOMContentLoaded', function () {
    fetch('/cabinet/api/get_authors.php')
    .then(res => res.json())
    .then(data => {
        const authorsList = document.getElementById('authors-list');
        if (!authorsList) return;
        if (!data.success || !data.authors.length) {
            authorsList.innerHTML = '<li>Автори відсутні</li>';
            return;
        }
        authorsList.innerHTML = '';
        data.authors.forEach(author => {
            const li = document.createElement('li');
            li.textContent = author.username || `User #${author.user_id}`;
            authorsList.appendChild(li);
        });
    })
    .catch(() => {
        const authorsList = document.getElementById('authors-list');
        if (authorsList) authorsList.innerHTML = '<li style="color:red;">Помилка завантаження авторів</li>';
    });
});
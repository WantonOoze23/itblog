document.addEventListener('DOMContentLoaded', function () {
    const menuCategories = document.getElementById('menu-categories');
    if (menuCategories) {
        menuCategories.onclick = function () {
            showCategories();
        };
    }

    // Функція для показу категорій 
    window.showCategories = async function () {
        const content = document.querySelector('.content');
        if (!content) return;
        const res = await fetch('/cabinet/api/get_all_categories.php');
        const data = await res.json();
        let html = `
            <div class="category-content-block">
                <h2>Категорії</h2>
                <ul id="category-list" class="menu-list">
                    ${data.categories.map(cat => `
                        <li>
                            <span>${cat.name}</span>
                            <button class="delete-category-btn" data-id="${cat.id}" title="Видалити">×</button>
                        </li>
                    `).join('')}
                </ul>
                <form id="add-category-form">
                    <input type="text" id="new-category-name" placeholder="Нова категорія" required>
                    <button type="submit" class="btn btn-primary">+</button>
                </form>
                <div id="category-message"></div>
            </div>
        `;
        content.innerHTML = html;

        // Видалення категорії
        document.querySelectorAll('.delete-category-btn').forEach(btn => {
            btn.onclick = async function () {
                if (!confirm('Видалити цю категорію?')) return;
                const res = await fetch('/cabinet/api/delete_category.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'category_id=' + encodeURIComponent(btn.dataset.id)
                });
                const data = await res.json();
                document.getElementById('category-message').textContent = data.success ? 'Категорію видалено' : (data.message || 'Помилка при видаленні категорії');
                window.showCategories();
            };
        });

        // Додавання категорії
        document.getElementById('add-category-form').onsubmit = async function (e) {
            e.preventDefault();
            const name = document.getElementById('new-category-name').value.trim();
            if (!name) return;
            const res = await fetch('/cabinet/api/add_new_category.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'name=' + encodeURIComponent(name)
            });
            const data = await res.json();
            const msg = document.getElementById('category-message');
            msg.textContent = data.success ? 'Категорію додано' : (data.message || 'Помилка при додаванні категорії');
            msg.style.color = data.success ? 'green' : 'red';
            if (data.success) {
                document.getElementById('new-category-name').value = '';
                window.showCategories();
            }
        };
    };
});
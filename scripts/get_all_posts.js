document.addEventListener('DOMContentLoaded', async function () {
    const loader = document.querySelector('.news_loader');
    if (!loader) return;

    // Завантажити всі категорії
    const categoriesRes = await fetch('/api/get_all_categories.php');
    const categoriesData = await categoriesRes.json();
    const categories = categoriesData.categories || [];

    // Кастомний дропдаун з чекбоксами
    const filterDiv = document.createElement('div');
    filterDiv.className = 'category-filter';
    filterDiv.style.marginBottom = '18px';
    filterDiv.innerHTML = `
        <div class="dropdown-categories" style="display:inline-block;">
            <button id="dropdown-btn" type="button" style="">
                Обрати категорії <span id="selected-count" style="font-weight:normal;color:#555;"></span> ▼
            </button>
            <div id="dropdown-list" style="display:none;position:absolute;z-index:10;top:110%;left:0;background:#fff;border:1px solid #ccc;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.08);padding:10px 16px;min-width:200px;">
                ${categories.map(cat => `
                    <label style="display:block;margin-bottom:6px;cursor:pointer;">
                        <input type="checkbox" class="cat-checkbox" value="${cat.name}" style="margin-right:8px;">
                        <span class="post-category" style="margin-right:0;">${cat.name}</span>
                    </label>
                `).join('')}
                <button id="reset-categories" type="button" style="margin-top:8px;">Скинути</button>
            </div>
        </div>
    `;
   const categoriesDiv = document.querySelector('.categories');
    if (categoriesDiv) categoriesDiv.appendChild(filterDiv);

    // Відкриття/закриття дропдауну
    const dropdownBtn = filterDiv.querySelector('#dropdown-btn');
    const dropdownList = filterDiv.querySelector('#dropdown-list');
    dropdownBtn.onclick = function () {
        dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
    };
    document.addEventListener('click', function (e) {
        if (!filterDiv.contains(e.target)) dropdownList.style.display = 'none';
    });

    // Завантажити всі пости
    let allPosts = [];
    function renderPosts(posts, selectedCategories = []) {
        let html = '';
        posts
            .filter(post =>
                !selectedCategories.length ||
                (post.categories && post.categories.some(cat => selectedCategories.includes(cat)))
            )
            .forEach(post => {
                html += `
                    <div class="news-post">
                        ${post.image ? `<img src="${post.image}" alt="" style="max-width:100%;">` : ''}
                        <h2>
                            <a href="post/post.html?id=${post.post_id}" class="post-link">
                                ${post.title || 'Без назви'}
                            </a>
                        </h2>
                        <p><b>Автор:</b> ${post.full_name || '—'}</p>
                        <p><b>Дата:</b> ${post.created_at || ''}</p>
                        ${post.categories && post.categories.length ? `<p><b>Категорії:</b> ${post.categories.map(c => `<span class="post-category" data-category="${c}" style="cursor:pointer;">${c}</span>`).join(' ')}</p>` : ''}
                    </div>
                `;
            });
        loader.innerHTML = html || '<p>Пости відсутні</p>';
    }

    fetch('/api/get_all_posts.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.posts.length) {
                loader.innerHTML = '<p>Пости відсутні</p>';
                return;
            }
            allPosts = data.posts;
            renderPosts(allPosts);

            // Обробка вибору чекбоксів
            const checkboxes = filterDiv.querySelectorAll('.cat-checkbox');
            const selectedCount = filterDiv.querySelector('#selected-count');
            function updateFilter() {
                const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
                renderPosts(allPosts, selected);
                selectedCount.textContent = selected.length ? `(${selected.length})` : '';
            }
            checkboxes.forEach(cb => cb.addEventListener('change', updateFilter));

            // Скидання вибору
            filterDiv.querySelector('#reset-categories').onclick = function () {
                checkboxes.forEach(cb => cb.checked = false);
                updateFilter();
            };

            // Клік по категорії в пості
            loader.addEventListener('click', function (e) {
                const cat = e.target.closest('.post-category');
                if (cat) {
                    checkboxes.forEach(cb => cb.checked = (cb.value === cat.dataset.category));
                    updateFilter();
                    dropdownList.style.display = 'block';
                }
            });
        })
        .catch(() => {
            loader.innerHTML = '<p style="color:red;">Сталася помилка при з’єднанні з сервером.</p>';
        });
});
document.addEventListener('DOMContentLoaded', function () {
    const fab = document.getElementById('fab-add-post');
    const content = document.querySelector('.content');

    // Підвантажити всі категорії для селектора
    async function fetchCategories() {
        const res = await fetch('/cabinet/api/get_all_categories.php');
        const data = await res.json();
        return data.categories || [];
    }

    // Відкрити форму додавання поста по FAB
    if (fab && content) {
        fab.addEventListener('click', function () {
            renderPostForm();
        });
    }

    // Глобальна функція для відкриття форми (додавання/редагування)
    window.renderPostForm = async function(post = null) {
        const categories = await fetchCategories();
        const selected = post && post.category_ids ? post.category_ids : [];

        content.innerHTML = `
            <div class="post-form-block">
                <h2>${post ? 'Редагувати пост' : 'Новий пост'}</h2>
                <form id="post-form">
                    <div id="editor-toolbar" style="margin-bottom:8px;">
                        <button type="button" data-cmd="bold" title="Жирний (Ctrl+B)"><b>B</b></button>
                        <button type="button" data-cmd="italic" title="Курсив (Ctrl+I)"><i>I</i></button>
                        <button type="button" data-cmd="underline" title="Підкреслення (Ctrl+U)"><u>U</u></button>
                        <button type="button" data-cmd="formatBlock" data-value="blockquote" title="Цитата">&ldquo;Цитата&rdquo;</button>
                        <button type="button" data-cmd="formatBlock" data-value="pre" title="Код">&lt;/&gt;</button>
                        <button type="button" data-cmd="formatBlock" data-value="h2" title="Заголовок H2">H<sub>2</sub></button>
                        <button type="button" data-cmd="formatBlock" data-value="p" title="Звичайний текст">¶</button>
                    </div>
                    <input type="text" id="post-title" placeholder="Заголовок" value="${post ? post.title : ''}" required style="width:100%;margin-bottom:10px;">
                    <div id="post-description" contenteditable="true" style="">${post ? post.description : ''}</div>
                    <input type="file" id="post-image-file" accept="image/*" style="width:100%;margin-bottom:10px;">
                    ${post && post.image ? `<div id="current-image" style="margin-bottom:10px;"><img src="${post.image}" alt="Поточне зображення" style="max-width:120px;max-height:80px;border:1px solid #ccc;border-radius:4px;"></div>` : ''}
                    <label style="font-weight:500;">Категорії:</label>
                    <div id="post-categories-checkboxes" style="margin-bottom:10px;">
                        ${categories.map(cat => `
                            <label style="margin-right:12px;">
                                <input type="checkbox" value="${cat.id}" ${selected.map(Number).includes(Number(cat.id)) ? 'checked' : ''}>
                                ${cat.name}
                            </label>
                        `).join('')}
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">${post ? 'Зберегти зміни' : 'Додати пост'}</button>
                    <div id="post-form-message" style="margin-top:8px;font-size:0.97em;"></div>
                </form>
                <div style="margin-top:8px;font-size:0.95em;color:#888;">
                    Можна використовувати <b>жирний</b>, <i>курсив</i>, <u>підкреслення</u>, цитати, код, <b>H2</b>, абзаци та <br>.<br>
                    Для форматування виділіть текст і скористайтесь кнопками або комбінаціями клавіш (Ctrl+B, Ctrl+I, Ctrl+U).
                </div>
            </div>
        `;

        // Панель форматування
        const toolbar = document.getElementById('editor-toolbar');
        const editor = document.getElementById('post-description');
        if (toolbar && editor) {
            toolbar.addEventListener('click', function(e) {
                if (e.target.closest('button')) {
                    const btn = e.target.closest('button');
                    const cmd = btn.getAttribute('data-cmd');
                    const value = btn.getAttribute('data-value') || null;
                    editor.focus();
                    if (cmd === 'formatBlock' && value) {
                        document.execCommand(cmd, false, value);
                    } else {
                        document.execCommand(cmd, false, value);
                    }
                    updateToolbarState();
                }
            });
            // Гарячі клавіші
            editor.addEventListener('keydown', function(e) {
                if (e.ctrlKey) {
                    if (e.key === 'b' || e.key === 'B') { e.preventDefault(); document.execCommand('bold'); }
                    if (e.key === 'i' || e.key === 'I') { e.preventDefault(); document.execCommand('italic'); }
                    if (e.key === 'u' || e.key === 'U') { e.preventDefault(); document.execCommand('underline'); }
                    updateToolbarState();
                }
            });
            // Оновлення стану кнопок
            editor.addEventListener('keyup', updateToolbarState);
            editor.addEventListener('mouseup', updateToolbarState);
            editor.addEventListener('focus', updateToolbarState);

            function updateToolbarState() {
                const states = {
                    bold: document.queryCommandState('bold'),
                    italic: document.queryCommandState('italic'),
                    underline: document.queryCommandState('underline'),
                    blockquote: document.queryCommandValue('formatBlock') === 'blockquote',
                    pre: document.queryCommandValue('formatBlock') === 'pre',
                    h2: document.queryCommandValue('formatBlock') === 'h2',
                    p: document.queryCommandValue('formatBlock') === 'p'
                };
                toolbar.querySelectorAll('button').forEach(btn => {
                    const cmd = btn.getAttribute('data-cmd');
                    const value = btn.getAttribute('data-value');
                    btn.classList.remove('active');
                    if (cmd === 'bold' && states.bold) btn.classList.add('active');
                    if (cmd === 'italic' && states.italic) btn.classList.add('active');
                    if (cmd === 'underline' && states.underline) btn.classList.add('active');
                    if (cmd === 'formatBlock' && value === 'blockquote' && states.blockquote) btn.classList.add('active');
                    if (cmd === 'formatBlock' && value === 'pre' && states.pre) btn.classList.add('active');
                    if (cmd === 'formatBlock' && value === 'h2' && states.h2) btn.classList.add('active');
                    if (cmd === 'formatBlock' && value === 'p' && states.p) btn.classList.add('active');
                });
            }
        }

        // Обробка сабміту форми
        document.getElementById('post-form').onsubmit = function(e) {
            e.preventDefault();
            const title = document.getElementById('post-title').value.trim();
            const description = document.getElementById('post-description').innerHTML.trim();
            const imageFile = document.getElementById('post-image-file').files[0];
            const cats = Array.from(document.querySelectorAll('#post-categories-checkboxes input[type="checkbox"]:checked')).map(cb => parseInt(cb.value));
            const msg = document.getElementById('post-form-message');
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description);
            formData.append('categories', JSON.stringify(cats));
            if (post && post.post_id) formData.append('post_id', post.post_id);
            if (imageFile) {
                formData.append('image', imageFile);
            } else if (post && post.image) {
                formData.append('image', post.image); // <-- Додаємо поточний шлях до зображення
            }

            fetch('/cabinet/api/add_new_edit_post.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                msg.textContent = data.message;
                msg.style.color = data.success ? 'green' : 'red';
                if (data.success) setTimeout(() => location.reload(), 1000);
            })
            .catch(() => {
                msg.textContent = 'Сталася помилка при з’єднанні з сервером.';
                msg.style.color = 'red';
            });
        };
    };

    // Глобальна функція для редагування поста
    window.editPost = function(postId) {
        fetch(`/cabinet/api/get_post.php?post_id=${postId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderPostForm(data.post);
                }
            });
    };
});
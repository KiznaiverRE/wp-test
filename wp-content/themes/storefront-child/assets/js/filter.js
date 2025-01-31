document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(form);

        fetch('/wp-admin/admin-post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
            .then(data => {
                console.log(data.data)
                let postsContainer = document.querySelector('.taxonomy-items');
                postsContainer.innerHTML = '';

                data.data.forEach(post => {
                    let postElement = document.createElement('div');
                    postElement.classList.add('tech_item')
                    postElement.innerHTML = `
                    <h2>${post.title}</h2>
                    <p>Бренд: ${post.brand}</p>
                    <p>Цена: ${post.price}</p>
                    <p>Дата выпуска: ${post.release_date}</p>
                    
                    <a class="button" href="${post.link}">Подробнее</a>
                `;
                    postsContainer.appendChild(postElement);
                });
            })

            .catch(error => console.error('Ошибка:', error));
    });
});



//     .then(response => {
//         return response.text(); // Читаем как текст для отладки
//     })
//     .then(data => {
//         console.log(data); // Посмотрим, что именно приходит от сервера
//     })
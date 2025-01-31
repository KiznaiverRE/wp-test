document.addEventListener('DOMContentLoaded', function() {
    console.log('Tech Meta Box JS is loaded!');

    let dateInput = document.querySelector('#tech_release_date');
    if (dateInput){
        dateInput.addEventListener('click', function (e){
            e.target.showPicker();
        })
    }


    // Кнопка "Добавить характеристику"
    const addButton = document.getElementById('add_char_button');
    const container = document.getElementById('tech_characteristics_container');

    if (addButton && container) {
        addButton.addEventListener('click', function() {
            // Считаем количество существующих полей
            const index = container.querySelectorAll('.tech_char_field').length;

            // Создадим новые поля для характеристики
            const newField = document.createElement('div');
            newField.classList.add('tech_char_field');

            newField.innerHTML = `
                <label for="tech_char_name_${index}">Название характеристики:</label>
                <input type="text" id="tech_char_name_${index}" name="tech_char_name[${index}]" value="" style="width:100%; margin-bottom: 10px;" />

                <label for="tech_char_value_${index}">Значение:</label>
                <input type="text" id="tech_char_value_${index}" name="tech_char_value[${index}]" value="" style="width:100%; margin-bottom: 10px;" />

                <button type="button" class="remove_char_button" style="margin-top: 5px;">Удалить характеристику</button>
            `;

            // Добавляем созданное поле в контейнер
            container.appendChild(newField);
        });
    }

    // Удаление характеристики
    container.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove_char_button')) {
            // Удаляем поле, которое содержит кнопку "Удалить"
            const fieldToRemove = e.target.closest('.tech_char_field');
            if (fieldToRemove) {
                container.removeChild(fieldToRemove);
            }
        }
    });
});
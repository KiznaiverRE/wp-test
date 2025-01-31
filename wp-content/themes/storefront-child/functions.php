<?php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
});

// Регистрируем новый тип записей

function register_custom_post_type() {
    register_post_type('tech', [
        'labels'      => [
            'name'          => 'Техника',
            'singular_name' => 'Техника',
            'add_new'       => 'Добавить технику',
            'add_new_item'  => 'Добавить новую технику',
            'edit_item'     => 'Редактировать технику',
            'new_item'      => 'Новая техника',
            'view_item'     => 'Посмотреть технику',
            'search_items'  => 'Найти технику',
            'not_found'     => 'Техника не найдена',
            'menu_name'     => 'Техника'
        ],
        'public'      => true,
        'has_archive' => true,
        'menu_icon'   => 'dashicons-lightbulb',
        'supports'    => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite'     => ['slug' => 'tech'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_custom_post_type');


// Регистрируем таксономии

function register_custom_taxonomies() {
    register_taxonomy('tech_category', 'tech', [
        'labels'        => ['name' => 'Категории', 'singular_name' => 'Категория'],
        'public'        => true,
        'hierarchical'  => true,
        'show_in_rest'  => true,
    ]);
    // Бренды
    register_taxonomy('tech_brand', 'tech', [
        'labels'        => ['name' => 'Бренды', 'singular_name' => 'Бренд'],
        'public'        => true,
        'hierarchical'  => false,
        'show_in_rest'  => true,
    ]);

    // Тип устройства
    register_taxonomy('tech_type', 'tech', [
        'labels'        => ['name' => 'Тип устройства', 'singular_name' => 'Тип устройства'],
        'public'        => true,
        'hierarchical'  => true,
        'show_in_rest'  => true,
    ]);
}

add_action('init', 'register_custom_taxonomies');


// Регистрируем мета поля

function register_tech_meta(){
    register_post_meta('tech', 'tech_price', [
        'type'   => 'number',
        'single' => true,
        'show_in_rest' => true,

    ]);
    register_post_meta('tech', 'tech_price', [
        'type'   => 'string',
        'single' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_tech_meta');


// Добавляем мета поля в админке

function add_tech_meta_boxes() {
    add_meta_box(
        'tech_meta_box',
        'Дополнительные параметры',
        'render_tech_meta_box',
        'tech',
        'normal'
    );

    add_meta_box(
        'tech_char_box',
        'Характеристики',
        'render_tech_char_box',
        'tech',
        'normal'
    );
}

add_action('add_meta_boxes', 'add_tech_meta_boxes');

function render_tech_meta_box() {
    $price = get_post_meta(get_the_ID(), 'tech_price', true);
    $release_date = get_post_meta(get_the_ID(), 'tech_release_date', true);

    wp_nonce_field('save_tech_meta', 'tech_meta_nonce');

    echo '<label for="tech_price">Цена (₽):</label>';
    echo '<input type="number" id="tech_price" name="tech_price" value="' . esc_attr($price) . '" style="width:100%; margin-bottom: 20px;" />';

    echo '<label for="tech_release_date">Дата выпуска:</label>';
    echo '<input type="date" id="tech_release_date" name="tech_release_date" value="' . esc_attr($release_date) . '" style="width:100%; margin-bottom: 20px;" />';
}

function render_tech_char_box(){
    $tech_chars = get_post_meta(get_the_ID(), 'tech_characteristics', true);

    if (empty($tech_chars)){
        $tech_chars = [];
    }


    echo '<div id="tech_characteristics_container">';
        foreach ($tech_chars as $key => $char) {
            echo '<div class="tech_char_field">';
                echo '<label for="tech_char_name_' . $key . '">Название характеристики:</label>';
                echo '<input type="text" id="tech_char_name_' . $key . '" name="tech_char_name[' . $key . ']" value="' . esc_attr($char['name']) . '" style="width:100%; margin-bottom: 10px;" />';

                echo '<label for="tech_char_value_' . $key . '">Значение:</label>';
                echo '<input type="text" id="tech_char_value_' . $key . '" name="tech_char_value[' . $key . ']" value="' . esc_attr($char['value']) . '" style="width:100%; margin-bottom: 10px;" />';
                echo '<button class="button-cancel" type="button" class="remove_char_button" style="margin-top: 5px;">Удалить характеристику</button>';
            echo '</div>';
        }
    echo '</div>';

    echo '<button type="button" id="add_char_button" style="margin-top: 10px;">Добавить характеристику</button>';

    wp_nonce_field('save_tech_meta', 'tech_char_meta_nonce');
}

function save_tech_meta($post_id) {
    if (!isset($_POST['tech_meta_nonce']) || !wp_verify_nonce($_POST['tech_meta_nonce'], 'save_tech_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['tech_price'])) {
        update_post_meta($post_id, 'tech_price', sanitize_text_field($_POST['tech_price']));
    }

    if (isset($_POST['tech_release_date'])) {
        update_post_meta($post_id, 'tech_release_date', sanitize_text_field($_POST['tech_release_date']));
    }

    if (isset($_POST['tech_char_name']) && isset($_POST['tech_char_value'])){
        $tech_chars = [];
        $names = $_POST['tech_char_name'];
        $values = $_POST['tech_char_value'];

        foreach ($names as $key => $name) {
            $tech_chars[] = [
                'name'  => sanitize_text_field($name),
                'value' => sanitize_text_field($values[$key])
            ];
        }

        update_post_meta($post_id, 'tech_characteristics', $tech_chars);
    }

    return $post_id;
}
add_action('save_post', 'save_tech_meta');


function add_tech_details_to_content($content) {
    if (is_singular('tech')) { // Только для кастомного типа поста "tech"

        $brand = get_the_term_list(get_the_ID(), 'tech_brand', '', ', ', '');

        $price = get_post_meta(get_the_ID(), 'tech_price', true);
        $release_date = get_post_meta(get_the_ID(), 'tech_release_date', true);

        $tech_chars = get_post_meta(get_the_ID(), 'tech_characteristics', true);

        $tech_details = '<div class="tech-details">';

        if ($brand) {
            $tech_details .= '<p><strong>Бренд:</strong> ' . $brand . '</p>';
        }

        if ($price) {
            $tech_details .= '<p><strong>Цена:</strong> ' . esc_html($price) . ' BYN</p>';
        }

        if ($release_date) {
            $tech_details .= '<p><strong>Дата выпуска:</strong> ' . esc_html($release_date) . '</p>';
        }

        // Характеристики
        if (!empty($tech_chars) && is_array($tech_chars)) {
            $tech_details .= '<h3>Характеристики:</h3><ul>';
            foreach ($tech_chars as $char) {
                $tech_details .= '<li><strong>' . esc_html($char['name']) . ':</strong> ' . esc_html($char['value']) . '</li>';
            }
            $tech_details .= '</ul>';
        }

        $tech_details .= '</div>';

        // Добавляем к контенту
        return $content . $tech_details;
    }
    return $content;
}

add_filter('the_content', 'add_tech_details_to_content');



function display_filter_form() {
    // Подключаем форму из отдельного файла
    get_template_part('filter-form');
}
add_action('display_filter_form', 'display_filter_form');



function filter_tech_posts() {
    error_log( 'Hello World!' );
    // Проверяем, что запрос пришёл через POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die('Ошибка запроса');
    }

    // Получаем параметры из POST-запроса
    $price_from = isset($_POST['price_from']) ? $_POST['price_from'] : '';
    $price_to = isset($_POST['price_to']) ? $_POST['price_to'] : '';
    $release_date_from = isset($_POST['release_date_from']) ? $_POST['release_date_from'] : '';
    $release_date_to = isset($_POST['release_date_to']) ? $_POST['release_date_to'] : '';
    $tech_category = isset($_POST['tech_category']) ? $_POST['tech_category'] : [];
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : '';

    // Параметры WP_Query
    $args = array(
        'post_type' => 'tech',
        'posts_per_page' => -1, // Выводим все посты (можно ограничить, если нужно)
        'tax_query' => array(),
        'meta_query' => array(),
        'orderby' => 'date', // По умолчанию сортируем по дате
        'order' => 'DESC', // По убыванию по умолчанию
    );

    // Фильтрация по цене
    if ($price_from) {
        $args['meta_query'][] = array(
            'key' => 'tech_price',
            'value' => $price_from,
            'compare' => '>=',
            'type' => 'NUMERIC'
        );
    }

    if ($price_to) {
        $args['meta_query'][] = array(
            'key' => 'tech_price',
            'value' => $price_to,
            'compare' => '<=',
            'type' => 'NUMERIC'
        );
    }

    // Фильтрация по дате выпуска
    if ($release_date_from) {
        $args['meta_query'][] = array(
            'key' => 'tech_release_date',
            'value' => $release_date_from,
            'compare' => '>=',
            'type' => 'DATE'
        );
    }

    if ($release_date_to) {
        $args['meta_query'][] = array(
            'key' => 'tech_release_date',
            'value' => $release_date_to,
            'compare' => '<=',
            'type' => 'DATE'
        );
    }

    // Фильтрация по категориям
    if (!empty($tech_category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'tech_category',
                'field'    => 'term_id',
                'terms'    => $tech_category,
                'operator' => 'IN',
            ),
        );
    }



    // Сортировка
    if ($sort_by) {
        switch ($sort_by) {
            case 'price_asc':
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                $args['meta_key'] = 'tech_price';
                break;
            case 'price_desc':
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                $args['meta_key'] = 'tech_price';
                break;
            case 'release_date_asc':
                $args['orderby'] = 'meta_value';
                $args['order'] = 'ASC';
                $args['meta_key'] = 'tech_release_date';
                break;
            case 'release_date_desc':
                $args['orderby'] = 'meta_value';
                $args['order'] = 'DESC';
                $args['meta_key'] = 'tech_release_date';
                break;
        }
    }

    // Запрос
    $query = new WP_Query($args);

    error_log( 'SECOND!' );

    // Собираем посты в массив для отправки на фронт
    $posts = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = array(
                'title' => get_the_title(),
                'link' => get_permalink(),
                'price' => get_post_meta(get_the_ID(), 'tech_price', true),
                'release_date' => get_post_meta(get_the_ID(), 'tech_release_date', true),
                'brand' => get_the_term_list(get_the_ID(), 'tech_brand', '', ', ', ''),
            );
        }
    }

//    echo '<pre>';
    print_r('sdgdgdfgdfgdf');
    print_r($posts);

//    echo '</pre>';
    exit;

    // Возвращаем результат в формате JSON
    wp_send_json_success($posts); // Возвращает успешный ответ с данными
}

// Регистрируем обработчик для обработки запроса
add_action('admin_post_nopriv_filter_tech_posts', 'filter_tech_posts');
add_action('admin_post_filter_tech_posts', 'filter_tech_posts');


function my_theme_enqueue_scripts() {

    // Подключаем наш JS файл
    wp_enqueue_script(
        'filter-js', // уникальное название скрипта
        get_stylesheet_directory_uri() . '/assets/js/filter.js', // путь к файлу
        array('jquery'), // зависимости от других скриптов (если нужно)
        null,
        true
    );

    // Локализуем параметры для скрипта
    wp_localize_script('filter-js', 'ajax_url', [admin_url('admin-post.php')]);
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');


//Скрипты для админки

function custom_admin_scripts() {
    if (is_admin()){
        ?>

        <script type="text/javascript">
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
        </script>

        <?php
    }
}

add_action('admin_footer', 'custom_admin_scripts');
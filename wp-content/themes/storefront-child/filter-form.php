<form id="tech-filters" method="POST">
    <input type="hidden" name="action" value="filter_tech_posts">
    <p>
        <label for="price_from">Цена от:</label>
        <input type="number" id="price_from" name="price_from" value="<?php echo isset($_POST['price_from']) ? esc_attr($_POST['price_from']) : ''; ?>" />
    </p>

    <p>
        <label for="price_to">Цена до:</label>
        <input type="number" id="price_to" name="price_to" value="<?php echo isset($_POST['price_to']) ? esc_attr($_POST['price_to']) : ''; ?>" />
    </p>

    <p>
        <label for="release_date_from">Дата выпуска от:</label>
        <input type="date" id="release_date_from" name="release_date_from" value="<?php echo isset($_POST['release_date_from']) ? esc_attr($_POST['release_date_from']) : ''; ?>" />
    </p>

    <p>
        <label for="release_date_to">Дата выпуска до:</label>
        <input type="date" id="release_date_to" name="release_date_to" value="<?php echo isset($_POST['release_date_to']) ? esc_attr($_POST['release_date_to']) : ''; ?>" />
    </p>

    <p>
        <h4>Категории</h4>
        <?php
        $terms = get_terms(array('taxonomy' => 'tech_category', 'hide_empty' => false));
        foreach ($terms as $term) {
            $checked = isset($_POST['tech_category']) && in_array($term->term_id, (array)$_POST['tech_category']);
            echo '<label><input type="checkbox" name="tech_category[]" value="' . esc_attr($term->term_id) . '" ' . ($checked ? 'checked' : '') . '> ' . esc_html($term->name) . '</label><br>';
        }
        ?>
    </p>


    <input type="submit" value="Применить фильтры" />
</form>
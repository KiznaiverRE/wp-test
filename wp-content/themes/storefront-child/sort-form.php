<form style="text-align: end; margin-bottom: 3rem" id="tech-sort" method="POST">
    <input type="hidden" name="action" value="filter_tech_posts">

    <label for="sort_by">Сортировать по:</label>
    <select id="sort_by" name="sort_by">
        <?php
        $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : '';
        ?>

        <option value="" <?php selected($sort_by, ''); ?>>Не выбрано</option>
        <option value="price_asc" <?php selected($sort_by, 'price_asc'); ?>>Цена: от меньшего</option>
        <option value="price_desc" <?php selected($sort_by, 'price_desc'); ?>>Цена: от большего</option>
        <option value="release_date_asc" <?php selected($sort_by, 'release_date_asc'); ?>>Дата выпуска: от старого</option>
        <option value="release_date_desc" <?php selected($sort_by, 'release_date_desc'); ?>>Дата выпуска: от нового</option>
    </select>

    <input type="submit" value="Сортировать" />
</form>
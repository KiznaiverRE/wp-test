<?= get_header(); ?>

<?php ?>

<div class="taxonomy-page">
    <h1><?php single_term_title(); ?></h1>
</div>


<?php
get_template_part('sort-form');

print_r($_POST)
?>


<div style="display: flex; justify-content: space-between" class="taxonomy-content">
    <div style="margin-right: 5rem" class="taxonomy-filter">
        <?php
        get_template_part('filter-form');
        ?>
    </div>

    <div style="display: flex; flex-wrap: wrap" class="taxonomy-items">
        <?php
        if (have_posts()){
            while (have_posts()) : the_post();
                ?>
                <div style="margin: 1%; padding: 2%; width: 31%; border: 1px solid #ccc" class="tech_item">
                    <h2><?php the_title() ?></h2>
                    <p>Бренд: <?php the_terms(get_the_ID(), 'tech_brand') ?> </p>

                    <?php
                    $price = get_post_meta(get_the_ID(), 'tech_price', true);
                    $release_date = get_post_meta(get_the_ID(), 'tech_release_date', true);

                    echo '<p>Цена: ' . esc_html($price) . ' BYN</p>';
                    echo '<p>Дата выпуска: ' . esc_html($release_date) . '</p>';

                    ?>
                    <a class="button" href="<?= get_permalink() ?>">Подробнее</a>
                </div>

            <?php
            endwhile;
        }

        ?>
    </div>
</div>

<?= get_footer(); ?>
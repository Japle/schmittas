<div id="products">
    <?php if ( function_exists( 'pagseguro_wp_ecommerce_filter_products' ) and is_home() ) pagseguro_wp_ecommerce_filter_products(); ?>
    <?php if ( have_posts() ) : ?>

    <?php $i = 1; while( have_posts() ) : the_post(); ?>

    <div class="grid_3 product<?php echo ( $i%3 == 0 ) ? ' omega' : ''; ?><?php echo ( $i == 1 or $i%4 == 0 ) ? ' alpha' : ''; ?>">
        <h3 class="product-category"><?php the_category(); ?></h3>
        <?php if ( function_exists( 'the_post_thumbnail' ) and has_post_thumbnail() ) : ?>
        <div class="product-image">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                <?php the_post_thumbnail(); ?>
            </a>
        </div>
        <?php else : ?>
        <div class="product-image">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                <img src="<?php bloginfo( 'template_url' ); ?>/assets/images/no-image.png" width="150" height="150" alt="" />
            </a>
        </div>
        <?php endif; ?>
        <h2 class="product-title">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>
    </div>

    <?php if ( $i%3 == 0 ) : ?>
    <hr class="clear" />
    <?php endif; ?>

    <?php $i++; endwhile; ?>

    <?php else : ?>

    <p class="no-product spacer">Nenhum produto encontrado.</p>

    <?php endif; ?>
</div>
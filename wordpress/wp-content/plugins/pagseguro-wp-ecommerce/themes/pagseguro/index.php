<?php get_header(); ?>

<div id="content" class="grid_9">
        
    <h1 class="title-section">Destaques</h1>

    <div id="products">
        <?php //if ( function_exists( 'pagseguro_wp_ecommerce_filter_products' ) ) pagseguro_wp_ecommerce_filter_products(); ?>
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

        <p class="no-product">Nenhum produto encontrado.</p>

        <?php endif; ?>
    </div>

</div><!-- / content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
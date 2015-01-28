<?php get_header(); ?>

<div id="content" class="grid_9">

    <?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>
    <h2 class="title-section"><?php the_category( ' &bull; ' ); ?></h2>
    
    <div id="product-metadata" class="grid_9">

        <h1 class="spacer"><?php the_title(); ?></h1>

        <div id="product-details-images" class="grid_3 alpha">
            <div class="spacer">
                <?php if ( function_exists( 'the_post_thumbnail' ) and has_post_thumbnail() ) : ?>
                <div class="product-image">
                    <?php the_post_thumbnail(); ?>
                </div>
                <?php else : ?>
                <div class="product-image">
                    <img src="<?php bloginfo( 'template_url' ); ?>/assets/images/no-image.png" width="150" height="150" alt="" />
                </div>
                <?php endif; ?>
                <?php //echo do_shortcode('[gallery columns="0" link="file" itemtag="div" icontag="p" captiontag="p"]'); ?>
            </div>
        </div><!-- / product-details-images -->

        <div id="product-details-metadata" class="grid_6 omega">
            <div class="spacer">
                <p class="product-price">R$ <?php if ( function_exists( 'pagseguro_wp_ecommerce_the_price' ) ) pagseguro_wp_ecommerce_the_price(); ?></p>
                <p class="product-buy-button"><?php if ( function_exists( 'pagseguro_wp_ecommerce_the_buy_button' ) ) pagseguro_wp_ecommerce_the_buy_button(); ?></p>
                <p class="product-code">Código do produto: <?php if ( function_exists( 'pagseguro_wp_ecommerce_the_code' ) ) pagseguro_wp_ecommerce_the_code(); ?></p>
                <p class="product-delivery">Prazo de entrega: <?php if ( function_exists( 'pagseguro_wp_ecommerce_the_delivery_information' ) ) pagseguro_wp_ecommerce_the_delivery_information(); ?></p>
                <p class="product-tags"><?php the_tags(); ?></p>
            </div>
        </div><!-- / product-details-metadata -->

    </div>

    <div id="product-details" class="grid_9">
        <div class="post spacer">
            <h3>Detalhes do produto</h3>
            <?php the_content(); ?>
        </div>
    </div>

    <div id="product-comments" class="grid_9">
        <div class="spacer">
            <h3>Opinião dos clientes sobre o produto</h3>
            <?php comments_template(); ?>
        </div>
    </div><!-- / product-comments -->


    <?php endwhile; endif; ?>
    
</div><!-- / content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
<div id="sidebar" class="grid_3">
    <div class="spacer">

        <h2>Meu Carrinho</h2>
        <p><a href="<?php echo get_permalink( get_option( 'pagseguro_wp_ecommerce_shopping_cart_page_id' ) ); ?>"><?php echo pagseguro_wp_ecommerce_get_total_products_in_shopping_cart(); ?> produtos.</a></p>
        
        <h2>Categorias</h2>
        <ul>
            <?php wp_list_categories( 'title_li=&hide_empty=0&show_count=2&child_of=' . get_option( 'pagseguro_wp_ecommerce_products_category_id' ) ); ?>
        </ul>

    </div>
</div>
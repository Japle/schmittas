<div class="wrap">

    <h2><?php _e( 'PagSeguro WP e-Commerce - WordPress options', 'pagseguro-wp-ecommerce' ); ?></h2>

    <?php if ( isset( $_GET['updated'] ) ) : ?>
    <div id="message" class="updated fade">
        <p><strong><?php _e( 'The WordPress options have been saved.', 'pagseguro-wp-ecommerce' ); ?></strong></p>
    </div>
    <?php endif; ?>

    <form method="post" action="options.php">
        
        <h3><?php _e( 'Products category', 'pagseguro-wp-ecommerce' ); ?></h3>
        <p><?php _e( "To select your posts defined like a product, PagSeguro WP e-Commerce uses the categories to find them. Define the categories you'll use to yours products. The categories child will be considered too.", "pagseguro-wp-ecommerce" ); ?></p>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Products category', 'pagseguro-wp-ecommerce' ); ?></th>
                <td>
                    <?php wp_dropdown_categories( 'hierarchical=1&hide_empty=0&show_count=1&name=pagseguro_wp_ecommerce_products_category_id&selected=' . $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_products_category_id ); ?>
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Shopping cart page', 'pagseguro-wp-ecommerce' ); ?></h3>
        <p><?php _e( 'Define the page where yours users will manage the purchases. Use the shortcode <code>[pagseguro-wp-ecommerce-cart]</code> to show the PagSeguro WP e-Commerce shopping cart.', 'pagseguro-wp-ecommerce' ); ?></p>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Shopping cart page', 'pagseguro-wp-ecommerce' ); ?></th>
                <td>
                    <?php wp_dropdown_pages( 'name=pagseguro_wp_ecommerce_shopping_cart_page_id&selected=' . $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_shopping_cart_page_id ); ?>
                </td>
            </tr>
        </table>

        <!--<h3><?php _e( 'Confirmation page', 'pagseguro-wp-ecommerce' ); ?></h3>
        <p><?php _e( 'Define the confirmation page where the visitors will be redirected after the purchase. You need to inform the URL of this page in PagSeguro configurations.', 'pagseguro-wp-ecommerce' ); ?></p>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Confirmation page', 'pagseguro-wp-ecommerce' ); ?></th>
                <td>
                    <?php wp_dropdown_pages( 'name=pagseguro_wp_ecommerce_confirmation_page_id&selected=' . $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_confirmation_page_id ); ?>
                </td>
            </tr>
        </table>-->

        <h3><?php _e( 'Buy button text', 'pagseguro-wp-ecommerce' ); ?></h3>
        <p><?php _e( "Define the text used in the buy button text. This button is used to yours users add the products into shopping cart.", "pagseguro-wp-ecommerce" ); ?></p>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Buy button text', 'pagseguro-wp-ecommerce' ); ?></th>
                <td>
                    <input type="text" class="regular-text" name="pagseguro_wp_ecommerce_buy_button_text" id="pagseguro_wp_ecommerce_buy_button_text" value="<?php echo $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_buy_button_text; ?>" />
                </td>
            </tr>
        </table>
    
        <p class="submit">
            <input type="submit" class="button-primary" name="submit" value="<?php _e( 'Save the WordPress options', 'pagseguro-wp-ecommerce' ); ?>" />
        </p>

        <?php settings_fields( 'pagseguro-wp-ecommerce-wp' ); ?>
        
    </form>
    
</div>
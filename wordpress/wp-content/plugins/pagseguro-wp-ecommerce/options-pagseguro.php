<div class="wrap">

    <h2><?php _e( 'PagSeguro WP e-Commerce - PagSeguro options', 'pagseguro-wp-ecommerce' ); ?></h2>

    <?php if ( isset( $_GET['updated'] ) ) : ?>
    <div id="message" class="updated fade">
        <p><strong><?php _e( 'The PagSeguro options have been saved.', 'pagseguro-wp-ecommerce' ); ?></strong></p>
    </div>
    <?php endif; ?>

    <form method="post" action="options.php">

        <h3><?php _e( 'E-mail charging', 'pagseguro-wp-ecommerce' ); ?></h3>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'E-mail charging', 'pagseguro-wp-ecommerce' ); ?></th>
                <td>
                    <input type="text" class="regular-text" name="pagseguro_wp_ecommerce_email_charging" id="pagseguro_wp_ecommerce_email_charging" value="<?php echo $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_email_charging; ?>" />
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Shipping method', 'pagseguro-wp-ecommerce' ); ?></h3>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Shipping method', 'pagseguro-wp-ecommerce' ); ?></th>
                <td>
                    <select name="pagseguro_wp_ecommerce_shipping_method" id="pagseguro_wp_ecommerce_shipping_method">
                        <option value="weight"<?php echo ( 'weight' == $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_shipping_method ) ? ' selected="selected"' : ''; ?>><?php _e( 'Freight by weight (calculated by PagSeguro)',  'pagseguro-wp-ecommerce' ); ?></option>
                        <option value="fixed"<?php echo ( 'fixed' == $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_shipping_method ) ? ' selected="selected"' : ''; ?>><?php _e( 'Freight fixed', 'pagseguro-wp-ecommerce' ); ?></option>
                        <option value="free"<?php echo ( 'free' == $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_shipping_method ) ? ' selected="selected"' : ''; ?>><?php _e( 'Freight FREE',  'pagseguro-wp-ecommerce' ); ?></option>
                    </select>
                    <div id="pagseguro_wp_ecommerce_freight_by_weight_options" class="pgwpe-options-complement">
                        <p>
                            <?php _e( 'Your Zip Code', 'pagseguro-wp-ecommerce' ); ?> <input type="text" id="pagseguro_wp_ecommerce_freight_zip_code" name="pagseguro_wp_ecommerce_freight_zip_code" value="<?php echo $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_freight_zip_code; ?>" />
                            <?php _e( 'Define a freight fixed to be used if the calculation fails. R$ ', 'pagseguro-wp-ecommerce' ); ?> <input type="text" id="pagseguro_wp_ecommerce_freight_fixed_price_pgs_nok" name="pagseguro_wp_ecommerce_freight_fixed_price_pgs_nok" value="<?php echo $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_freight_fixed_price_pgs_nok; ?>" />
                        </p>
                    </div>
                    <div id="pagseguro_wp_ecommerce_freight_fixed_options" class="pgwpe-options-complement">
                        R$ <input type="text" id="pagseguro_wp_ecommerce_freight_fixed_price" name="pagseguro_wp_ecommerce_freight_fixed_price" value="<?php echo $pagseguro_wp_ecommerce->pagseguro_wp_ecommerce_freight_fixed_price; ?>" />
                        <?php _e( 'per product.', 'pagseguro-wp-ecommerce' ); ?>
                    </div>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" class="button-primary" name="submit" value="<?php _e( 'Save the PagSeguro options', 'pagseguro-wp-ecommerce' ); ?>" />
        </p>

        <?php settings_fields( 'pagseguro-wp-ecommerce-pagseguro' ); ?>
        
    </form>
    
</div>
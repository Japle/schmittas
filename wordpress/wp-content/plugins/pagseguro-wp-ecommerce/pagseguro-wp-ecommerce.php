<?php
/*
Plugin Name: PagSeguro WP e-Commerce
Plugin URI: http://apiki.com/pagseguro/
Description: Transforme uma instalação do WordPress numa loja virtual integrada ao PagSeguro
Author: Apiki
Version: 1.0
Author URI: http://apiki.com/
*/

/**
 * PagSeguro WP e-Commerce
 *
 * @author Apiki
 * @version 1.0
 * @license GPL
 */
class Pagseguro_Wp_Ecommerce {

    /**
     * Define the PagSeguro WP e-Commerce version
     *
     * @var string
     */
    var $_version = '1.0';

    /**
     * Define the required version of WordPress
     *
     * @var string
     */
    var $_required_wp_version = '2.9';

    /**
     * Store WordPress options
     *
     * @var array
     */
    var $default_options_wp = array();

    /**
     * Store PagSeguro options
     * 
     * @var array
     */
    var $default_options_pagseguro = array();

    /**
     * Store the pages used by PagSeguro WP e-Commerce
     * 
     * @var array
     */
    var $_pagseguro_wp_ecommerce_pages;

    /**
     * Store the products in the shopping cart
     * 
     * @var object
     */
    var $products_in_cart;

    /**
     * Store an instance of Pagseguro_Wp_Ecommerce_Cart class
     * 
     * @var object
     */
    var $pagseguro_wp_ecommerce_obj_cart;
    
    /**
     * Construct method
     * 
     * @global object $wpdb WordPress Database object
     */
    function Pagseguro_Wp_Ecommerce()
    {
        global $wpdb;
        
        $wpdb->pagseguro_wp_ecommerce_table_shopping_cart = $wpdb->prefix . 'pagseguro_wp_ecommerce_shopping_cart';

        include_once dirname( __FILE__ ) . '/classes/Pagseguro_Wp_Ecommerce_Cart.php';
        $this->pagseguro_wp_ecommerce_obj_cart = new Pagseguro_Wp_Ecommerce_Cart();

        add_action( 'activate_pagseguro-wp-ecommerce/pagseguro-wp-ecommerce.php', array( &$this, '_install' ) );
        add_action( 'deactivate_pagseguro-wp-ecommerce/pagseguro-wp-ecommerce.php', array( &$this, 'deactive' ) );

        $this->_set_default_options();

        add_action( 'init', array( &$this, 'i18n' ) );
        //add_action( 'init', array( &$this, 'themes' ) );

        /* Force refresh theme roots. */
	delete_site_transient( 'theme_roots' );
        switch_theme( 'pagseguro', 'pagseguro' );
        
        //$this->themes();
        add_action( 'init', array( &$this, '_observe_params' ) );
        $this->_handle_ajax();
        add_action( 'init', array( &$this, 'get_products_in_shopping_cart' ) );
        
        add_action( 'admin_init', array( &$this, 'register_settings' ) );

        add_action( 'admin_menu', array( &$this, 'menu' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_css' ) );
        add_action( 'wp_print_styles', array( &$this, 'css' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_js' ) );
        add_action( 'wp_print_scripts', array( &$this, 'js' ) );
        add_action( 'admin_notices', array( &$this, 'alert_required_settings' ) );
        add_action( 'admin_notices', array( &$this, 'alert_required_wp_version' ) );

        add_shortcode( 'pagseguro-wp-shopping-cart', array( &$this, 'shortcode' ) );
        add_shortcode( 'pagseguro-wp-ecommerce-cart', array( &$this, 'shortcode' ) );

        add_action( 'do_meta_boxes', array( &$this, 'add_meta_box' ), 10, 3 );
        add_action( 'save_post', array(&$this, 'save_post_meta'), 11, 2);
        add_action( 'publish_post', array(&$this, 'save_post_meta'), 11, 2);
    }

    /**
     * Function executed when this plugin is installed.
     *
     * The following actions is performed:
     *
     * - Add "manage_pagseguro_wp_ecommerce" capability to administrator role.
     * - Add the default WordPress and PagSeguro options.
     * - Create, if not exists, the [prefix]pagseguro_wp_ecommerce_shopping_cart
     * database table.
     *
     * @global object $wpdb WordPress database object
     */
    function _install()
    {
        global $wpdb;
        
	$role = get_role( 'administrator' );
	if ( !$role->has_cap( 'manage_pagseguro_wp_ecommerce' ) )
            $role->add_cap( 'manage_pagseguro_wp_ecommerce' );

        foreach( $this->default_options_wp as $option_name => $option_value )
            add_option( $option_name, $option_value );

        foreach( $this->default_options_pagseguro as $option_name => $option_value )
            add_option( $option_name, $option_value );
        
        $charset_collate = '';
        if( $wpdb->supports_collation() ) :
            if( !empty( $wpdb->charset ) ) :
                    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            endif;
            if( !empty( $wpdb->collate ) ) :
                    $charset_collate .= " COLLATE $wpdb->collate";
            endif;
        endif;

        $sql_to_create_table = "CREATE TABLE IF NOT EXISTS $wpdb->pagseguro_wp_ecommerce_table_shopping_cart (".
        "cart_id int(11) NOT NULL auto_increment,".
        "cart_post_id bigint(20) NOT NULL,".
        "cart_cookie_id varchar(40) NOT NULL,".
        "cart_quantity tinyint(3) NOT NULL,".
        "cart_registered_at TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,".
        "PRIMARY KEY (cart_id)) $charset_collate;";

        include_once ABSPATH . '/wp-admin/includes/upgrade.php';
        dbDelta( $sql_to_create_table );
    }

    /**
     * This function defines the WordPress and PagSeguro options and it's default
     * values.
     *
     * The options is setted like property of this object and the values are
     * defined in agreement with default value or the final user value.
     */
    function _set_default_options()
    {
        $this->default_options_wp = array(
            'pagseguro_wp_ecommerce_set_themes' => 1,
            'pagseguro_wp_ecommerce_products_category_id' => 1,
            'pagseguro_wp_ecommerce_shopping_cart_page_id' => 2,
            'pagseguro_wp_ecommerce_confirmation_page_id' => 2,
            'pagseguro_wp_ecommerce_buy_button_text' => 'Buy it!'
        );

        $this->default_options_pagseguro = array(
            'pagseguro_wp_ecommerce_email_charging' => '',
            'pagseguro_wp_ecommerce_shipping_method' => 'fixo',
            'pagseguro_wp_ecommerce_freight_fixed_price' => '',
            'pagseguro_wp_ecommerce_freight_fixed_price_pgs_nok' => '',
            'pagseguro_wp_ecommerce_freight_zip_code' => ''
        );

        foreach( $this->default_options_wp as $option_name => $option_value )
            $this->{$option_name} = ( get_option( $option_name ) ) ? get_option( $option_name ) : $option_value;

        foreach( $this->default_options_pagseguro as $option_name => $option_value )
            $this->{$option_name} = ( get_option( $option_name ) ) ? get_option( $option_name ) : $option_value;
    }

    /**
     * Register the two group of options. One to WordPress and other to PagSeguro.
     * 
     */
    function register_settings()
    {
        foreach( $this->default_options_wp as $option_name => $option_value )
            register_setting( 'pagseguro-wp-ecommerce-wp', $option_name );

        foreach( $this->default_options_pagseguro as $option_name => $option_value )
            register_setting( 'pagseguro-wp-ecommerce-pagseguro', $option_name );
    }

    /**
     * 
     */
    function deactive()
    {
        if ( $this->_is_wp_3() )
            switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
        else
            switch_theme( 'default', 'default' );
    }
    
    /**
     * Loads the textdomain used by iMasters WP Budget
     */
    function i18n()
    {
        load_plugin_textdomain( 'pagseguro-wp-ecommerce', false, 'pagseguro-wp-ecommerce/languages' );
    }
    
    function themes()
    {
        /* Force refresh theme roots. */
	delete_site_transient( 'theme_roots' );
        register_theme_directory( dirname( __FILE__ ) . '/themes' );
        if ( get_option( 'pagseguro_wp_ecommerce_set_themes' ) ) :
            switch_theme( 'pagseguro', 'pagseguro' );
        endif;
    }

    /**
     * 
     */
    function menu()
    {
        add_menu_page( __( 'PagSeguro WP e-Commerce', 'pagseguro-wp-ecommerce' ), __( 'PagSeguro WP e-Commerce', 'pagseguro-wp-ecommerce' ), 'manage_pagseguro_wp_ecommerce', 'pagseguro-wp-ecommerce/options-wp.php' );
        add_submenu_page( 'pagseguro-wp-ecommerce/options-wp.php', __( 'WordPress options', 'pagseguro-wp-ecommerce' ), __( 'WordPress options', 'pagseguro-wp-ecommerce' ), 'manage_pagseguro_wp_ecommerce', 'pagseguro-wp-ecommerce/options-wp.php' );
        add_submenu_page( 'pagseguro-wp-ecommerce/options-wp.php', __( 'PagSeguro options', 'pagseguro-wp-ecommerce' ), __( 'PagSeguro options', 'pagseguro-wp-ecommerce' ), 'manage_pagseguro_wp_ecommerce', 'pagseguro-wp-ecommerce/options-pagseguro.php' );
    }

   /**
     * Method used to query the posts and show just the products.
    *
    * @todo Support to 3.0 version. Filter for post_type
     *
     * We consider the products the posts under the categories defined
     * like products category.
     */
    function filter_products()
    {
        query_posts( 'meta_key=pagseguro_wp_ecommerce_product_metadata&cat=' . $this->pagseguro_wp_ecommerce_products_category_id );
    }

    /**
     * This method is called by WordPress Shortcode engine when it's find the
     * shortcode [pagseguro-wp-ecommerce-table].
     *
     * @param array $attributes The attributes defined when use the shortcode
     * @return string
     */
    function shortcode( $attributes )
    {
        return $this->embed_shopping_cart();
    }

    /**
     * Adds a new meta box to Post editor
     */
    function add_meta_box()
    {
        add_meta_box( 'pagseguro-wp-ecommerce', __( 'PagSeguro WP e-Commerce' ),  array( &$this, 'show_meta_box' ), 'post', 'side' );
    }

    /**
     * Shows the iMasters WP Budget meta box at post editor
     *
     * @global object $post The post object
     * @return void
     */
    function show_meta_box()
    {
        global $post;

        $post_metadata = get_post_meta( $post->ID, 'pagseguro_wp_ecommerce_product_metadata', true );
?>
        <p>
            <label for="pagseguro_wp_ecommerce_product_price"><?php _e( 'Product price', 'pagseguro-wp-ecommerce' ); ?></label>
            <br />
            R$ <input type="text" class="form-input-tip" value="<?php echo $post_metadata['product_price']; ?>" id="pagseguro_wp_ecommerce_product_price" name="pagseguro_wp_ecommerce_product_price" />
        </p>
        <p>
            <label for="pagseguro_wp_ecommerce_product_weight"><?php _e( 'Product weight', 'pagseguro-wp-ecommerce' ); ?></label>
            <br />
            <input type="text" class="form-input-tip" value="<?php echo $post_metadata['product_weight']; ?>" id="pagseguro_wp_ecommerce_product_weight" name="pagseguro_wp_ecommerce_product_weight" />
            <br />
            <span class="setting-description"><?php _e( 'It will be used to calculate the shipping cost. Use kilogram unit.', 'pagseguro-wp-ecommerce' ); ?></span>
        </p>       
        <p>
            <label for="pagseguro_wp_ecommerce_product_code"><?php _e( 'Product code', 'pagseguro-wp-ecommerce' ); ?></label>
            <br />
            <input type="text" class="form-input-tip" value="<?php echo $post_metadata['product_code']; ?>" id="pagseguro_wp_ecommerce_product_code" name="pagseguro_wp_ecommerce_product_code" />
            <br />
            <span class="setting-description"><?php _e( 'Define the product code used in your store. It can help you identify it among others.', 'pagseguro-wp-ecommerce' ); ?></span>
        </p>
        <p>
            <label for="pagseguro_wp_ecommerce_product_delivery_information"><?php _e( 'Delivery information', 'pagseguro-wp-ecommerce' ); ?></label>
            <br /><input type="text" class="form-input-tip" value="<?php echo $post_metadata['product_delivery_information']; ?>" id="pagseguro_wp_ecommerce_product_delivery_information" name="pagseguro_wp_ecommerce_product_delivery_information" />
            <br /><span class="setting-description"><?php _e( 'How many days the users need to wait to receive this product? Show information about the delivery proccess.', 'pagseguro-wp-ecommerce' ); ?></span>
        </p>
<?php
    }

    /**
     * Save the meta information when post is saved
     *
     * @param int $new_post_id The Id of the post saved
     * @param object $post The object containing the post data
     * @return object Returns the object containing the post data
     */
    function save_post_meta( $new_post_id, $post )
    {
        // Ignore autosaves, ignore quick saves
        if ( @constant( 'DOING_AUTOSAVE' ) ) return $post;
		if (!$_POST ) return $post;
		if ( !in_array( $_POST['action'], array( 'editpost', 'post' ) ) ) return $post;

        $post_id = attribute_escape( $_POST['post_ID'] );
		if ( !$post_id ) $post_id = $new_post_id;
		if ( !$post_id ) return $post;

        // Make sure we're saving the correct version
        $_p = wp_is_post_revision( $post_id );
	if ( $_p )
            $post_id = $_p;

        if ( !empty( $_POST['pagseguro_wp_ecommerce_product_price'] ) )
            $product_metadata['product_price'] = $this->sanitize_price( $_POST['pagseguro_wp_ecommerce_product_price'] );

        if ( !empty( $_POST['pagseguro_wp_ecommerce_product_code'] ) )
            $product_metadata['product_code'] = $_POST['pagseguro_wp_ecommerce_product_code'];
        
        if ( !empty( $_POST['pagseguro_wp_ecommerce_product_weight'] ) )
            $product_metadata['product_weight'] = $this->sanitize_weight( $_POST['pagseguro_wp_ecommerce_product_weight'] );

        if ( !empty( $_POST['pagseguro_wp_ecommerce_product_delivery_information'] ) )
            $product_metadata['product_delivery_information'] = $_POST['pagseguro_wp_ecommerce_product_delivery_information'];
        
        if ( !update_post_meta( $post_id, 'pagseguro_wp_ecommerce_product_metadata', $product_metadata ) )
            add_post_meta( $post_id, 'pagseguro_wp_ecommerce_product_metadata', $product_metadata, true );

    }

    /**
     * Sanitizes a string considered a price to store it in MySQL
     *
     * All characters, excepts, numbers are stripped. So, we put a . (dot)
     * to split the price and the decimal value.
     *
     * @param string $price The price to be sanitized
     * @return string
     */
    function sanitize_price( $price )
    {
        $price = preg_replace( '/[^\d]/', '', $price );

        $price_no_digits = substr( $price, 0, ( strlen( $price ) - 2 ) );
        $price_digits = substr( $price, -2, 2 );

        $price = sprintf( '%d.%s', $price_no_digits, $price_digits );

        return $price;
    }

    function sanitize_weight( $weight )
    {
        $weight = str_replace( ',', '.', $weight );
        $weight = preg_replace( '/[^\d.]/', '', $weight );
        
        $length = strlen( $weight );

        if ( $length <= 3 )
            $weight = '0.' . sprintf("%03s", $weight );

        if ( $length > 4 and strpos( $weight, '.' ) === false )
            $weight = sprintf( '%d.%d', substr ( $weight, 0, $length - 3 ), substr( $weight, ( $length - 3 ), $length ) );
        
        return $weight;
    }

    function sanitize_weight_to_freight( $weight )
    {
        return str_replace( '.', ',', $weight );
        
    }

    function the_price()
    {
        global $post;

        $post_metadata = get_post_meta( $post->ID, 'pagseguro_wp_ecommerce_product_metadata', true );

        echo number_format( $post_metadata['product_price'], 2, ',', '.' );
    }

    function the_code()
    {
        global $post;

        $post_metadata = get_post_meta( $post->ID, 'pagseguro_wp_ecommerce_product_metadata', true );

        echo $post_metadata['product_code'];
    }

    function the_delivery_information()
    {
        global $post;

        $post_metadata = get_post_meta( $post->ID, 'pagseguro_wp_ecommerce_product_metadata', true );

        echo $post_metadata['product_delivery_information'];
    }

    function the_buy_button()
    {
        global $post;

        $params = array(
            '_pagseguro_wp_ecommerce_action' => 'add',
            'post_id' => $post->ID
        );

        printf( '<a class="pagseguro-wp-ecommerce-buy-button" href="%s" title="%s"><span>%s</span></a>',
            str_replace( '&', '&amp;', add_query_arg( $params, get_permalink( $this->pagseguro_wp_ecommerce_shopping_cart_page_id ) ) ),
            esc_attr( $post->post_title ),
            $this->pagseguro_wp_ecommerce_buy_button_text
        );
    }

    /**
     * Add a stylesheet to style the iMasters WP Budget shopping cart
     * @return void
     */
    function css()
    {
        if ( !is_page( $this->pagseguro_wp_ecommerce_shopping_cart_page_id ) )
                return;

        wp_enqueue_style( 'pagseguro-wp-ecommerce-css', plugins_url( 'pagseguro-wp-ecommerce/assets/css/style.css'), false, $this->_version, 'all' );
    }

   /**
     * Add a JavaScript to script iMasters WP Budget shopping cart
     * @return void
     */
    function js()
    {
        if ( !is_page( $this->pagseguro_wp_ecommerce_shopping_cart_page_id ) )
                return;

        wp_enqueue_script('pagseguro-wp-ecommerce-js', plugins_url( 'pagseguro-wp-ecommerce/assets/javascript/script.js'), array( 'jquery' ), $this->_version, true );
        echo "<script type=\"text/javascript\">\n";
        printf( "var pagseguro_wp_ecommerce_shopping_cart_page = '%s';\n", get_permalink( $this->pagseguro_wp_ecommerce_shopping_cart_page_id ) );
        printf( "var pagseguro_wp_ecommerce_ajax_url = '%s';\n", plugins_url( 'pagseguro-wp-ecommerce/pagseguro-wp-ecommerce.php' ) );
        printf( "var pagseguro_wp_ecommerce_message_to_confirm_delete = '%s';\n", __( 'Do you really want remove this item?', 'pagseguro-wp-ecommerce' ) );
        echo "</script>\n";
    }

    function admin_css()
    {
        wp_enqueue_style( 'pagseguro-wp-ecommerce-admin-css', plugins_url( 'pagseguro-wp-ecommerce/assets/css/style-admin.css'), null, $this->_version );
    }
    
    function admin_js()
    {
        wp_enqueue_script('pagseguro-wp-ecommerce-admin-js', plugins_url( 'pagseguro-wp-ecommerce/assets/javascript/admin-script.js'), array( 'jquery' ), $this->_version, true );
    }

    /**
     * This method store the products in the shopping cart in an object. Cause
     * the method get uses session and it needs be called before any output.
     */
    function get_products_in_shopping_cart()
    {
        $this->products_in_cart = $this->pagseguro_wp_ecommerce_obj_cart->get();
    }

   /**
     * @todo Create an option to define the total numbers to show
     * @param int $quantity The current quantity. Used to select the related option.
     * @param string $attr_name The name used in attribute name
     * @param string $attr_id The name used in attribute id
     * @return string Return the select markup
     */
    function _get_dropdown_to_quantity( $quantity, $attr_name, $attr_id )
    {
        $option = sprintf( '<select class="pagseguro-wp-ecommerce-quantity" name="%s" id="%s">', $attr_name, $attr_id );
        for( $i = 1; $i <= 20; $i++ ) :
            $option .= sprintf( '<option value="%d"%s>%d</option>', $i, ( $i == $quantity ) ? ' selected="selected"' : '', $i );
        endfor;
        $option .=  '</select>';

        return $option;
    }

   /**
     * Build the shopping cart table and the form used to send the budget
     *
     * @return string
     */
    function embed_shopping_cart()
    {
        include_once dirname( __FILE__ ) . '/classes/pgs.php';
        $pgs = new pgs( array( 'enconding' => 'UTF-8', 'email_cobranca' => get_option( 'pagseguro_wp_ecommerce_email_charging' ) ) );
        
        $products_in_cart = $this->products_in_cart;
        $has_products_in_cart = ( count( $products_in_cart ) > 0 ) ? true : false;

        $list_products_in_cart = ( $has_products_in_cart ) ? '' : sprintf( '<p><strong>%s</strong></p>', __( 'Any product in your shopping cart', 'pagseguro-wp-ecommerce' ) );

        // When doesn't have any product in the cart, sends a message
        if ( !$has_products_in_cart )
            return $list_products_in_cart;

        $shipping_method = get_option( 'pagseguro_wp_ecommerce_shipping_method' );

        $total_products_in_cart = count( $products_in_cart );
        
        $subtotal = 0.00;
        foreach( (array)$products_in_cart as $product_in_cart ) :

            $post_metadata = get_post_meta( $product_in_cart->ID, 'pagseguro_wp_ecommerce_product_metadata', true );
            
            switch( $shipping_method ) :
                case 'free' :
                    $frete = 0;
                    $peso = 0;
                    break;
                case 'fixed' :
                    $frete = str_replace( array( ',', '.' ), '', get_option( 'pagseguro_wp_ecommerce_freight_fixed_price' ) );
                    $peso = null;
                    break;
                case 'weight' :
                    $frete = null;
                    $peso = str_replace( array( ',', '.' ), '', $post_metadata['product_weight'] );
                    break;
            endswitch;
            
            $pgs->adicionar(
                array(
                    'descricao' => $product_in_cart->post_title,
                    'valor' => str_replace( array( ',' ), '', $post_metadata['product_price'] ),
                    'peso' => $peso,
                    'frete' => $frete,
                    'quantidade' => $product_in_cart->cart_quantity,
                    'id' => $product_in_cart->ID
                )
             );
            
            $remove_button = sprintf( '<a class="pagseguro-wp-ecommerce-btn-remove" href="%s" title="%s">%s</a>',
                add_query_arg( array(
                    '_pagseguro_wp_ecommerce_action' => 'delete',
                    'post_id' => $product_in_cart->ID
                )),
                __( 'Remove this product', 'pagseguro-wp-ecommerce' ),
                __( 'Remove', 'pagseguro-wp-ecommerce' )
            );

            $list_products_in_cart .= sprintf( '<tr><td>%s %s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                    $product_in_cart->post_title,
                    $remove_button,
                    $this->_get_dropdown_to_quantity( $product_in_cart->cart_quantity, 'pagseguro_wp_ecommerce_quantity', 'pagseguro_wp_ecommerce_quantity_' . $product_in_cart->ID ),
                    'R$ ' . number_format( $post_metadata['product_price'], 2, ',', '.' ),
                    'R$ ' . number_format( ( $product_in_cart->cart_quantity * $post_metadata['product_price'] ), 2, ',', '.' )
            );

            $subtotal = ( $subtotal + ( $product_in_cart->cart_quantity * $post_metadata['product_price'] ) );
            $weight = ( $weight + ( $product_in_cart->cart_quantity * $post_metadata['product_weight' ] ) );
        endforeach;
?>
<form id="frm-pswpe-shopping-cart" method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>">
        <table class="pagseguro-wp-ecommerce-table">
            <thead>
                <tr>
                    <th><?php _e( 'Product', 'pagseguro-wp-ecommerce' ); ?></th>
                    <th><?php _e( 'Quantity', 'pagseguro-wp-ecommerce' ); ?></th>
                    <th><?php _e( 'Unit value', 'pagseguro-wp-ecommerce' ); ?></th>
                    <th><?php _e( 'Total value', 'pagseguro-wp-ecommerce' ); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3"><?php _e( 'Subtotal', 'pagseguro-wp-ecommerce' ); ?></td>
                    <td>
                        <?php _e( 'R$', 'pagseguro-wp-ecommerce' ); ?> <span id="pswpe-value-subtotal"><?php echo number_format( $subtotal, 2, ',', '.' ); ?></span>
                        <input type="hidden" name="subtotal" id="subtotal" value="<?php echo $subtotal; ?>" />
                        <input type="hidden" name="weight" id="weight" value="<?php echo $this->sanitize_weight_to_freight( $weight ); ?>" />
                    </td>
                </tr>
                <?php if ( 'weight' == $this->pagseguro_wp_ecommerce_shipping_method ) : ?>
                <tr>
                    <td colspan="2"><?php _e( 'Enter the delivery address zip code to calculate the value of service delivery:', 'pagseguro-wp-ecommerce' ); ?></td>
                    <td colspan="2" align="right">
                        <input type="text" id="pswpe_zipcode_1" name="pswpe_zipcode_1" maxlength="5" />
                        -
                        <input type="text" id="pswpe_zipcode_2" name="pswpe_zipcode_2" maxlength="3" />
                        <input type="submit" id="pswpe-zipcode-btn-ok" class="pswpe-button" name="zipcode" value="Ok" />
                    </td>
                </tr>
                <tr id="pswpe-feedback-message" class="pswpe-hide">
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr id="delivery-sedex-pac" class="pswpe-hide">
                    <td><?php _e( 'Choose the delivery method', 'pagseguro-wp-ecommerce' ); ?></td>
                    <td colspan="3" align="right">
                        <input type="radio" id="pswpe_zipcode_sedex" name="pswpe_freight_value" value="" />
                        <label for="pswpe_zipcode_sedex"><?php _e( 'Sedex', 'pagseguro-wp-e-commerce' ); ?></label>
                        
                        <input type="radio" checked="checked" id="pswpe_zipcode_pac" name="pswpe_freight_value" value="" />
                        <label for="pswpe_zipcode_pac"><?php _e( 'PAC', 'pagseguro-wp-e-commerce' ); ?></label>
                    </td>
                </tr>
                <tr id="delivery-pgsnok" class="pswpe-hide">
                    <td><?php _e( 'Freight fixed', 'pagseguro-wp-ecommerce' ); ?></td>
                    <td colspan="3" align="right" id="pgsnok">
                        <input type="radio" checked="checked" id="pswpe_zipcode_pgsnok" name="pswpe_freight_value" value="" />
                        <label for="pswpe_zipcode_pgsnok"><?php _e( 'Freight fixed', 'pagseguro-wp-e-commerce' ); ?></label>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><?php _e( 'Total', 'pagseguro-wp-ecommerce' ); ?></td>
                    <td><?php _e( 'R$', 'pagseguro-wp-ecommerce' ); ?> <span id="pswpe-value-total"><?php echo number_format( $subtotal, 2, ',', '.' ); ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if ( 'fixed' == $this->pagseguro_wp_ecommerce_shipping_method ) : ?>
                <tr>
                    <td colspan="3"><?php _e( 'Freight value', 'pagseguro-wp-ecommerce' ); ?></td>
                    <td>
                        <?php _e( 'R$', 'pagseguro-wp-ecommerce' ); ?>
                        <?php 
                        $freigh_value = number_format( ( $this->pagseguro_wp_ecommerce_freight_fixed_price * $total_products_in_cart ), 2, ',', '.' );
                        echo $freigh_value;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><?php _e( 'Total', 'pagseguro-wp-ecommerce' ); ?></td>
                    <td>
                    <?php _e( 'R$', 'pagseguro-wp-ecommerce' ); ?>
                        <span id="pswpe-value-total"><?php echo number_format( ( $subtotal + $freigh_value ), 2, ',', '.' ); ?></span>
                    </td>
                </tr>
                <?php endif; ?>
            </tfoot>
            <tbody>
                <?php echo $list_products_in_cart; ?>
            </tbody>
        </table>
    </form>

    <table class="pagseguro-wp-ecommerce-table">
        <tbody>
            <tr>
                <td colspan="1"><a href="<?php bloginfo( 'url' ); ?>"><?php _e( 'Continue buying', 'pagseguro-wp-ecommerce' ); ?></a></td>
                <td colspan="3" align="right"><?php $pgs->mostra(); ?></td>
            </tr>
        </tbody>
    </table>
<?php
    }

    function _observe_params()
    {
        $action = ( isset( $_REQUEST['_pagseguro_wp_ecommerce_action' ] ) ) ? $_REQUEST['_pagseguro_wp_ecommerce_action' ] : '';

        switch( $action ) :
            case 'add' :
                $this->pagseguro_wp_ecommerce_obj_cart->add( $_GET['post_id'], 1);
                break;
            case 'update' :
                $this->pagseguro_wp_ecommerce_obj_cart->update( $_GET['post_id'], $_GET['quantity'] );
                break;
            case 'delete' :
                $this->pagseguro_wp_ecommerce_obj_cart->delete( $_GET['post_id'] );
                break;
        endswitch;
    }

    function _handle_ajax()
    {
        $pswpe_ajax = ( isset( $_REQUEST['pswpe_ajax'] ) ) ? $_REQUEST['pswpe_ajax'] : '';

        switch( $pswpe_ajax ) :
            case 'zipcode' :
                require_once dirname( __FILE__ ) . '/classes/PagSeguro_Frete.php';
                $objFrete = new PagSeguro_Frete();
                $params = array(
                    'cepOrigem' => get_option( 'pagseguro_wp_ecommerce_freight_zip_code' ),
                    'cepDestino' => esc_attr( $_GET['zipcode'] ),
                    'valor' => trim( $_GET['subtotal'] ),
                    'peso' => trim( $_GET['weight'] )
                );
                $hasFreightValues = $objFrete->calcular( $params );
                
                if ( $hasFreightValues ) :
                    $prices = array(
                        'Sedex' => $objFrete->getSedex(),
                        'PAC' => $objFrete->getPac()
                    );
                    echo json_encode( $prices );
                else :
                    $prices = array(
                        'pgsnok' => get_option( 'pagseguro_wp_ecommerce_freight_fixed_price_pgs_nok' )
                    );
                    echo json_encode( $prices );
                endif;
                break;
        endswitch;
    }

    /**
     * Check if the current WordPress version is the 3.0 or higher.
     * 
     * @global string $wp_version WordPress version
     * @return bool Return true if the current WordPress is the 3.0 or higher and
     * false otherwise.
     */
    function _is_wp_3()
    {
        global $wp_version;
        return version_compare( $wp_version, '3.0', '>=' );
    }

    function alert_required_wp_version()
    {
        global $wp_version;
        $is_wp_lower_than_required = version_compare( $wp_version, $this->_required_wp_version, '<' );
        if ( $is_wp_lower_than_required )
            printf( '<div class="updated"><p>%s</p></div>', __( 'PagSeguro WP e-Commerce requires WordPress 2.9 or higher. <a href="http://wordpress.org/download/">Download WordPress</a> and update your version.', 'pagseguro-wp-ecommerce' ) );
            
    }

    function alert_required_settings()
    {
        $needs_settings = false;
        
        $email_charging = $this->pagseguro_wp_ecommerce_email_charging;
        $shipping_method = $this->pagseguro_wp_ecommerce_shipping_method;
        $fixed_price = $this->pagseguro_wp_ecommerce_freight_fixed_price;
        $zip_code = $this->pagseguro_wp_ecommerce_freight_zip_code;

        if ( empty( $email_charging ) )
            $needs_settings = true;

        switch( $shipping_method ) :
            case 'free' :
                $needs_settings = false;
                break;
            case 'weight' :
                if ( empty( $zip_code ) )
                    $needs_settings = true;
                break;
            case 'fixed' :
                if ( empty( $fixed_price ) )
                    $needs_settings = true;
                break;
        endswitch;
                
        $settings_page = admin_url( 'admin.php?page=pagseguro-wp-ecommerce/options-pagseguro.php' );
        if ( $needs_settings )
            printf( '<div class="updated"><p>%s</p></div>', sprintf( __( 'PagSeguro WP e-Commerce requires settings. Go to <a href="%s">Settings page</a> to configure it.', 'uol-host-perfil' ), $settings_page ) );
    }

}

/**
 * Loads WordPress bootstrap if this file is called directly like an Ajax call.
 */
if ( !function_exists( 'add_action' ) ) :
    $wp_root = '../../..';
    if ( file_exists( $wp_root . '/wp-load.php' ) ) :
        require_once $wp_root . '/wp-load.php';
    else :
        require_once $wp_root . '/wp-config.php';
    endif;
endif;

if ( function_exists( 'register_theme_directory' ) )
    register_theme_directory( WP_PLUGIN_DIR . '/pagseguro-wp-ecommerce/themes' );

$pagseguro_wp_ecommerce = new Pagseguro_Wp_Ecommerce();

require_once dirname( __FILE__ ) . '/template-tags.php';
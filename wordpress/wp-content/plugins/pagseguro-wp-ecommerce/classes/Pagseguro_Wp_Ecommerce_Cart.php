<?php
class Pagseguro_Wp_Ecommerce_Cart {

    var $cookie_name = 'iwpb';

    function Pagseguro_Wp_Ecommerce_Cart()
    {
    }

    function get()
    {
        global $wpdb;

        $products_in_cart = $wpdb->get_results( sprintf( "
            SELECT sp.*, p.*
            FROM $wpdb->pagseguro_wp_ecommerce_table_shopping_cart AS sp
            INNER JOIN $wpdb->posts AS p ON sp.cart_post_id = p.ID
            WHERE
            sp.cart_cookie_id = '%s'",
            $this->_get_cart_id()
        ) );

        return $products_in_cart;
    }

    function add( $post_id, $quantity )
    {
        global $wpdb;
        
        $the_product_was_added = $this->_the_product_was_added($post_id);

        if ( $the_product_was_added == 0 ) :

            return $wpdb->query( $wpdb->prepare( "
                INSERT INTO $wpdb->pagseguro_wp_ecommerce_table_shopping_cart
                ( cart_post_id, cart_cookie_id, cart_quantity )
                VALUES
                ( %d, '%s', %d )",
                $post_id,
                $this->_get_cart_id(),
                $quantity
            ) );

        else :

            return $this->update( $post_id, $quantity );

        endif;
    }

    function update( $post_id, $quantity )
    {
        global $wpdb;

        return $wpdb->query( $wpdb->prepare( "
            UPDATE $wpdb->pagseguro_wp_ecommerce_table_shopping_cart
            SET
            cart_quantity = %d
            WHERE
            cart_post_id = %d
            AND
            cart_cookie_id = '%s'",
            $quantity,
            $post_id,
            $this->_get_cart_id()
        ) );
    }

    function delete( $post_id )
    {
        global $wpdb;

        return $wpdb->query( $wpdb->prepare( "
            DELETE FROM $wpdb->pagseguro_wp_ecommerce_table_shopping_cart
            WHERE
            cart_post_id = %d
            AND
            cart_cookie_id = '%s'",
            $post_id,
            $this->_get_cart_id()
        ) );
    }

    function delete_all()
    {
        global $wpdb;

        return $wpdb->query( $wpdb->prepare( "
            DELETE FROM $wpdb->pagseguro_wp_ecommerce_table_shopping_cart
            WHERE
            cart_cookie_id = '%s'",
            $this->_get_cart_id()
        ) );
    }

    function _delete_old()
    {

    }

    function _the_product_was_added( $post_id )
    {
        global $wpdb;

        return $wpdb->get_var( sprintf( "
            SELECT COUNT(*)
            FROM $wpdb->pagseguro_wp_ecommerce_table_shopping_cart
            WHERE
            cart_cookie_id = '%s'
            AND
            cart_post_id = %d",
            $this->_get_cart_id(),
            $post_id
        ) );
    }

    function _get_cart_id()
    {

        if ( !isset( $_SESSION ) )
            session_start();

        if ( isset( $_COOKIE[$this->cookie_name] ) )
                return $_COOKIE[$this->cookie_name];

        $session_id = session_id();
        setcookie($this->cookie_name, $session_id, time()+3600*24*30, '/' );

        return $session_id;
    }

}
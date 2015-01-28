<?php
/**
 *
 * @global <type> $pagseguro_wp_ecommerce
 */
function pagseguro_wp_ecommerce_the_buy_button()
{
    global $pagseguro_wp_ecommerce;
    $pagseguro_wp_ecommerce->the_buy_button();
}

/**
 *
 * @global <type> $pagseguro_wp_ecommerce
 */
function pagseguro_wp_ecommerce_the_price()
{
    global $pagseguro_wp_ecommerce;
    $pagseguro_wp_ecommerce->the_price();
}

/**
 *
 * @global <type> $pagseguro_wp_ecommerce
 */
function pagseguro_wp_ecommerce_the_code()
{
    global $pagseguro_wp_ecommerce;
    $pagseguro_wp_ecommerce->the_code();
}

/**
 *
 * @global <type> $pagseguro_wp_ecommerce
 */
function pagseguro_wp_ecommerce_the_delivery_information()
{
    global $pagseguro_wp_ecommerce;
    $pagseguro_wp_ecommerce->the_delivery_information();
}

/**
 *
 * @global <type> $pagseguro_wp_ecommerce
 */
function pagseguro_wp_ecommerce_filter_products()
{
    global $pagseguro_wp_ecommerce;
    $pagseguro_wp_ecommerce->filter_products();
}

function pagseguro_wp_ecommerce_get_products_in_shopping_cart()
{
    global $pagseguro_wp_ecommerce;
    return $pagseguro_wp_ecommerce->products_in_cart;
}

function pagseguro_wp_ecommerce_get_total_products_in_shopping_cart()
{
    global $pagseguro_wp_ecommerce;
    return count( $pagseguro_wp_ecommerce->products_in_cart );
}

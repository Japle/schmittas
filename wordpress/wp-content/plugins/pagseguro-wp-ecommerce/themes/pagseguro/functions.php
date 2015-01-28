<?php
/**
 * We can use set_post_thumbnail_size or add_image_size to register a dimension
 * to the images used in the template.
 *
 * add_image_size in this case is better than set_post_thumbnail_size becouse the
 * first one don't conflict with the current user thumbnail size. So the user
 * can have her thumbnail size dimension and the template can has yours.
 *
 */
if ( function_exists( 'add_image_size' ) )
    add_image_size( 'pswpe-post-thumbnail', 150, 150, true );

if ( function_exists( 'add_theme_support' ) )
    add_theme_support( 'post-thumbnails' );

function pagseguro_wp_ecommerce_the_category()
{
    $i = 0;
    foreach( ( get_the_category() ) as $category ) :
        if ( $category->category_parent <> 0 and $i == 0 ) :
            printf( '<a href="%s" title="%2$s">%2$s</a>',
                get_category_link( $category->cat_ID ),
                $category->cat_name
            );
            $i++;
        endif;
    endforeach;
}

function pagseguro_wp_ecommerce_the_loop()
{
    require_once TEMPLATEPATH . '/loop.inc.php';
}

/**
 * Shows a singular or plural message based in a informed number
 * How to use: singular_plural( '%d child', '%d children', 2 );\
 *
 * @version 0.1
 * @date Monday, January 7, 2008
 * @author Leandro Vieira Pinho - http://leandrovieira.com
 */

function singular_plural( $strMsgSingular, $strMsgPlural, $intTotal )
{
    if ( $intTotal > 1 )
 	return sprintf( $strMsgPlural, $intTotal );
 	return sprintf( $strMsgSingular, $intTotal );
}

/**
 * Remove os estilos padrão do WordPress para a galeria.
 * É preciso retornar uma tag div devido ela está inclusa
 * junto aos estilos.
 */
add_filter( 'gallery_style', 'apiki_remove_gallery_style' );
function apiki_remove_gallery_style( $output )
{
    return '<div class="product-gallery">';
}

wp_enqueue_script( 'resetDefaultValue', get_bloginfo( 'template_url') . '/assets/javascript/jquery.resetDefaultValue.js', array( 'jquery' ) );
wp_enqueue_script( 'lightBox', get_bloginfo( 'template_url' ) . '/assets/javascript/jquery.lightbox-0.5.min.js', array( 'jquery' ) );
wp_enqueue_script( 'script', get_bloginfo( 'template_url') . '/assets/javascript/script.js', array( 'jquery' ), null, true );
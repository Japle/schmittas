<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
    <?php wp_head(); ?>
</head>
<body>

    <div id="wrapper">

        <div id="header">

            <div class="container_12 clearfix">

                <div id="branding" class="grid_4">
                    <h1>
                        <a href="<?php bloginfo( 'url' ); ?>" title="<?php bloginfo( 'name' ); ?>">
                            <img src="<?php bloginfo( 'template_url' ); ?>/assets/images/marca-da-minha-loja.png" width="175" height="55" alt="" />
                        </a>
                    </h1>
                </div><!-- / branding -->

                <div id="search" class="grid_6">
                    <?php get_search_form(); ?>
                </div>

            </div>

        </div><!-- / header -->

        <div id="wrapper-content" class="container_12 clearfix">
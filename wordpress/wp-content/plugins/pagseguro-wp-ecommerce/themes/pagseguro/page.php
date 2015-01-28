<?php get_header(); ?>

<div id="content" class="grid_9">

    <?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>
    <h1 class="title-section"><?php the_title(); ?></h1>
        
    <div id="page" class="spacer">
        <?php the_content(); ?>
    </div>    
    <?php endwhile; endif; ?>
    
</div><!-- / content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
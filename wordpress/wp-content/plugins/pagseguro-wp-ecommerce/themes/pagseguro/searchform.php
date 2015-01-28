<form action="<?php bloginfo( 'url' ); ?>" id="searchform" method="get">
    <div>
        <input type="text" id="s" name="s" value="<?php esc_html( the_search_query() ); ?>" />
        <input type="submit" value="Buscar" class="submit" />
    </div>
</form>
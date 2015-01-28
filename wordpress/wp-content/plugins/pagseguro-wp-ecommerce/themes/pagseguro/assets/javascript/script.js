jQuery(function() {
    jQuery('#nav li:last').addClass('last');

    var $galleryItens = jQuery('div.gallery-item');
    var totalItens = $galleryItens.length;
    $galleryItens.not(':first').hide();
    html = '<ul class="product-gallery-controlls">';
    for( i = 0; i < totalItens; i++ ) {
        html += '<li><a href="javascript:;" onclick="LOCTEL.showGalleryItem(' + i + ');">' + ( i + 1 ) + '</a></li>';
    }
    html += '</ul>';
    jQuery('div.product-gallery').append(html);

});

var LOCTEL = {
    showGalleryItem : function(itemIndex) {
        var $galleryItens = jQuery('div.gallery-item');
        $galleryItens.hide();
        $galleryItens.eq(itemIndex).fadeIn();
    }
}
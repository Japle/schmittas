jQuery( function($) {

    var $shipping_method = $( '#pagseguro_wp_ecommerce_shipping_method' );
    var $freight_by_weight = $( '#pagseguro_wp_ecommerce_freight_by_weight_options' );
    var $freight_fixed = $( '#pagseguro_wp_ecommerce_freight_fixed_options' );

    switch( $shipping_method.val() ) {
        case 'weight' :
            $freight_fixed.hide()
            $freight_by_weight.children( ':input' ).focus();
            break;
        case 'fixed' :
            $freight_by_weight.hide()
            $freight_fixed.children( ':input' ).focus();
            break;
        case 'free' :
            $freight_by_weight.hide();
            $freight_fixed.hide();
        break;
    }

    $shipping_method.change( function() {

        var freight_options = $(this).val();

        switch( freight_options ) {
            case 'weight' :
                $freight_fixed.hide();
                $freight_by_weight
                .fadeIn()
                .children( ':input' ).focus();
                break;
            case 'fixed' :
                $freight_by_weight.hide();
                $freight_fixed
                .fadeIn()
                .children( ':input' ).focus();
                break;
            case 'free' :
                $freight_by_weight.hide();
                $freight_fixed.hide();
            break;
        }
    })

})
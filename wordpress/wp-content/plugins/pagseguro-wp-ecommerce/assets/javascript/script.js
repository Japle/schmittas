jQuery( function($) {

    PSWPE.init();

    var _current_url, current_url, quantity, id, post_id;
    $('select[name="pagseguro_wp_ecommerce_quantity"]').change(function(){
        _current_url = pagseguro_wp_ecommerce_shopping_cart_page;
        current_url = ( _current_url.indexOf('?') != -1 ) ? _current_url + '&' : _current_url + '?';
        quantity = $(this).val();
        id = $(this).attr('id');
        post_id = id.replace('pagseguro_wp_ecommerce_quantity_', '' );
        window.location.href = current_url + '_pagseguro_wp_ecommerce_action=update&post_id=' + post_id + '&quantity=' + quantity;
    })

    $('a.iwpb-btn-remove').click(function(e){
        var _confirm = confirm( iwpb_message_to_confirm_delete );
        if ( !_confirm )
            e.preventDefault();
    })

})

var PSWPE = {

    init : function() {
        this.calculate_freight();
        jQuery( 'input[name="pswpe_freight_value"]').click( function() {
            PSWPE.calculate_total_value();
            PSWPE.set_freight_to_ps( jQuery(this).val() );
        });
    },

    calculate_freight : function() {

        var feedback_message = jQuery( '#pswpe-feedback-message' );        
        var frm_shopping_cart = jQuery( '#frm-pswpe-shopping-cart' );

        frm_shopping_cart.submit( function( event ) {

            var _zipcode = jQuery( '#pswpe_zipcode_1' ).val();
            var __zipcode = jQuery( '#pswpe_zipcode_2' ).val();
            var zipcode = _zipcode + '-' + __zipcode;

            if (
                !zipcode.match( /^[0-9]{5}-[0-9]{3}$/ )
                ||
                PSWPE.str_repeat( zipcode.substr( 0, 1 ), 5 ) == _zipcode
                ||
                _zipcode === '01234'
                ||
                _zipcode === '12345'
               ) {
                feedback_message
                .fadeIn()
                .find( 'td' )
                .html( '<strong>Por favor, informe um CEP inválido</strong>' );
                jQuery( '#pswpe_zipcode_1' ).focus();
                return false;
            } else {
                feedback_message.hide();
            }
            var subtotal = jQuery( '#subtotal' ).val();
            var weight = jQuery( '#weight' ).val();

            feedback_message.ajaxStart( function() {
                jQuery(this)
                .fadeIn()
                .find( 'td' )
                .html( '<strong>Calculando... valor do frete. Aguarde...</strong>' );
            })

            jQuery.ajax( {

                url : pagseguro_wp_ecommerce_ajax_url,
                data : {
                    'pswpe_ajax' : 'zipcode',
                    'zipcode' : zipcode,
                    'subtotal' : subtotal,
                    'weight' : weight
                },
                dataType : 'json',
                success : function( ajax_return ) {
                    feedback_message.hide();

                    if ( ajax_return.pgsnok ) {
                        jQuery( '#delivery-pgsnok' ).fadeIn();
                        jQuery( '#pswpe_zipcode_pgsnok' )
                        .val( ajax_return.pgsnok )
                        .next()
                        .text( 'R$ ' + ajax_return.pgsnok );

                        jQuery( 'input[name*="item_peso_"]' ).val('');
                    } else {
                        jQuery( '#delivery-sedex-pac' ).fadeIn();
                        
                        jQuery( '#pswpe_zipcode_sedex' )
                        .val( ajax_return.Sedex )
                        .next()
                        .text( 'R$ ' + ajax_return.Sedex + ' Sedex' );

                        jQuery( '#pswpe_zipcode_pac' )
                        .val( ajax_return.PAC )
                        .next()
                        .text( 'R$ ' + ajax_return.PAC + ' PAC' );
                    }

                    PSWPE.calculate_total_value();
                    PSWPE.set_freight_to_ps( ( ajax_return.pgsnok ) ? ajax_return.pgsnok : ajax_return.PAC );
                    PSWPE.set_zipcode_to_ps( zipcode );
                },
                cache : false

            } );

            event.preventDefault();

        } );

    },

    calculate_total_value : function() {
        var _subtotal = jQuery( '#pswpe-value-subtotal' ).text();
        var subtotal = parseInt( _subtotal.replace( /[^\d]/, '' ) );

        var _freight = jQuery( 'input[name="pswpe_freight_value"]:checked').val();
        var freight = parseInt( _freight.replace( /[^\d]/, '' ) );

        var total = subtotal + freight;

        jQuery( '#pswpe-value-total' ).text( PSWPE.number_format( total/100, 2, ',', '.' ) );
    },

    /**
     * based here http://www.selfcontained.us/2008/04/22/format-currency-in-javascript-simplified/
     */
    formatCurrency : function( num ) {
        num = isNaN(num) || num === '' || num === null ? 0.00 : num;
        return parseFloat(num/100).toFixed(2);
    },

    /**
     * Adiciona campo de formulário que define o valor do frete e campo que define
     * o tipo de frete.
     *
     * @param string freight_value Valor de frete a ser enviado ao PagSeguro
     */
    set_freight_to_ps : function( freight_value ) {
        jQuery( 'input[name="item_frete_1"]' ).val( freight_value.replace( /[^\d]/, '' ) );
        var freight_type_selected = jQuery( 'input[name="pswpe_freight_value"]:checked' ).attr( 'id' );
        if ( freight_type_selected.match( /_sedex$/ ) )
            freight_type = 'SD';
        else
            freight_type = 'EN';

        var was_created = jQuery( 'input[name="tipo_frete"]' ).length;
        if ( !was_created )
            jQuery( 'form[target="pagseguro"]' ).append( '<input type="hidden" name="tipo_frete" value="' + freight_type + '" />' );
        else
            jQuery( 'input[name="tipo_frete"]' ).val( freight_type );

    },

    /**
     * Adiciona campo de formulário que define o CEP do cliente.
     *
     * @param string zipcode Cep a ser enviado ao PagSeguro
     */
    set_zipcode_to_ps : function( zipcode ) {
        zipcode = zipcode.replace( /[^\d]/, '' );
        var was_created = jQuery( 'input[name="cliente_cep"]' ).length;
        if ( !was_created )
            jQuery( 'input[name="email_cobranca"]' ).after( '<input type="hidden" name="cliente_cep" value="' + zipcode + '" />' );
    },

    /**
     * 
     */
    number_format : function(number, decimals, dec_point, thousands_sep) {
        // http://kevin.vanzonneveld.net
        // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +     bugfix by: Michael White (http://getsprink.com)
        // +     bugfix by: Benjamin Lupton
        // +     bugfix by: Allan Jensen (http://www.winternet.no)
        // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // +     bugfix by: Howard Yeend
        // +    revised by: Luke Smith (http://lucassmith.name)
        // +     bugfix by: Diogo Resende
        // +     bugfix by: Rival
        // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
        // +   improved by: davook
        // +   improved by: Brett Zamir (http://brett-zamir.me)
        // +      input by: Jay Klehr
        // +   improved by: Brett Zamir (http://brett-zamir.me)
        // +      input by: Amir Habibi (http://www.residence-mixte.com/)
        // +     bugfix by: Brett Zamir (http://brett-zamir.me)
        // +   improved by: Theriault
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    },

    /**
     * http://phpjs.org/functions/str_repeat
     */
    str_repeat : function( input, multiplier ) {
        // http://kevin.vanzonneveld.net
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // *     example 1: str_repeat('-=', 10);
        // *     returns 1: '-=-=-=-=-=-=-=-=-=-='
        return new Array(multiplier+1).join(input);
    }
    
}
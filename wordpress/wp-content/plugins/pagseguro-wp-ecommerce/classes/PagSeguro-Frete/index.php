<?php
require_once dirname( __FILE__ ) . '\Pag_Seguro_Frete.php';


//---- Ex 1
$objFrete = new Pag_Seguro_Frete();
$args = array( 'cepOrigem' => '04302000', 'cepDestino' => '14.810-165', 'valor' => '10', 'peso' => '0,300' );

if ( $objFrete->calcular( $args ) ) {
    echo 'UF: ' . $objFrete->getUf();
    echo '<br />Valor SEDEX: ' . $objFrete->getSedex();
    echo '<br />Valor PAC: ' . $objFrete->getPac();
} else echo 'erro';

echo '<br /><br />';

//---- Ex 2
$objNewFrete = new Pag_Seguro_Frete();
$args = array( 'cepOrigem' => '04302000', 'cepDestino' => '14.810-165', 'valor' => '10', 'peso' => '0,300', 'retornar' => true );

print_r($objFrete->calcular($args));

?>
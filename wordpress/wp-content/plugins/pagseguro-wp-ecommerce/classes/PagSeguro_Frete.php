<?php

/**
 * Classe responsável por realizar a consulta do valor do frete utilizando o serviço
 * disponibilizado pelo PagSeguro
 * 
 * @author Apiki
 * @version 1.0
 */
class PagSeguro_Frete {

    /**
     * @var string endereço fixo de requisição
     */
    protected $_urlBase = 'https://pagseguro.uol.com.br/desenvolvedor/simulador_de_frete_calcular.jhtml?';
    private $_uf = '';
    private $_pac = '';
    private $_sedex = '';
    private $_isCapital = '';

    /**
     *  Indica se a cidade de entrega é capital ou não.
     * @return bool True para capital, false para interior.
     */
    public function getIsCapital() {
        return $this->_isCapital;
    }

    /**
     *  Valor do frete via Sedex.
     * @return float Valor do Sedex .
     */
    public function getSedex() {
        return $this->_sedex;
    }

    /**
     *  Valor do frete via Pac.
     * @return float Valor do Pac.
     */
    public function getPac() {
        return $this->_pac;
    }

    /**
     *  Indica o Estado(Unidade Federativa) do endereço de entrega.
     * @return string UF de destino.
     */
    public function getUf() {
        return $this->_uf;
    }

    /**
     *  Método que grava os valores de frete recuperados.
     *
     * Todos os parâmetros necessários para a busca são informados em um array
     * que deve ser passado para o método, são eles:
     *
     * <ol>
     * <li>cepOrigem       = string | CEP de origem da encomenda.</li>
     * <li>cepDestino      = string | CEP de destino da encomenda.</li>
     * <li>peso            = string|int | Peso(gr) da encomenda.</li>
     * <li>valor           = string|int | Valor da encomenda.</li>
     * <li>retornar        = bool | TRUE: Retorna um array com os dados recuperados.
     *                              FALSE: Grava os dados recuperados na classe.
     *                              Por Default é atribuído o valor FALSE.
     *                              Quando o valor FALSE está atribuído os dados recuperados
     *                              são gravados em atributos da classe e é retornado um bool
     *                              Quando o valor TRUE está atribuído é retornado um array
     *                              com as seguintes chaves: uf | capital | valorPac | valorSedex
     * </ol>
     *
     * Todos os parâmetros são obrigatórios.
     *
     * @param array $args Parâmetros necessários para gerar a url de requisição.
     * @return bool|array Retorna true se a pesquisa for realizada corretamente
     *              ou false caso ocorra algum problema na requisição ou falte
     *              algum parâmetro. Pode retornar um array com os dados encontrados
     *              caso a opção retornar seja setada como TRUE.
     */
    public function calcular( $args = array() )
    {
        $retornar = false; // True: retorna um array com os dados; False: grava os dados na classe
        if ( empty ( $args['cepOrigem'] ) || empty ( $args['cepDestino'] ) ||
             empty ( $args['peso'] ) || empty ( $args['valor'] ) ) {            
            return false;
        }

        $url = sprintf( '%spostalCodeFrom={$%s}&weight={$%s}&value={$%s}&postalCodeTo={$%s}',
            $this->_urlBase,
            preg_replace( '@[^\d]@', '', $args['cepOrigem'] ),
            (string)$args['peso'],
            (string)$args['valor'],
            preg_replace( '@[^\d]@', '', $args['cepDestino'] )
        );        

        $retorno = $this->_getContent( $url );

        $args_retorno = explode('|', $retorno);

        if ( !empty ( $args['retornar'] ) )
            $retornar = $args['retornar'];

        if ( $args_retorno[0] != 'nok' ) {
            if ( !$retornar ) {
            $this->_uf = $args_retorno[1];
            $this->_isCapital = $args_retorno[2];
            $this->_sedex = $args_retorno[3];
            $this->_pac = $args_retorno[4];
            return true;
            } else {
                return array( 'uf' => $args_retorno[1], 'capital' => $args_retorno[2], 'valorPac' => $args_retorno[4], 'valorSedex' => $args_retorno[3] );
            }
        } else {
            return false;
        }
    }

    /**
     *  Método que realiza o acesso à URL do PagSeguro e retorna os dados encontrados
     *
     * @param string $url Endereço completo para acesso via CURL
     * @return array|bool Dados de retorno da url requisitada.
     */
    protected function _getContent( $url ) 
    {
        if( !function_exists('curl_init') )
            exit( 'A extensão CURL do PHP está desabilitada. Habilite-a para o funcionamento desta classe.' );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $retorno = curl_exec($curl);        
        curl_close($curl);

        return $retorno;
    }
}
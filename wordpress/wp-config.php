<?php
/** 
 * A configuração de base do WordPress
 *
 * Este ficheiro define os seguintes parâmetros: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, e ABSPATH. Pode obter mais informação
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} no Codex. As definições de MySQL são-lhe fornecidas pelo seu serviço de alojamento.
 *
 * Este ficheiro é usado para criar o script  wp-config.php, durante
 * a instalação, mas não tem que usar essa funcionalidade se não quiser. 
 * Salve este ficheiro como "wp-config.php" e preencha os valores.
 *
 * @package WordPress
 */

// ** Definições de MySQL - obtenha estes dados do seu serviço de alojamento** //
/** O nome da base de dados do WordPress */
define('DB_NAME', 'wordpress');

/** O nome do utilizador de MySQL */
define('DB_USER', 'root');

/** A password do utilizador de MySQL  */
define('DB_PASSWORD', '');

/** O nome do serviddor de  MySQL  */
define('DB_HOST', 'localhost');

/** O "Database Charset" a usar na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O "Database Collate type". Se tem dúvidas não mude. */
define('DB_COLLATE', '');

/**#@+
 * Chaves Únicas de Autenticação.
 *
 * Mude para frases únicas e diferentes!
 * Pode gerar frases automáticamente em {@link https://api.wordpress.org/secret-key/1.1/salt/ Serviço de chaves secretas de WordPress.org}
 * Pode mudar estes valores em qualquer altura para invalidar todos os cookies existentes o que terá como resultado obrigar todos os utilizadores a voltarem a fazer login
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '^9X3}Y7>Z0}wj1Jot#,20Z}|UV3_i+!bYEtZ>V)FzNF}2L}_[pm|C)B63l$t[:HS');
define('SECURE_AUTH_KEY',  'q@*Y-V+u=J(]8&,;AIsP/<cfxQW_,:-Xu$Qij#[0B{;X0`fG;(~t3BYgbn_fEfZ@');
define('LOGGED_IN_KEY',    'OWhA.?KFsDj=0^H]8/2*{wVx28q<r9HA@RC iK]HV)/CD8FImJ4V8IeCp=^}qo0a');
define('NONCE_KEY',        'N4kx-asLrhPm=j9Y?#j;H$1(YP>mA$[>N.Ujg!7m@:lL@TNc!HI:/:NigOy,9H q');
define('AUTH_SALT',        'WYC!Ll~q:h];VI^QVm;NoVz#B?}ep:MZjDOPzB[pN-+lSTlDB2=KkZ-H-p!Z}fQG');
define('SECURE_AUTH_SALT', 'd=X(eCU8x:EpC:Cm_CFd_hZ.[-5^;>RVO:qSW!{H6G3=_m^p/33PHiGtO$H|Bc%t');
define('LOGGED_IN_SALT',   '7JO`,h^n4d#,1+GKs3w:([v&BG0f)uOthKKVwWi93Hb=.vgK^@B_|M)YmR5@g bH');
define('NONCE_SALT',       'IS>X6KA*<{yW1>|c1J:Y^x 5?avR)@xu=6G;nu|=XXDkcki#f<i4gzL.JcBh 5+/');

/**#@-*/

/**
 * Prefixo das tabelas de WordPress.
 *
 * Pode suportar múltiplas instalações numa só base de dados, ao dar a cada
 * instalação um prefixo único. Só algarismos, letras e underscores, por favor!
 */
$table_prefix  = 'wp_';

/**
 * Idioma de Localização do WordPress, Inglês por omissão.
 *
 * Mude isto para localizar o WordPress. Um ficheiro MO correspondendo ao idioma
 * escolhido deverá existir na directoria wp-content/languages. Instale por exemplo
 * pt_PT.mo em wp-content/languages e defina WPLANG como 'pt_PT' para activar o
 * suporte para a língua portuguesa.
 */
define('WPLANG', 'pt_PT');

/**
 * Para developers: WordPress em modo debugging.
 *
 * Mude isto para true para mostrar avisos enquanto estiver a testar.
 * É vivamente recomendado aos autores de temas e plugins usarem WP_DEBUG
 * no seu ambiente de desenvolvimento.
 */
define('WP_DEBUG', false);

/* E é tudo. Pare de editar! */

/** Caminho absoluto para a pasta do WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Define as variáveis do WordPress e ficheiros a incluir. */
require_once(ABSPATH . 'wp-settings.php');

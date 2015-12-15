<?php
/**
 * Arquivo principal do sistema CATAFORTE, define todas as páginas do sistema.
 *
 * Autor: JAMN CONSULTORIA EM INFORMÁTICA
 */

require_once('include/phpvortex/APP_Base.class.php');
require_once('include/phpvortex/conf/conf.php');
require_once('include/conn.php');

session_start();

$app =& new APP_Base(array(
	'pages' => array(
		'welcome' => array(
			'class' => 'SEC_Include',
			'style' => array('cataforte.css'),
			'opts' => array(
			'include' => 'tela_inicial.php'
			),
			'layout' => array()
		),
		'bases' => array(
			'class' => 'SEC_Include',
			'style' => array('cataforte.css'),
			'opts' => array(
			'include' => 'bases.php'
			),
			'layout' => array()
		),
		'consulta' => array(
			'class' => 'SEC_Include',
			'style' => array('cataforte.css'),
			'opts' => array(
			'include' => 'consulta.php'
			)
		),
		'tabela' => array(
			'class' => 'SEC_Include',
			'style' => array('tabela.css'),
			'opts' => array(
			'include' => 'tabela_dinamica.php'
			)
		),
		'tabelapop' => array(
			'class' => 'SEC_Include',
			'style' => array('tabela.css'),
			'opts' => array(
				'include' => 'tabela_dinamica.php'
			),
			'layout' => array(
				array(
					'name' => 'content',
					'class' => 'SEC_Static',
					'opts' => array()
				),
				array(
					'name' => 'rodape',
					'class' => 'SEC_Static',
					'opts' => array(
						'include' => 'rodape.html'
					)
				)
			)
		),
		'varinf' => array(
			'class' => 'SEC_Include',
			'opts' => array(
				'include' => 'varinf.php'
			)
		),
		'varinfpop' => array(
			'class' => 'SEC_Include',
			'style' => array('cataforte.css'),
			'opts' => array(
				'include' => 'varinf.php'
			),
			'layout' => array()
		)
	),
	'layout' => array(
		array(
			'name' => 'menu',
			'class' => 'SEC_Include',
			'opts' => array(
			'include' => 'menu.php'
			)
		),
		array(
			'name' => 'content',
			'class' => 'SEC_Static',
			'opts' => array()
		),
		array(
			'name' => 'rodape',
			'class' => 'SEC_Static',
			'opts' => array(
			'include' => 'rodape.html'
			)
		)
	),
	'styles' => array('paep.css'),
	'title' => 'Cataforte - Sistema de Tabula&ccedil;&atilde;o de Dados',
    //'onload' => "",
	'connection_class' => 'DB_MySQL',
	'connection_opts' => $db_conn
));

if (!isset($_REQUEST['page'])) {
	$app->Show('welcome');
} else {
	$app->Show($_REQUEST['page']);
}

?>
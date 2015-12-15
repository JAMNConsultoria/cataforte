<?php
/**
 * Arquivo principal da PAEP, define todas as pсginas do sistema.
 *
 * Autor: Thiago Ramon Gonчalves Montoya, para a Fundaчуo SEADE
 */

require_once('phpvortex/APP_Base.class.php');
require_once('phpvortex/conf/conf.php');
require_once('conn.php');

session_start();

$app =& new APP_Base(array(
	'pages' => array(
		'welcome' => array(
			'class' => 'SEC_Include',
			'style' => array('estilo.css'),
			'opts' => array(
				'include' => 'welcome.php'
			),
			'layout' => array()
		),
		'bases' => array(
			'class' => 'SEC_Include',
			'style' => array('estilo.css'),
			'opts' => array(
				'include' => 'bases.php'
			),
			'layout' => array()
		),
		'consulta' => array(
			'class' => 'SEC_Include',
			'style' => array('estilo.css'),
			'opts' => array(
				'include' => 'consulta.php'
			)
		),
		'tabela' => array(
			'class' => 'SEC_Include',
			'style' => array('tabela.css'),
			'opts' => array(
				'include' => 'tabela.php'
			)
		),
		'tabelapop' => array(
			'class' => 'SEC_Include',
			'style' => array('tabela.css'),
			'opts' => array(
				'include' => 'tabela.php'
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
						'file' => '../../rodape.html'
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
			'style' => array('estilo.css'),
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
				'file' => '../../rodape.html'
			)
		)
	),
	'styles' => array('paep.css'),
	'title' => 'Pesquisa da Atividade Econ&ocirc;mica Paulista - 2001',
	'connection_class' => 'DB_MySQL',
	'connection_opts' => $db_conn
));

if (!isset($_REQUEST['page'])) {
	$app->Show('welcome');
} else {
	$app->Show($_REQUEST['page']);
}

?>
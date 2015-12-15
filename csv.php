<?php
require_once('include/phpvortex/DB_MySQL.class.php');
require_once('include/TB_Dados.class.php');
require_once('include/phpvortex/conf/conf.php');
require_once('include/conn.php');

session_start();

if (isset($_REQUEST['posicao'])) $_SESSION['posicao'] = $_REQUEST['posicao'];
if (isset($_REQUEST['filtro'])) $_SESSION['filtro'] = $_REQUEST['filtro'];

header("Content-type: text/comma-separated-values");
header("Content-Disposition: attachment; filename=consulta.csv");
$db =& new DB_MySQL($db_conn);
$db->Connect();
$tb =& new TB_Dados($db);
$tb->ShowCross($_SESSION['base'], $_SESSION['posicao'], $_SESSION['filtro'], TRUE);
?>
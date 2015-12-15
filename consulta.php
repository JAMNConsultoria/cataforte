<?php
if (!isset($_SESSION['vars'])) $_SESSION['vars'] = array();
if (!isset($_SESSION['posicao'])) $_SESSION['posicao'] = array();
if (!isset($_SESSION['filtro'])) $_SESSION['filtro'] = array();

$show = 0;
if (isset($_REQUEST['action'])) {
	if (!strcmp($_REQUEST['action'], 'new')) {
		$_SESSION['vars'] = array();
		$_SESSION['posicao'] = array();
		$_SESSION['filtro'] = array();
	} elseif (!strcmp($_REQUEST['action'], 'var_list')) {
		$show = 1;
	} elseif (!strcmp($_REQUEST['action'], 'var_pred')) {
		$show = 2;
	} elseif (!strcmp($_REQUEST['action'], 'var_save')) {
		$show = $_REQUEST['dest'];
		if (isset($_REQUEST['vars'])) {
			foreach($_REQUEST['vars'] as $var) $_SESSION['vars'][] = $var;
			$_SESSION['vars'] = array_unique($_SESSION['vars']);
		}
	} elseif (!strcmp($_REQUEST['action'], 'var_rem')) {
		$show = $_REQUEST['dest'];
		if (isset($_REQUEST['vars'])) {
			foreach($_REQUEST['vars'] as $var) $_SESSION['vars'][] = $var;
			$_SESSION['vars'] = array_unique($_SESSION['vars']);
		}
		if (isset($_REQUEST['rmvars'])) {
			foreach ($_REQUEST['rmvars'] as $var)
				unset($_SESSION['vars'][array_search($var, $_SESSION['vars'])]);
		}
	}
}
if (empty($_SESSION['vars']) || $show) {
	if ($show == 1) {
		include('var_list.php');
	} else {
		include('var_pred.php');
	}
} else {
	include('var_org.php');
}
?>
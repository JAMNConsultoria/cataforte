<?php
if (isset($_REQUEST['base'])) {
	$_SESSION['base'] = $_REQUEST['base'];
}
include("cabec.html");
?>

<div id="back">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" height="20">
		<tr>
	<?php if (!strcmp($_REQUEST['page'], 'tabela') || !strcmp($_REQUEST['page'], 'varinf') || (!strcmp($_REQUEST['page'], 'consulta') && ((empty($_REQUEST['dest']) && (!isset($_REQUEST['action']) || (strcmp($_REQUEST['action'], 'var_pred') && strcmp($_REQUEST['action'], 'new')))) || (!empty($_REQUEST['dest']) && ($_REQUEST['dest'] == 1)) || !strcmp($_REQUEST['action'], 'var_list')))) { ?>
			<td width="73" rowspan="3"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo (!strcmp($_REQUEST['page'], 'varinf')?'tabela':'consulta'); ?><?php if (!strcmp($_REQUEST['page'], 'consulta') && ((!isset($_REQUEST['action']) || strstr($_REQUEST['action'], 'list')) || (!empty($_REQUEST['dest']) && ($_REQUEST['dest'] == 1)))) echo '&action=var_pred'; ?>"><img src="img/bt_voltar.gif" border="0"></a></td>
	<?php } ?>
			<td width="120" rowspan="3"><a href="<?php echo $_SERVER['PHP_SELF']; ?><?php if (isset($_SESSION['base']) && ($_SESSION['base']=='imp_distritos')) echo "distritos/"; ?>"><img src="img/bt_pag_ini.gif" border="0"></a></td>
			<td width="106" rowspan="3"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=new"><img src="img/bt_nova.gif" border="0"></a></td>
			<td width="106" rowspan="3"><img src="img/bt_acesso.gif" border="0"></td>
			<td bgcolor="#666632" height="2"></td>
		</tr>
		<tr>
			<td bgcolor="ffffff" height="1"></td>
		</tr>
		<tr>

			<td>
				<table cellpadding="0" cellspacing="1" border="0" width="100%" height="17" bgcolor="#666632">
					<tr>
						<td bgcolor="ffffff"><font class="v10">&nbsp;<?php
	$rs = $GLOBALS['app']->db->Query("SELECT base_desc_menu FROM tb_bases WHERE base_tabela = '{$_SESSION['base']}'");
	$base = $rs->Row();
	echo $base['base_desc_menu'];
	$rs->Close();
?></font></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
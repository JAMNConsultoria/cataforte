<script language="javascript">
function AbreNota(valor) {
	wurl="<?php echo $_SERVER['PHP_SELF']; ?>?page=varinfpop&var=";
	window.open(wurl+valor,"varinfpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=1,resizable=no,width=600,height=250");
	return false;
}

function ChkFiltro(me, filtro) {
	var ds = document.getElementById(filtro).style;
	if (me.checked) {
		ds.display = '';
	} else {
		ds.display = 'none';
	}
}

function GoVar(act) {
	if (act == 'add') {
		document.pred.dest.value='1';
		document.pred.submit();
	} else if (act == 'del') {
		document.pred.dest.value='2';
		document.pred.action='<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_rem';
		document.pred.submit();
	}
	return false;
}
</script>
<div id="back">
<form name="pred" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_save" method="post">
<input type="hidden" name="dest" value="0">
<br />
	<div id="conteudo2">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td><!--img src="img/tabin_top_left.gif"--></td><td align="right"><!--img src="img/tabin_top_right.gif"--></td></tr>
        </table>
		<div id="fundo_branco">
   		    <img src="img/seta_cab.gif" align="absmiddle"><font class="v10" color="#000000">&nbsp;<b>SELECIONAR VARI&Aacute;VEIS POR TEMA E ASSUNTO:&nbsp;</b></font>

            <br />

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?php if (!empty($_SESSION['vars'])) { ?>
				<tr>
					<td width="688"><select name="rmvars[]" multiple size="4" style="width:688px; font-family:verdana, Arial, Helvetica, sans-serif; font-size:9px;">
<?php
$vars = array();
foreach ($_SESSION['vars'] as $var) {
	$pc = explode("_", $var);
	$vars[] = $pc[0];
}
$rs = $GLOBALS['app']->db->Query("SELECT var_cod, var_nome FROM tb_{$_SESSION['base']}_variavel WHERE var_cod IN ('".implode("', '",$vars)."') ORDER BY var_ordem");
$vars = array();
while ($row = $rs->Row()) $vars[$row['var_cod']] = $row['var_nome'];
foreach ($_SESSION['vars'] as $var) {
	$pc = explode("_", $var);
	if (empty($pc[1])) {
?>
						<option value="<?php echo $var; ?>"><?php echo $vars[$var]; ?></option>
<?php } else { ?>
						<option value="<?php echo $var; ?>"><?php echo "{$vars[$pc[0]]} - {$pc[1]}"; ?></option>
<?php } } ?>
					</select><br />&nbsp;</td>
				</tr>
<?php } ?>
				<tr>
				<?php if (!empty($_SESSION['vars'])) { ?>
					<td align="center"><a href="javascript:GoVar('add');"><img src="img/bt_adiciona.gif" border="0"></a><img src="img/dot.gif" width="50" height="0">
				      <a href="javascript:GoVar('del');"><img src="img/bt_remove.gif" border="0"></a><br /><br /></td>
				<?php } else { ?>
					<td><br /><br /><a href="javascript:GoVar('add');"><img src="img/bt_selvar.gif" border="0"></a><br /><br /></td>
				<?php } ?>
				</tr>
			</table>
		</div>
        
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr><td><!--img src="img/tabin_bottom_left.gif"--></td><td align="right"><!--img src="img/tabin_bottom_right.gif"--></td></tr>
	</table>
	</div>

	<br />
<?php
$rs_cat = $GLOBALS['app']->db->Query("SELECT cat_cod, cat_desc FROM tb_{$_SESSION['base']}_categ WHERE cat_pai = -1 ORDER BY cat_desc");
while ($row_cat = $rs_cat->Row()) {
?>
	<div id="conteudo2">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td><!--img src="img/tabin_top_left.gif"--></td><td align="right"><!--img src="img/tabin_top_right.gif"--></td></tr>
        </table>
        
        <br />
        <img src="img/seta_cab.gif" align="absmiddle"><font class="v10" color="#000000">&nbsp;<b><?php echo $row_cat['cat_desc']; ?></b></font>
    
		<div id="fundo_branco">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
        <?php
        $rs = $GLOBALS['app']->db->Query("SELECT var_cod, var_nome, var_formato FROM tb_{$_SESSION['base']}_variavel WHERE var_categ = {$row_cat['cat_cod']} ORDER BY var_ordem");
        while ($row = $rs->Row()) {
        ?>
			<tr>
				<td width="24"><input type="checkbox" name="vars[]" value="<?php echo $row['var_cod']; ?>" <?php if (in_array($row['var_cod'], $_SESSION['vars'])) echo " checked"; ?>></td>
				<td class="v11"> <?php echo $row['var_nome']; ?> [<a href="#" onClick="return AbreNota('<?php echo $row['var_cod']; ?>');" class="a1">defini&ccedil;&atilde;o</a>]</td>
			</tr>
		<?php } ?>
		</table>
		</div>

        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td><img src="img/tabin_bottom_left.gif"></td><td align="right"><img src="img/tabin_bottom_right.gif"></td></tr>
        </table>
	</div>

	<br />
<?php } ?>


	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="4AB9E6" height="5">
		<tr valign="bottom"><td><!--img src="img/tabex_bottom_left.gif"--></td><td align="right"><!--img src="img/tabex_bottom_right.gif"--></td></tr>
	</table>

</div>

<div id="botoes" align="center" style="position:absolute; left:1px; top:5px; width:50px; height:50px; z-index:100">
<input type="image" src="img/bt_confirmar.gif" border="0"><br /></div>

<script language="javascript" src="float.js"></script>
<script language="javascript">flevInitPersistentLayer('botoes',0,'','','','','','75',100,100);</script>
</form>
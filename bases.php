<?php
function ListaBases($pai, $dados = NULL) {
	global $app;
	$top = FALSE;
	if (empty($dados)) {
		$top = TRUE;
		$dados = array();
		$rs =& $app->db->Query("SELECT * FROM tb_bases");
		while ($row = $rs->Row()) {
			$dados['bases'][$row['base_cod']]['nome'] = $row['base_desc'];
			$dados['bases'][$row['base_cod']]['tabela'] = $row['base_tabela'];
			$dados['filhos'][$row['base_pai']][] = $row['base_cod'];
		}
		$rs->Close();
	}

	$filhos = array();
	foreach ($dados['filhos'][$pai] as $row) {
		$filhos[$dados['bases'][$row]['nome']]['cod'] = $row;
		$filhos[$dados['bases'][$row]['nome']]['nome'] = $dados['bases'][$row]['nome'];
		$filhos[$dados['bases'][$row]['nome']]['tabela'] = $dados['bases'][$row]['tabela'];
	}
	ksort($filhos);
	foreach ($filhos as $row) if (empty($row['tabela'])) { ?>
<a href="javascript:GoBase(<?php echo $row['cod']; ?>);"><img src="img/bt_mais<?php echo ($top?'':'2'); ?>.gif" border="0" align="absmiddle">&nbsp;<?php echo $row['nome']; ?></a><br />
	<div style="display:none" id="base_<?php echo $row['cod']; ?>">
	<?php ListaBases($row['cod'], $dados); ?>
	</div>
<?php } else {
		$rs =& $app->db->Query("SELECT COUNT(*) as vars FROM tb_{$row['tabela']}_variavel WHERE var_categ > 0");
		$tmp = $rs->Row();
		$vars = $tmp['vars'];
		$rs->Close();
?>
<a href='<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=new&base=<?php echo $row['tabela']; ?>'><img src="img/bt_seta<?php echo ($top?'5':'4'); ?>.gif" border="0" align="absmiddle">&nbsp;<?php echo $row['nome']; ?></a> <font size="1">(<?php echo $vars; ?> vari&aacute;veis)</font><br />
<?php }
}
?>
<script language="javascript">
function AbreNota(valor) {
	wurl="<?php echo $_SERVER['PHP_SELF']; ?>?page=varinfpop&var=";
	window.open(wurl+valor,"varinfpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=1,resizable=no,width=600,height=250");
	return false;
}

function GoBase(base) {
	var s = document.getElementById('base_'+base).style;
	if (s.display == "") {
		s.display = "none";
	} else {
		s.display = "";
	}
}
</script>
<br>
<div id="back">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="4AB9E6" height="5">
		<tr valign="top"><td><img src="img/tabex_top_left.gif"></td><td align="right"><img src="img/tabex_top_right.gif"></td></tr>
	</table>
<br>
	<div id="conteudo2" style="background-color:#FFFFFF;">
	<table cellpadding="0" cellspacing="0" border="0" width="734">
		<tr><td><img src="img/tabin_top_left.gif"></td><td align="right"><img src="img/tabin_top_right.gif"></td></tr>
	</table>
		<div id="fundo_branco">
        <div id="top2">
			<table cellpadding="0" cellspacing="0" border="0" width="80%" style="margin-left:30px">
		<tr>
			<td valign="top"><font face="verdana" size="2">
 			<p>Selecione uma das bases abaixo para iniciar sua pesquisa.<br>
<font size="1">(Ao selecionar todas as empresas, unidades locais ou setores, opta-se pelas variáveis comuns a essas bases, restringindo-se portanto o universo de variáveis acessíveis)</font></p>
			<div><?php ListaBases(1); ?></div><br>
			</font>
			</td>
		</tr>
	</table>
	</div>
</div>
	<table cellpadding="0" cellspacing="0" border="0" width="734">
		<tr><td><img src="img/tabin_bottom_left.gif"></td><td align="right"><img src="img/tabin_bottom_right.gif"></td></tr>
	</table>
	</div>
	<br>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="4AB9E6" height="5">
		<tr valign="bottom"><td><img src="img/tabex_bottom_left.gif"></td><td align="right"><img src="img/tabex_bottom_right.gif"></td></tr>
	</table>
</div>
<br>

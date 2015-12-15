<script language="javascript">
function AbreNota(valor) {
	wurl="<?php echo $_SERVER['PHP_SELF']; ?>?page=varinfpop&var=";
	window.open(wurl+valor,"varinfpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=1,resizable=no,width=600,height=250");
	return false;
}
</script>

<?php include("cabec.html");?>
<div id="back">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#666632" height="5">
		<tr valign="top"><td><!--img src="img/tabex_top_left.gif"--></td><td align="right"><!--img src="img/tabex_top_right.gif"--></td></tr>
	</table>

	<br />
	
    <div id="conteudo2" style="text-align: center">
        <table cellpadding="5" cellspacing="2" border="0" width="100%" >
            <tr><td><img src="img/tabin_top_left.gif"></td><td align="right"><img src="img/tabin_top_right.gif"></td></tr>
        </table>
        <div style="padding: 10px 20px 10px 20px; font-family: verdana,arial; font-size: 12px; width: 95%; text-align: justify">
            <p class="tit"><b>Sistema de Tabulação de Dados</b><br /><b>BRASIL</b></p>
<p>O projeto Cataforte - Fortalecimento do Associativismo e Cooperativismo dos Catadores de Materiais Recicláveis tem contribuído para a transformação da vida de milhares de catadores em todo o Brasil. </p>

<p>O Cataforte é fruto da parceria entre a Fundação do Banco do Brasil e a Secretaria Nacional de Economia Solidária do Ministério do Trabalho e Emprego e envolve a capacitação de 10.600 catadores em 17 estados e no Distrito Federal.</p>

<p>Seu objetivo é mobilizar os catadores e estimular sua organização em cooperativas e associações, fortalecendo sua autonomia para gerir e atuar nas diferentes etapas da cadeia produtiva de recicláveis. <br/>O projeto prevê, ainda, equipar as organizações dos catadores, facilitando o transporte, logística e infraestrutura adequada para o trabalho.</p>

        </div>
    
        <table cellpadding="5" cellspacing="2" border="0" width="100%">
            <tr><td><!--img src="img/tabin_bottom_left.gif"--></td><td align="right"><!--img src="img/tabin_bottom_right.gif"--></td></tr>
        </table>
	</div>

	<br />
    
<?php
function ListaBases($pai, $dados = NULL) {
	global $app;
	$top = FALSE;
	if (empty($dados)) {
		$top = TRUE;
		$dados = array();
		$rs =& $app->db->Query("SELECT * FROM tb_bases  where base_cod >= 2000 and base_cod <3000");
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
<a style="color: #666632; font-weight: bold;text-decoration: none;" href="javascript:GoBase(<?php echo $row['cod']; ?>);"><img src="img/bt_mais<?php echo ($top?'':'2'); ?>.gif" border="0" align="absmiddle">&nbsp;<?php echo $row['nome']; ?></a><br />
	<div style="display:block" id="base_<?php echo $row['cod']; ?>">
	<?php ListaBases($row['cod'], $dados); ?>
	</div>
<?php } else {
		$rs =& $app->db->Query("SELECT COUNT(*) as vars FROM tb_{$row['tabela']}_variavel WHERE var_categ > 0");
		$tmp = $rs->Row();
		$vars = $tmp['vars'];
		$rs->Close();
		$rs =& $app->db->Query("SELECT COUNT(DISTINCT v.var_cod) as vars FROM tb_{$row['tabela']}_variavel AS v LEFT JOIN tb_{$row['tabela']}_rel_ter_var USING (var_cod) WHERE NOT ISNULL(ter_cod)");
		$tmp = $rs->Row();
		$vars += $tmp['vars'];
		$rs->Close();
?>
<a style="color: #666632;" href='<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=new&base=<?php echo $row['tabela']; ?>'><img src="img/bt_seta<?php echo ($top?'5':'4'); ?>.gif" border="0" align="absmiddle">&nbsp;<?php echo $row['nome']; ?></a> <font size="1"> {<?php echo $vars; ?> vari&aacute;veis}</font><br />
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
	<div id="conteudo2">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td><img src="img/tabin_top_left.gif"></td><td align="right"><img src="img/tabin_top_right.gif"></td></tr>
        </table>
        <div id="fundo_branco2" style="padding: 10px 20px 10px 20px; font-family: verdana,arial; font-size: 12px; width: 100%;">
            <p>Selecione uma das bases abaixo para iniciar sua pesquisa.</p>
            <div id="top2"><?php ListaBases(2000); ?></div>
        </div>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td><img src="img/tabin_bottom_left.gif"></td><td align="right"><img src="img/tabin_bottom_right.gif"></td></tr>
        </table>
	</div>

	<br />

	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#666632" height="5">
		<tr valign="bottom"><td><!--img src="img/tabex_bottom_left.gif"--></td><td align="right"><!--img src="img/tabex_bottom_right.gif"--></td></tr>
	</table>
</div>

<?php include ("rodape.html"); ?>
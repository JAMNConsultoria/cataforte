<?php
require_once('include/TB_Dados.class.php');

if (isset($_REQUEST['posicao'])) $_SESSION['posicao'] = $_REQUEST['posicao'];
if (isset($_REQUEST['filtro'])) $_SESSION['filtro'] = $_REQUEST['filtro'];

$ops = array('S' => 'Soma de ', 'M' => 'Média de ', 'F' => '');

$var_cods = array();
foreach ($_SESSION['vars'] as $var) {
	$pc = explode("_", $var);
	$var_cods[] = $pc[0];
}

$linhas = $colunas = $conteudos = array();
foreach ($_SESSION['posicao'] as $var => $row) {
	if ($row['pos'] == 'L') {
		$linhas[$row['ordem'].$var] = $var;
	} elseif ($row['pos'] == 'C') {
		$colunas[$row['ordem'].$var] = $var;
	} elseif (($row['pos'] == 'F') || ($row['pos'] == 'S') || ($row['pos'] == 'M')) {
		$conteudos[$row['ordem'].$var] = $var;
	}
}
ksort($linhas);
$linhas = array_values($linhas);
ksort($colunas);
$colunas = array_values($colunas);
ksort($conteudos);
$conteudos = array_values($conteudos);

$sql = "SELECT var_cod, var_nome, var_formato FROM tb_{$_SESSION['base']}_variavel WHERE var_cod IN ('".implode("', '", $var_cods)."')";
$rs = $GLOBALS['app']->db->Query($sql);
$vars = array('frequencia' => array('nome' => 'Freqüência'));
while ($row = $rs->Row()) {
	$vars[$row['var_cod']]['nome'] = $row['var_nome'];
	if ($row['var_formato'] > 0) {
		$sql = "SELECT fmt_val, fmt_desc FROM tb_formato_valor WHERE fmt_cod = {$row['var_formato']} ORDER BY fmt_desc";
		$rsf = $GLOBALS['app']->db->Query($sql);
		while ($rowf = $rsf->Row())
			$vars[$row['var_cod']]['formato'][$rowf['fmt_val']] = $rowf['fmt_desc'];
		$rsf->Close();
	}
}
$rs->Close();
?>
<script language="javascript">
function AbreNota(valor) {
	wurl="<?php echo $_SERVER['PHP_SELF']; ?>?page=varinfpop&var=";
	window.open(wurl+valor,"varinfpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=1,resizable=no,width=600,height=250");
	return false;
}
</script>
<br>
<br />
<strong>Resumo da tabela</strong>:<br />
<?php if (!empty($linhas)) { ?>
<strong>Vari&aacute;vel(eis) na(s) Linha(s)</strong>: <?php foreach ($linhas as $linha) {
	$pc = explode('_', $linha);
	echo $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . "; ";
} ?><br />
<?php } ?>
<?php if (!empty($colunas)) { ?>
<strong>Vari&aacute;vel(eis) na(s) Coluna(s)</strong>: <?php foreach ($colunas as $coluna) {
	$pc = explode('_', $coluna);
	echo $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . "; ";
} ?><br />
<?php } ?>
<?php if (!empty($conteudos)) { ?>
<strong>Vari&aacute;vel(eis) no Conteúdo</strong>: <?php foreach ($conteudos as $conteudo) {
	$pc = explode('_', $conteudo);
	echo $ops[$_SESSION['posicao'][$conteudo]['pos']] . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . "; ";
} ?><br />
<?php } ?>
<br>
<?php
$fc = 0;
$filtros = array();
foreach ($_SESSION['filtro'] as $var_cod => $filtro) if (isset($filtro['tipo']) && !(strcmp($filtro['tipo'], 'C') && strcmp($filtro['tipo'], 'L'))) {
	$fc++;
	$filtros[] = $var_cod;
}

if ($fc) {
?>
<strong>Filtros Utilizados</strong>:<br>
<?php
	foreach ($filtros as $var) {
		$pc = explode("_", $var);
		if (empty($vars[$pc[0]]['formato'])) {
?>
<b>Vari&aacute;vel:</b> <?php echo $vars[$pc[0]]['nome'].(empty($pc[1])?'':' - '.$pc[1]).' - <b>Conte&uacute;do:</b> '.$_SESSION['filtro'][$var]['op'].'&nbsp;'.$_SESSION['filtro'][$var]['val']; ?><br>
<?php } else { ?>
<b>Vari&aacute;vel:</b> <?php echo $vars[$pc[0]]['nome'].(empty($pc[1])?'':' - '.$pc[1]) ?> - <b>Conte&uacute;do:</b> <?php 
	$cnt = 0;
	foreach ($_SESSION['filtro'][$var]['lista'] as $fmt) echo ((!$cnt++)?'':"; \n").$vars[$pc[0]]['formato'][$fmt];
 ?><br>
<?php } ?>
<?php } ?>
<?php } ?>
<br />
<br>
<?php
$tb =& new TB_Dados($GLOBALS['app']->db);
$tb->ShowCross($_SESSION['base'], $_SESSION['posicao'], $_SESSION['filtro']);
?>
<div style="position:relative; left:20px"><table width="95%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="1"><strong><a href="imp.php?page=varinfpop" target="_blank">Fontes e Notas</a></strong></font></td>
</tr>
</table></div>
<br />
<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="30%">&nbsp;</td>
<td width="40%">
<table width=100% cellpadding=0 cellspacing=5 border="0" bgcolor="#E3EFFC" class="tabela">
  <tr>
		<td colspan=2 align="center" height="30"><p><font color="#ff6600"><b>Conven&ccedil;&otilde;es Utilizadas</b></font></td>

	</tr>
	<tr>
		<td><font color="ff6600"><b>-</b></font></td>
		<td>Fen&ocirc;meno inexistente </td>
	</tr>
  <tr>
		<td colspan=8 align="center" height="10"></td>
	</tr>
</table>
</td>
<td width="30%">&nbsp;</td>
</tr>
</table>
<br />
<br />
<p align="center"><a href="csv.php"><img src="img/bt_down.gif" title="Baixar o resultado em um arquivo texto CSV" border="0"></a></p>
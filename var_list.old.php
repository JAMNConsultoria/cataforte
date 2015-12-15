<?php
if (!isset($_SESSION['vars'])) $_SESSION['vars'] = array();

function ExplodeAnos($texto) {
	$tmp = array();
	$blocos = explode("/",trim($texto));
	foreach ($blocos as $bloco) {
		if (!empty($bloco)) {
			$anos = explode("-",trim($bloco));
			if (count($anos) == 2) {
				for ($i = $anos[0];$i <= $anos[1];$i++) $tmp[] = $i;
			} elseif (count($anos) == 1) {
				$tmp[] = $anos[0];
			}
		}
	}
	return $tmp;
}

function RecAdd(&$todos, $termos, &$filhos) {
	foreach ($termos as $termo) {
		if (!empty($filhos[$termo])) {
			RecAdd($todos, $filhos[$termo], $filhos);
		}
		$todos[] = $termo;
	}
}

function MontaVars($pai, $dados = NULL) {
	global $app;
	if (empty($dados)) {
		$dados = array();
		$rs =& $app->db->Query("SELECT t.ter_cod, t.ter_nome, t.ter_ordem FROM tb_{$_SESSION['base']}_rel2_ter AS r LEFT JOIN tb_termo2 AS t USING (ter_cod) WHERE r.ter_tipo = 0");
		while ($row = $rs->Row()) {
			$dados['termos'][$row['ter_cod']]['nome'] = $row['ter_nome'];
			$dados['termos'][$row['ter_cod']]['ordem'] = $row['ter_ordem'].'_'.$row['ter_nome'];
			$dados['ter_rel'][$row['ter_cod']]['pai'] = array();
			$dados['ter_rel'][$row['ter_cod']]['filho'] = array();
			$dados['ter_rel'][$row['ter_cod']]['rel'] = array();
			$dados['ter_var'][$row['ter_cod']] = array();
		}
		$rs->Close();
		$dados['ter_rel'][0]['rel'] = array();
		$rs =& $app->db->Query("SELECT ter_cod, ter_rel, ter_tipo FROM tb_termo2_relacionado");
		while ($row = $rs->Row()) if (isset($dados['termos'][$row['ter_cod']])) {
			if ($row['ter_tipo'] == 0) {
				$dados['ter_rel'][$row['ter_cod']]['pai'][] = $row['ter_rel'];
				$dados['ter_rel'][$row['ter_rel']]['filho'][] = $row['ter_cod'];
			} else {
				$dados['ter_rel'][$row['ter_cod']]['rel'][] = $row['ter_rel'];
			}
		}
		$rs->Close();
		$dados['ter_var'][0] = array();
		$rs =& $app->db->Query("SELECT var_cod, ter_cod FROM tb_{$_SESSION['base']}_rel_ter_var WHERE var_tipo = 0");
		while ($row = $rs->Row()) {
			$dados['ter_var'][$row['ter_cod']][] = $row['var_cod'];
			$dados['var_ter'][$row['var_cod']][] = $row['ter_cod'];
		}
		$rs->Close();
		$rs =& $app->db->Query("SELECT var_cod, var_nome, var_formato, var_ops, var_ordem FROM tb_{$_SESSION['base']}_variavel order by var_ordem");
		$keys = array_keys($dados['var_ter']);
		while ($row = $rs->Row()) if (in_array($row['var_cod'], $keys)) {
			$dados['vars'][$row['var_cod']]['nome'] = $row['var_nome'];
			$dados['vars'][$row['var_cod']]['formato'] = $row['var_formato'];
			$dados['vars'][$row['var_cod']]['ops'] = $row['var_ops'];
			$dados['vars'][$row['var_cod']]['ordem'] = $row['var_ordem'];
		}
		$rs->Close();
		$dados['nohide'] = array();
		foreach ($_SESSION['vars'] as $var) if (isset($dados['var_ter'][$var])) {
			foreach ($dados['var_ter'][$var] as $ter) {
				$dados['nohide'][] = $nh = $ter;
				while ($nh > 0) {
					foreach ($dados['ter_rel'][$nh]['pai'] as $row) $dados['nohide'][] = $nh = $row;
				}
			}
		}
		if (isset($_REQUEST['termo'])) $_REQUEST['goto'] = $_REQUEST['termo'];
		if (isset($_REQUEST['goto'])) {
			$dados['nohide'][] = $nh = $_REQUEST['goto'];
			while ($nh > 0) {
				$last = $nh;
				foreach ($dados['ter_rel'][$nh]['pai'] as $row) $dados['nohide'][] = $nh = $row;
			}
			$_REQUEST['termo'] = $last;
		}
	}

	if (isset($_REQUEST['vars'])) {
		foreach($_REQUEST['vars'] as $var) $_SESSION['vars'][] = $var;
		$_SESSION['vars'] = array_unique($_SESSION['vars']);
	}

	$filhos = array();
	foreach ($dados['ter_rel'][$pai]['filho'] as $row) if (array_key_exists($row, $dados['termos'])) {
		$filhos[$dados['termos'][$row]['ordem']]['cod'] = $row;
		$filhos[$dados['termos'][$row]['ordem']]['nome'] = $dados['termos'][$row]['nome'];
	}
	ksort($filhos);
	foreach ($filhos as $row) if ($pai != 0) { ?>
<a href="javascript:GoTermo(<?php echo $row['cod']; ?>, false);"><img src="img/bt_mais2.gif" border="0" align="absmiddle"></a>&nbsp;
&nbsp;<a href="javascript:GoTermo(<?php echo $row['cod']; ?>, false);"><?php echo $row['nome']; ?></a><br />
	<div <?php if (!in_array($row['cod'], $dados['nohide']) && !((count($dados['ter_rel'][$row['cod']]['filho']) <= 1) && empty($dados['ter_var'][$row['cod']]))) echo 'style="display:none" '; ?>id="termo_<?php echo $row['cod']; ?>">
	<?php MontaVars($row['cod'], $dados); ?>
	</div>
<?php } else { ?>
<?php if (isset($_REQUEST['termo']) && ($_REQUEST['termo'] == $row['cod'])) { ?>
<a href="javascript:GoTermo(<?php echo $row['cod']; ?>, false);"><img src="img/bt_mais.gif" border="0" align="absmiddle"></a>&nbsp;&nbsp;<a href="javascript:GoTermo(<?php echo $row['cod']; ?>, false);"><?php echo $row['nome']; ?></a><br />
	<div id="termo_<?php echo $row['cod']; ?>">
	<?php MontaVars($row['cod'], $dados); ?>
	</div>
<?php } else { ?>
<a href="javascript:LoadTermo(<?php echo $row['cod']; ?>);"><img src="img/bt_mais.gif" border="0" align="absmiddle"></a>&nbsp;&nbsp;<a href="javascript:LoadTermo(<?php echo $row['cod']; ?>);"><?php echo $row['nome']; ?></a><br />
<?php } ?>
<?php }

	$filhos = array();
	foreach ($dados['ter_rel'][$pai]['rel'] as $row) if (array_key_exists($row, $dados['termos'])) $filhos[$row] = $dados['termos'][$row]['nome'];
	asort($filhos);
	foreach ($filhos as $cod => $nome) { ?>

<img src="img/asterisco2.gif" border="0"><i>Ver tamb&eacute;m</i>:&nbsp;<a href="javascript:GoTermo(<?php echo $cod; ?>, true);"><?php echo $nome; ?></a><br>

<?php }
	$filhos = array();
	foreach ($dados['ter_var'][$pai] as $row) {
		$filhos[$dados['vars'][$row]['ordem']]['cod'] = $row;
		$filhos[$dados['vars'][$row]['ordem']]['nome'] = $dados['vars'][$row]['nome'];
		$filhos[$dados['vars'][$row]['ordem']]['formato'] = $dados['vars'][$row]['formato'];
		$filhos[$dados['vars'][$row]['ordem']]['ops'] = $dados['vars'][$row]['ops'];
	}
	ksort($filhos);
	$cor = 0;
	if (count($filhos) > 1) { ?>
		<table border="0" cellpadding="0" cellspacing="0" width="625">
			<tr <?php if ($cor++ % 2) echo 'bgcolor="#EFEFEF"'; ?>>
				<td width="12"><img src="img/bt_seta2.gif" border="0"></td>
				<td width="20"><input type="checkbox" onClick="SelAll(this);"></td>
				<td width="20">&nbsp;</td>
				<td class="v11"><i>Selecionar todas as vari&aacute;veis deste n&iacute;vel</i></td>
			</tr>
		</table>
<?php
	}
	foreach ($filhos as $row) { ?>
		<table border="0" cellpadding="0" cellspacing="0" width="625">
			<tr <?php if ($cor++ % 2) echo 'bgcolor="#EFEFEF"'; ?>>
				<td width="12"><img src="img/bt_seta2.gif" border="0"></td>
				<td width="20"><input type="checkbox" name="vars[]" value="<?php echo $row['cod']; ?>"<?php if (in_array($row['cod'], $_SESSION['vars'])) echo ' checked'; ?>></td>
				<td width="20"><img src="img/bt_<?php echo ($row['formato'] == 0)?'quant':(($row['ops'] & 256)?'mista':'cat'); ?>.gif" title="<?php echo ($row['formato'] == 0)?'Vari&aacute;vel Quantitativa - Pode ser usada como conte&uacute;do da tabela, usada nos c&aacute;lculos.':(($row['ops'] & 256)?'Vari&aacute;vel Mista - Pode ser usada como Variável Categórica ou Quantitativa.':'Vari&aacute;vel Categ&oacute;rica - Pode ser usada nas linhas ou colunas da tabela.'); ?>" align="absmiddle"></td>
				<td class="v11"><?php echo $row['nome']; ?> <a href="#" onClick="return AbreNota('<?php echo $row['cod']; ?>');" class="a1"><img src="img/icone_info.jpg" title="definição" border="0"></td>
			</tr>
		</table>
<?php }
}
?>

<script language="javascript">
function AbreNota(valor) {
	wurl="<?php echo $_SERVER['PHP_SELF']; ?>?page=varinfpop&var=";
	window.open(wurl+valor,"varinfpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=1,resizable=no,width=600,height=250");
	return false;
}

function GoAnos(v) {
	var t = document.getElementById('var_'+v);
	if (t.style.display == '') {
		t.style.display = 'none';
	} else {
		t.style.display = '';
	}
}

function GoTermo(termo, vai) {
	var t = document.getElementById('termo_'+termo);
	if (t == null) {
		document.var_list.action = "<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&goto="+termo;
		document.var_list.submit();
	} else {
		var o = t;
		var close = true;
		do {
			while (o.nodeName != "DIV") o = o.parentNode;
			if (o.style.display != '') close = false;
			if (o.getAttribute("id") != "top2") {
				o.style.display = '';
				o = o.parentNode;
			}
		} while (o.getAttribute("id") != "top2");
		if (close && !vai) {
			t.style.display = 'none';
		} else if (vai) {
			o = t;
			while (o.nodeName != 'A') o = o.previousSibling;
			scrollTo(0, o.offsetTop);
		}
	}
}

function LoadTermo(termo) {
	document.var_list.action = "<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&tema=1&termo="+termo;
	document.var_list.submit();
}

function SelAll(it) {
	var status = it.checked;
	var p = it.parentNode;
	while (p.nodeName != 'DIV') p = p.parentNode;
	for (i=0;i < p.childNodes.length;i++) {
		if (p.childNodes[i].nodeName == "TABLE") {
			for (j=0;j < p.childNodes[i].childNodes.length;j++) {
				if (p.childNodes[i].childNodes[j].nodeName == "TBODY") {
					for (k=0;k < p.childNodes[i].childNodes[j].childNodes.length;k++) {
						if (p.childNodes[i].childNodes[j].childNodes[k].nodeName == "TR") {
							for (l=0;l < p.childNodes[i].childNodes[j].childNodes[k].childNodes.length;l++) {
								if (p.childNodes[i].childNodes[j].childNodes[k].childNodes[l].nodeName == "TD") {
									for (m=0;m < p.childNodes[i].childNodes[j].childNodes[k].childNodes[l].childNodes.length;m++) {
										if (p.childNodes[i].childNodes[j].childNodes[k].childNodes[l].childNodes[m].nodeName == "INPUT") {
											p.childNodes[i].childNodes[j].childNodes[k].childNodes[l].childNodes[m].checked = status;
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function GoVar(act) {
	if (act == 'busca') {
		document.var_list.dest.value='1';
		document.var_list.action='<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_save';
		document.var_list.submit();
	}
	return false;
}

function LocalizaAct(e) {
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;

	if (keycode == 13) {
		GoVar('busca');
		return false;
	} else return true;
}
</script>
<div id="back">
<form name="var_list" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_save" method="post">
<input type="hidden" name="dest" value="2">
	<br>
	<div id="conteudo2"><img src="img/seta_cab.gif" align="absmiddle"><font class="v10">&nbsp;<b>SELEÇÃO DAS VARIÁVEIS :</b></font></div>
	<br>

	<table cellpadding="0" cellspacing="2" border="0" width="735" height="24" align="left">
		<tr>
			<td width="39">&nbsp;</td>
			<?php if (empty($_REQUEST['busca']) || !empty($_REQUEST['tema'])) { ?>
			<td valign="bottom"><font class="v10"><b>Organizadas por <font color="E13821">Tema</font></b>&nbsp;</font><img src="img/seta_down.gif" border="0"></td>
			<td valign="top" align="right"><!--a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&busca=a%"><img src="img/bt_assunto.gif" border="0"--></a></td>
			<?php } else { ?>
			<td valign="bottom"><font class="v10"><b>Organizadas por <font color="E13821">Assunto</font></b>&nbsp;</font><img src="img/seta_down.gif" border="0"></td>
			<td valign="top" align="right"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&tema=1"><img src="img/bt_tema.gif" border="0"></a></td>
			<?php } ?>
		</tr>
	</table>
<?php if (!empty($_REQUEST['busca']) && empty($_REQUEST['tema'])) { ?>
	<div id="conteudo2" style="background-color:#EFEFEF;">
	<table cellpadding="0" cellspacing="0" border="01" width="734">
		<tr><td><img src="img/tabin_top_left.gif"></td><td align="right"><img src="img/tabin_top_right.gif"></td></tr>
	</table>

	<div id="fundo_branco"><font class="v10"><b>Digite um assunto:</b>&nbsp;&nbsp;</font><input type="text" name="busca" onKeyPress="LocalizaAct(event);" value="<?php echo ((empty($_REQUEST['busca']) || strstr($_REQUEST['busca'], '%'))?'':$_REQUEST['busca']); ?>" class="campo_busca">&nbsp;&nbsp;<a href="javascript:GoVar('busca');"><img src="img/bt_localizar.gif" value="Localizar" align="absmiddle" border="0"></a></div>
	<div id="fundo_branco" style="background-image: url(img/pontos.gif); height:4px;"><img src="img/dot.gif" height="4" width="0"></div>
	<div id="fundo_branco" style="margin:12px 23px 12px 23px;">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td colspan="2"><font class="v10"><b>Índice de assuntos:</b></font></td>
			</tr>
			<tr>
				<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&busca=%"><img src="letras/a-z.gif" align="absmiddle" border="0"></a></td>
				<td align="right">
				<?php
function semacento ($a) {
	$a = ereg_replace("[áàâãª]","a",$a);
	$a = ereg_replace("[ÁÀÂÃ]","A",$a);
	$a = ereg_replace("[éèê]","e",$a);
	$a = ereg_replace("[ÉÈÊ]","E",$a);
	$a = ereg_replace("[íìî]","i",$a);
	$a = ereg_replace("[ÍÌÎ]","I",$a);
	$a = ereg_replace("[óòôõº]","o",$a);
	$a = ereg_replace("[ÓÒÔÕ]","O",$a);
	$a = ereg_replace("[úùû]","u",$a);
	$a = ereg_replace("[ÚÙÛ]","U",$a);
	$a = str_replace("ç","c",$a);
	$a = str_replace("Ç","C",$a);
	$a = ereg_replace(" ","",$a);
	return $a;
}

	$rs =& $GLOBALS['app']->db->Query("SELECT DISTINCT(LEFT(ter_nome,1)) AS letra FROM tb_{$_SESSION['base']}_rel_ter AS r LEFT JOIN linguagem.tb_termo USING (ter_cod) ORDER BY letra");
	$letras = array();
	while ($row = $rs->Row()) $letras[strtolower(semacento($row['letra']))] = 1;
	for ($letra = ord('a'); $letra <= ord('z'); $letra++) {
		if (isset($letras[chr($letra)])) {
	 ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&busca=<?php echo chr($letra);?>%"><img src="letras/<?php echo chr($letra); ?>.gif" border="0"></a><?php
		} else {
	 ?><img src="letras/<?php echo chr($letra); ?>2.gif" border="0"><?php
		}
	}
	$rs->Close();
?></td>
			</tr>
		</table>
	 </div>


	<table cellpadding="0" cellspacing="0" border="0" width="734">
		<tr><td><img src="img/tabin_bottom_left.gif"></td><td align="right"><img src="img/tabin_bottom_right.gif"></td></tr>
	</table>
	</div>
	<br>
<?php } ?>

	<div id="conteudo2" style="background-color:#FFFFFF;">
	<table cellpadding="0" cellspacing="0" border="0" width="734">
		<tr><td><img src="img/tabin_top_left.gif"></td><td align="right"><img src="img/tabin_top_right.gif"></td></tr>
	</table>
	<div id="fundo_branco">
	<div id="top2">
<?php
if (empty($_REQUEST['busca']) || !empty($_REQUEST['tema'])) {
	if (empty($_REQUEST['termo']) && empty($_REQUEST['goto'])) {
		$rs =& $GLOBALS['app']->db->Query("SELECT DISTINCT t.ter_cod, t.ter_nome FROM tb_{$_SESSION['base']}_rel2_ter AS x LEFT JOIN tb_termo2 AS t USING (ter_cod) LEFT JOIN tb_termo2_relacionado AS r ON r.ter_cod = t.ter_cod WHERE r.ter_tipo = 0 AND r.ter_rel = 0 AND x.ter_tipo = 0 ORDER BY t.ter_ordem, t.ter_nome");
		while ($row = $rs->Row()) { ?>
<a href="javascript:LoadTermo(<?php echo $row['ter_cod']; ?>);"><img src="img/bt_mais.gif" border="0" align="absmiddle"></a>&nbsp;
&nbsp;<a href="javascript:LoadTermo(<?php echo $row['ter_cod']; ?>);"><?php echo $row['ter_nome']; ?></a><br />
<?php
		}
		$rs->Close();
	} else {
		MontaVars(0);
	}
}
if (!empty($_REQUEST['busca'])) {
	$rs =& $GLOBALS['app']->db->Query("SELECT t.ter_cod, t.ter_nome FROM tb_{$_SESSION['base']}_rel_ter AS r LEFT JOIN linguagem.tb_termo AS t USING (ter_cod) WHERE t.ter_nome like '{$_REQUEST['busca']}' AND t.ter_nome not like '!%'");
	$dados = $rs->All();
	$rs->Close();
	$rs =& $GLOBALS['app']->db->Query("SELECT t.ter_cod, t.ter_sinonimo AS ter_nome FROM tb_{$_SESSION['base']}_rel_ter AS r LEFT JOIN linguagem.tb_termo_sinonimo AS t USING (ter_cod) WHERE t.ter_sinonimo like '{$_REQUEST['busca']}'");
	$dados = array_merge($dados, $rs->All());
	$rs->Close();
	$termos = array();
	if ((count($dados) > 0) && !strstr($_REQUEST['busca'], '%')) {
		$termo = $dados[0]['ter_nome'];
		foreach ($dados as $row) $termos[] = $row['ter_cod'];

		$rs =& $GLOBALS['app']->db->Query("SELECT t.ter_cod, t.ter_rel FROM tb_{$_SESSION['base']}_rel_ter AS r LEFT JOIN linguagem.tb_termo_relacionado AS t USING (ter_cod) WHERE t.ter_tipo = 0");
		while ($row = $rs->Row()) {
			$filhos[$row['ter_rel']][] = $row['ter_cod'];
		}
		$rs->Close();

		$todos = array();
		RecAdd($todos, $termos, $filhos);
		$todos = array_unique($todos);

		$id = 0;
		$cor = 0;
		$rs =& $GLOBALS['app']->db->Query("SELECT DISTINCT v.var_cod, t.ter_nome, var_nome, var_formato, var_periodo, var_ops FROM tb_{$_SESSION['base']}_rel_ter_var AS r INNER JOIN tb_{$_SESSION['base']}_variavel AS v using(var_cod) LEFT JOIN linguagem.tb_termo AS t ON t.ter_cod = r.ter_cod WHERE v.var_cod = r.var_cod AND r.ter_cod IN (".implode(', ', $todos).") ORDER BY  t.ter_ordem, t.ter_nome, v.var_ordem, v.var_nome");
		if ($rs->RowCount() > 0) { ?>
<br>Vari&aacute;veis encontradas em <?php echo $termo; ?>:<br>
<div>
<?php
			$ternome = "";
			while ($row = $rs->Row()) {
				if ($row['ter_nome'] != $ternome) {
					$ternome = $row['ter_nome'];
					echo "<br><b>$ternome</b><br>";
				}
				if (empty($row['var_periodo'])) { ?>
		<table border="0" cellpadding="0" cellspacing="0" width="625">
			<tr <?php if ($cor++ % 2) echo 'bgcolor="F3F8FC"'; ?>>
				<td width="12"><img src="img/bt_seta2.gif" border="0"></td>
				<td width="20"><input type="checkbox" name="vars[]" value="<?php echo $row['var_cod']; ?>"<?php if (in_array($row['var_cod'], $_SESSION['vars'])) echo ' checked'; ?>></td>
				<td width="15"><img src="img/bt_<?php echo ($row['var_formato'] == 0)?'quant':(($row['var_ops'] & 256)?'mista':'cat'); ?>.gif" title="<?php echo ($row['var_formato'] == 0)?'Vari&aacute;vel Quantitativa - Pode ser usada como conte&uacute;do da tabela, usada nos c&aacute;lculos.':(($row['var_ops'] & 256)?'Vari&aacute;vel Mista - Pode ser usada como Variável Categórica ou Quantitativa.':'Vari&aacute;vel Categ&oacute;rica - Pode ser usada nas linhas ou colunas da tabela.'); ?>" align="absmiddle"></td>
				<td class="v11"><?php echo $row['var_nome']; ?> [<a href="#" onClick="return AbreNota('<?php echo $row['var_cod']; ?>');" class="a1">defini&ccedil;&atilde;o</a>]</td>
			</tr>
		</table>
<?php } else { ?>
		<table border="0" cellpadding="0" cellspacing="0" width="625">
			<tr <?php if ($cor++ % 2) echo 'bgcolor="F3F8FC"'; ?>>
				<td width="28"><a href="javascript:GoAnos('<?php echo $row['var_cod'].'_'.$id; ?>', false);"><img src="img/bt_mais2.gif" border="0" align="absmiddle"></a>&nbsp;</td>
				<td width="15"><img src="img/bt_<?php echo ($row['var_formato'] == 0)?'quant':(($row['var_ops'] & 256)?'mista':'cat'); ?>.gif" title="<?php echo ($row['var_formato'] == 0)?'Vari&aacute;vel Quantitativa - Pode ser usada como conte&uacute;do da tabela, usada nos c&aacute;lculos.':(($row['var_ops'] & 256)?'Vari&aacute;vel Mista - Pode ser usada como Variável Categórica ou Quantitativa.':'Vari&aacute;vel Categ&oacute;rica - Pode ser usada nas linhas ou colunas da tabela.'); ?>" align="absmiddle"></td>
				<td class="v11"><?php echo $row['var_nome']; ?> [<a href="#" onClick="return AbreNota('<?php echo $row['var_cod']; ?>');" class="a1">defini&ccedil;&atilde;o</a>]</td>
			</tr>
			<tr id="var_<?php echo $row['var_cod'].'_'.($id++); ?>" style="display:none">
				<td colspan="3"><div><table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php
$anos = ExplodeAnos($row['var_periodo']);
if (count($anos) > 1) { ?>
			<tr>
				<td width="12"><img src="img/bt_seta2.gif" border="0"></td>
				<td width="20"><input type="checkbox" onClick="SelAll(this);"></td>
				<td class="v11"><i>Selecionar todos os anos desta vari&aacute;vel</i></td>
			</tr>
<?php }  ?>
				<?php foreach($anos as $ano) { ?>
				<tr>
					<td width="12"><img src="img/bt_seta2.gif" border="0"></td>
					<td width="20"><input type="checkbox" name="vars[]" value="<?php echo "{$row['var_cod']}_{$ano}"; ?>"<?php if (in_array($row['var_cod'].'_'.$ano, $_SESSION['vars'])) echo ' checked'; ?>></td>
					<td class="v11"><?php echo $ano; ?></td>
				</tr>
				<?php } ?>
				</table></div></td>
			</tr>
		</table>
<?php
				}
			} ?>
</div>
<?php
		}
		$rs->Close();

		$rs =& $GLOBALS['app']->db->Query("SELECT DISTINCT t.ter_nome FROM tb_{$_SESSION['base']}_rel_ter AS l, linguagem.tb_termo_relacionado AS r LEFT JOIN linguagem.tb_termo AS t ON t.ter_cod = r.ter_rel WHERE l.ter_cod = r.ter_rel AND r.ter_tipo = 1 AND r.ter_cod IN (".implode(', ', $termos).") ORDER BY t.ter_nome");
		while ($row = $rs->Row()) { ?>
<img src="img/asterisco.gif" border="0"><i>Ver tamb&eacute;m</i>:&nbsp;<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&busca=<?php echo urlencode($row['ter_nome']); ?>"><?php echo $row['ter_nome']; ?></a><br>
<?php 	}
		$rs->Close();
	}
	if (!strstr($_REQUEST['busca'], '%')) {
		$outros = array();
		$rs =& $GLOBALS['app']->db->Query("SELECT t.ter_cod FROM tb_{$_SESSION['base']}_rel_ter AS l LEFT JOIN linguagem.tb_termo AS t USING (ter_cod) WHERE t.ter_nome like '%{$_REQUEST['busca']}%'");
		while ($row = $rs->Row()) $outros[] = $row['ter_cod'];
		$rs->Close();
		$rs =& $GLOBALS['app']->db->Query("SELECT t.ter_cod FROM tb_{$_SESSION['base']}_rel_ter AS l, linguagem.tb_termo_sinonimo AS t WHERE l.ter_cod = t.ter_cod AND t.ter_sinonimo like '%{$_REQUEST['busca']}%'");
		while ($row = $rs->Row()) $outros[] = $row['ter_cod'];
		$rs->Close();

		foreach($termos as $termo) unset($outros[array_search($termo, $outros)]);

		if (!empty($outros) && empty($termos)) {
			$GLOBALS['app']->db->Insert('tb_log_busca', array('log_busca' => "'".$GLOBALS['app']->db->AddSlashes($_REQUEST['busca'])."'"));
?>
Assunto não encontrado.<br>Assuntos similares:<br><div>
<?php
			$outros = array_unique($outros);
			$rs =& $GLOBALS['app']->db->Query("SELECT ter_nome FROM linguagem.tb_termo WHERE ter_cod IN (".implode(', ',$outros).") ORDER BY ter_nome");
			while ($row = $rs->Row()) { ?>
<img src="img/asterisco.gif" border="0"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&busca=<?php echo urlencode($row['ter_nome']); ?>"><?php echo $row['ter_nome']; ?></a><br>
<?php	 	}
			$rs->Close();
?>
</div>
<?php
		} elseif (empty($outros) && empty($termos)) {
			$GLOBALS['app']->db->Insert('tb_log_busca', array('log_busca' => "'".$GLOBALS['app']->db->AddSlashes($_REQUEST['busca'])."'"));
?>
Nenhum assunto encontrado.
<?php
		}
	} else {
		if (empty($dados)) {
?>
Nenhum assunto come&ccedil;ando com <?php echo substr($_REQUEST['busca'], 0, 1); ?> encontrado.<br>
<?php
		} else {
			if (strlen($_REQUEST['busca']) == 1) {
?>
Todos os Assuntos:<br>
<?php
			} else {
?>
Assuntos come&ccedil;ando com a letra <?php echo strtoupper(substr($_REQUEST['busca'], 0, 1)); ?>:<br>
<?php
			}
			foreach ($dados as $row) $termos[semacento($row['ter_nome'])] = $row['ter_nome'];
			ksort($termos);
			$termos = array_unique($termos);
			foreach ($termos as $termo) { ?>
<img src="img/asterisco.gif" border="0"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta&action=var_list&busca=<?php echo urlencode($termo); ?>"><?php echo $termo; ?></a><br>
<?php		}
		}
	}
}
?>
	</div>
	<div style="background-image: url(img/pontos.gif); height:4px; margin-top:12px; margin-bottom:6px;"><img src="img/dot.gif" height="4" width="0"></div>

<div style="margin:6px 0 0 0;padding-left:10px"><font face="arial" size="1"><b>Legenda:</b></font></div>
<div style="margin:6px 0 3px 0;padding-left:10px"><img src="img/bt_cat.gif" align="absmiddle"><font face="arial" size="1"><b> Variável Categórica - Pode ser usada nas linhas ou colunas da tabela</b> [<a href="#" onClick="return AbreNota('interno_var_categ');" class="a1">defini&ccedil;&atilde;o</a>]</font></div>
<div style="margin:6px 0 6px 0;padding-left:10px"><img src="img/bt_quant.gif" align="absmiddle"><font face="arial" size="1"><b> Variável Quantitativa - Pode ser usada como conteúdo da tabela ou usada nos cálculos</b> [<a href="#" onClick="return AbreNota('interno_var_quant');" class="a1">defini&ccedil;&atilde;o</a>]</font></div>
<div style="margin:6px 0 6px 0;padding-left:10px"><img src="img/bt_mista.gif" align="absmiddle"><font face="arial" size="1"><b> Variável Mista - Pode ser usada como Variável Categórica ou Quantitativa</b> [<a href="#" onClick="return AbreNota('interno_var_mista');" class="a1">defini&ccedil;&atilde;o</a>]</font></div>
	</div>
	<table cellpadding="0" cellspacing="0" border="0" width="734">
		<tr><td><img src="img/tabin_bottom_left.gif"></td><td align="right"><img src="img/tabin_bottom_right.gif"></td></tr>
	</table>
	</div>

	<br>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#666632" height="5">
		<tr valign="bottom"><td><img src="img/tabex_bottom_left.gif"></td><td align="right"><img src="img/tabex_bottom_right.gif"></td></tr>
	</table>

</div>

<div id="botoes" align="center" style="position:absolute; left:0px; top:5px; width:50px; height:50px; z-index:100">
<input type="image" src="img/bt_confirmar.gif" value="OK"><br>
</div>

<script language="javascript" src="float.js"></script>
<script language="javascript">flevInitPersistentLayer('botoes',0,'','','','','','105',20,50);</script>

</form>
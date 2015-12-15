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

function TabelaHTML() {
	document.gera.action = "<?php echo $_SERVER['PHP_SELF']; ?>?page=tabela";
	document.gera.submit();
}

function TabelaCSV() {
	document.gera.action = "csv.php";
	document.gera.submit();
}
</script>
<div id="back">
<br>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=consulta" method="post" name="gera">
	<div id="conteudo2"><img src="img/seta_cab.gif" align="absmiddle"><font class="v10">&nbsp;<b>FORMATAÇÃO DA TABELA :</b></font></div>
	<br>
	<div id="conteudo2" style="background-color:#FFFFFF;">
	<table cellpadding="0" cellspacing="0" border="0" width="734">
		<tr>
			<td width="154" valign="top" align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="154">
					<tr>
						<td><img src="img/tabin_top_left.gif"></td>
						<td align="right"><img src="img/tabin_top_right.gif"></td>
					</tr>
				</table><br><img src="img/tabela.gif"></td>
				<td width="580" bgcolor="DCEBF7" valign="bottom">
					<table cellpadding="0" cellspacing="0" border="0" width="558">
						<tr>
							<td width="16" valign="bottom"><img src="img/tabin_top_right.gif"></td>
							<td><font class="v10"><b>INSTRUÇÕES<br><br>1</b> - </font><font class="v11">Selecione uma variável<font class="v10" color="118DB8"><b> CATEGÓRICA</b></font> para compor a <font class="v10" color="118DB8"><b>LINHA</b></font> e outra para a <font class="v10" color="118DB8"><b>COLUNA</b></font>;</font><br>
							<font class="v10"><b>2</b> - </font><font class="v11">Clique na opção [ <font class="v10" color="118DB8"><b>FILTRO</b></font> ] para refinar a pesquisa e obter resultados mais específicos.<br>&nbsp;<br>&nbsp;<br></font></td>
						</tr>
					</table>
				</td>
		</tr>
			<tr>
				<td align="center"></td>
				<td align="right" valign="top"><img src="img/tabin_top_right.gif"></td>
			</tr>

			<tr>
				<td colspan="2" align="center"><br>
					<table cellpadding="2" cellspacing="1" border="0" width="700">
						<tr bgcolor="CCE5F9">
							<td width="45" align="center"><font class="v10"><b>ORDEM</b></font></td>
							<td width="90" align="center"><font class="v10"><b>MOSTRAR</b></font></td>
							<td width="45" align="center"><font class="v10"><b>FILTRO</b></font></td>
							<td width="516"><font class="v10"><b>VARIÁVEIS</b></font></td>
						</tr>
					</table><img src="img/dot.gif" height="7" width="0"><br>
<?php $new_ord = 1; ?>
					<table cellpadding="2" cellspacing="1" border="0" width="698" bgcolor="CCE5F9">
						<tr bgcolor="EAF3FA">
							<td width="44" align="center"><select name="posicao[frequencia][ordem]" style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px;">
							<?php for ($i=1;$i<=(count($_SESSION['vars'])+1);$i++) { ?>
								<option value="<?php echo $i; ?>" <?php if ((isset($_SESSION['posicao']['frequencia']['ordem']) && ($i == $_SESSION['posicao']['frequencia']['ordem'])) || (empty($_SESSION['posicao']['frequencia']['ordem']) && ($new_ord == $i))) echo "selected"; ?>><?php echo $i; ?></option>
							<?php }
								$new_ord++;
							?>
							</select></td>
							<td width="89"><select name="posicao[frequencia][pos]" style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px;">
								<option value="0" <?php if (isset($_SESSION['posicao']['frequencia']['pos']) && ("0" == $_SESSION['posicao']['frequencia']['pos'])) echo "selected"; ?>>Ocultar</option>
								<option value="F" <?php if (!isset($_SESSION['posicao']['frequencia']['pos']) || isset($_SESSION['posicao']['frequencia']['pos']) && ("F" == $_SESSION['posicao']['frequencia']['pos'])) echo "selected"; ?>>Exibir</option>
								</select></td>
							<td width="44" align="center">&nbsp;</td>
							<td width="504">
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr valign="top">
										<td width="17"><img src="img/bt_quant.gif"></td>
										<td><font class="v10"> <b>Freq&uuml;&ecirc;ncia</b></font></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
<br>
<?php
	$vars = array();
	foreach ($_SESSION['vars'] as $var) {
		$pc = explode("_", $var);
		$vars[] = $pc[0];
	}
	$rs = $GLOBALS['app']->db->Query("SELECT var_cod, var_nome, var_formato, var_ops FROM tb_{$_SESSION['base']}_variavel WHERE var_cod IN ('".implode("', '",$vars)."') ORDER BY var_nome");
	while ($row = $rs->Row()) $vars[$row['var_cod']] = $row;
	$rs->Close();

	foreach ($_SESSION['vars'] as $var) {
		$pc = explode("_", $var);
		$row = $vars[$pc[0]];
		if ($row['var_formato'] > 0) {
?>
					<table cellpadding="2" cellspacing="1" border="0" width="698" bgcolor="CCE5F9">
						<tr bgcolor="EAF3FA">
							<td width="44" align="center"><select name="posicao[<?php echo $var; ?>][ordem]" style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px;">
							<?php for ($i=1;$i<=(count($_SESSION['vars'])+1);$i++) { ?>
								<option value="<?php echo $i; ?>" <?php if ((isset($_SESSION['posicao'][$var]['ordem']) && ($i == $_SESSION['posicao'][$var]['ordem'])) || (empty($_SESSION['posicao'][$var]['ordem']) && ($new_ord == $i))) echo "selected"; ?>><?php echo $i; ?></option>
							<?php }
								$new_ord++;
							?>
							</select></td>
							<td width="89"><select name="posicao[<?php echo $var; ?>][pos]" style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px;">
								<option value="0" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("0" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Ocultar</option>
								<option value="L<?php if ($row['var_ops'] & 512) echo 'X'; ?>" <?php if (isset($_SESSION['posicao'][$var]['pos']) && (strpos($_SESSION['posicao'][$var]['pos'], "L") !== FALSE)) echo "selected"; ?>>Linha</option>
								<option value="C<?php if ($row['var_ops'] & 512) echo 'X'; ?>" <?php if (isset($_SESSION['posicao'][$var]['pos']) && (strpos($_SESSION['posicao'][$var]['pos'], "C") !== FALSE)) echo "selected"; ?>>Coluna</option>
<?php if ($row['var_ops'] & 256) { ?>                                                                
<?php if ($row['var_ops'] & 1) { ?>
								<option value="S" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("S" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Soma</option>
<?php } ?>
<?php if ($row['var_ops'] & 2) { ?>
                                                                <option value="S" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("S" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Soma</option>                                                                
								<option value="M" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("M" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Média</option>
<?php } ?>
<?php } ?>
								</select></td>
							<td width="44" align="center"><input type="checkbox" name="filtro[<?php echo $var; ?>][tipo]" value="<?php echo ($row['var_formato'] == 0)?"L":"C"; ?>" onClick="ChkFiltro(this, 'filtro_<?php echo $var; ?>');"<?php if (!empty($_SESSION['filtro'][$var]['tipo'])) echo " checked"; ?>></td>
							<td width="504">
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr valign="top">
										<td width="17"><img src="img/bt_<?php if ($row['var_ops'] & 256) { ?>mista<?php } else { ?>cat<?php } ?>.gif"></td>
										<td><font class="v10"> <b><?php echo $row['var_nome']; ?><?php if (!empty($pc[1])) echo " - ".$pc[1]; ?></b> [<a href="#" onClick="return AbreNota('<?php echo $row['var_cod']; ?>');" class="a1">defini&ccedil;&atilde;o</a>]</font></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="F6FAFD" valign="top" <?php if (empty($_SESSION['filtro'][$var]['tipo'])) echo " style='display:none'"; ?> id="filtro_<?php echo $var; ?>">
							<td colspan="3">&nbsp;</td>
							<td>
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr valign="top">
										<td width="17"><img src="img/seta_laranja.gif"></td>
										<td><font class="v10"> Filtrar a variável acima, selecionando apenas as que contenham as seguintes respostas:</font></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<select name="filtro[<?php echo $var; ?>][lista][]" size="4" multiple style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px;">
											<?php
											$rs2 = $GLOBALS['app']->db->Query("SELECT DISTINCT {$row['var_cod']} AS valores FROM tb_{$_SESSION['base']}_dados");
											$valores = array();
											$nulo = false;
											while ($val = $rs2->Row()) if (!is_null($val['valores'])) {
												$valores[] = $val['valores'];
											} else {
												$nulo = true;
											}
											$rs2->Close();
											$rs2 = $GLOBALS['app']->db->Query("SELECT fmt_val, fmt_desc FROM tb_formato_valor WHERE fmt_cod = {$row['var_formato']} AND (fmt_val IN (".implode(", ", $valores).")".($nulo?' OR ISNULL(fmt_val)':'').") ORDER BY fmt_ordem");
											while ($fmt = $rs2->Row()) {
												if (is_null($fmt['fmt_val'])) $fmt['fmt_val'] = 'null';
											?>
											 	<option value="<?php echo $fmt['fmt_val']; ?>" <?php if ((isset($_SESSION['filtro'][$var]['lista']) && in_array($fmt['fmt_val'], $_SESSION['filtro'][$var]['lista'])) || (empty($_SESSION['filtro'][$var]['lista']) && ($fmt['fmt_val'] >= 0))) echo " selected"; ?>><?php echo $fmt['fmt_desc']; ?></option>
											<?php } ?>
											</select></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
<?php } else { ?>
					<table cellpadding="2" cellspacing="1" border="0" width="698" bgcolor="CCE5F9">
						<tr bgcolor="EAF3FA">
							<td width="44" align="center"><select name="posicao[<?php echo $var; ?>][ordem]" style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px;">
							<?php for ($i=1;$i<=(count($_SESSION['vars'])+1);$i++) { ?>
								<option value="<?php echo $i; ?>" <?php if ((isset($_SESSION['posicao'][$var]['ordem']) && ($i == $_SESSION['posicao'][$var]['ordem'])) || (empty($_SESSION['posicao'][$var]['ordem']) && ($new_ord == $i))) echo "selected"; ?>><?php echo $i; ?></option>
							<?php }
								$new_ord++;
							?>
							</select></td>
							<td width="89"><select name="posicao[<?php echo $var; ?>][pos]" style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px;">
								<option value="0" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("0" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Ocultar</option>
<?php if ($row['var_ops'] & 1) { ?>
								<option value="S" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("S" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Soma</option>
<?php } ?>
<?php if ($row['var_ops'] & 2) { ?>
								<option value="M" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("M" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Média</option>
								<option value="S" <?php if (isset($_SESSION['posicao'][$var]['pos']) && ("S" == $_SESSION['posicao'][$var]['pos'])) echo "selected"; ?>>Soma</option>                                                                
<?php } ?>
								</select></td>
							<td width="44" align="center"><input type="checkbox" name="filtro[<?php echo $var; ?>][tipo]" value="<?php echo ($row['var_formato'] == 0)?"L":"C"; ?>" onClick="ChkFiltro(this, 'filtro_<?php echo $var; ?>');"<?php if (!empty($_SESSION['filtro'][$var]['tipo'])) echo " checked"; ?>></td>
							<td width="504">
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr valign="top">
										<td width="17"><img src="img/bt_quant.gif"></td>
										<td><font class="v10"> <b><?php echo $row['var_nome']; ?><?php if (!empty($pc[1])) echo " - ".$pc[1]; ?></b> [<a href="#" onClick="return AbreNota('<?php echo $row['var_cod']; ?>');" class="a1">defini&ccedil;&atilde;o</a>]</font></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="F6FAFD" valign="top" <?php if (empty($_SESSION['filtro'][$var]['tipo'])) echo " style='display:none'"; ?> id="filtro_<?php echo $var; ?>">
							<td colspan="3">&nbsp;</td>
							<td valign="top">
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr valign="top">
										<td width="17"><img src="img/seta_verde.gif"></td>
										<td><font class="v10"> Filtrar a variável acima, utilizando apenas as que contenham os seguintes critérios:</font></td>
									</tr>
									<tr>
										<td></td>
										<td><select name="filtro[<?php echo $var; ?>][op]" style="font-family:verdana, Arial, Helvetica, sans-serif; font-size:10px; background-color:ffffff;">
											<option value="<=" <?php if (isset($_SESSION['filtro'][$var]['op']) && !strcmp($_SESSION['filtro'][$var]['op'], '<=')) echo " selected"; ?>>Menor ou igual a</option>
											<option value=">=" <?php if (isset($_SESSION['filtro'][$var]['op']) && !strcmp($_SESSION['filtro'][$var]['op'], '>=')) echo " selected"; ?>>Maior ou igual a</option>
											<option value="=" <?php if (isset($_SESSION['filtro'][$var]['op']) && !strcmp($_SESSION['filtro'][$var]['op'], '=')) echo " selected"; ?>>Igual a</option>
											<option value="<>" <?php if (isset($_SESSION['filtro'][$var]['op']) && !strcmp($_SESSION['filtro'][$var]['op'], '<>')) echo " selected"; ?>>Diferente de</option>
											<option value=">" <?php if (isset($_SESSION['filtro'][$var]['op']) && !strcmp($_SESSION['filtro'][$var]['op'], '>')) echo " selected"; ?>>Maior que</option>
											<option value="<" <?php if (isset($_SESSION['filtro'][$var]['op']) && !strcmp($_SESSION['filtro'][$var]['op'], '<')) echo " selected"; ?>>Menor que</option>
											</select> <input type="text" name="filtro[<?php echo $var; ?>][val]" value="<?php if (isset($_SESSION['filtro'][$var]['val'])) echo $_SESSION['filtro'][$var]['val']; ?>" class="campo_busca" style="width:50px; background-color:ffffff;"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
<?php } ?>
<br>
<?php } ?>
				</td>
			</tr>
	  </table>

	<br>
<table align="left" cellpadding="0" cellspacing="0" border="0" width="329">
	<tr>
		<td width="7">&nbsp;</td>
		<td align="center" width="146"><input type="image" src="img/bt_ver.gif" value="Visualizar o resultado na tela" onClick="TabelaHTML();"></td>
		<td width="30">&nbsp;</td>
		<td align="center" width="146"><input type="image" src="img/bt_down.gif" value="Baixar o resultado em um arquivo texto CSV" onClick="TabelaCSV();"></td>
	</tr>
</table>
<br/>

	<br>
			<div style="width:695px; background-image:  url(img/pontos.gif); height:4px; margin-left:20px; margin-top:12px; margin-bottom:6px;"><img src="img/dot.gif" height="4" width="0"></div>
			<div style="width:690px; margin:6px 0 0 20px;"><font face="arial" size="1"><b>Legenda:</b></font></div>
			<div style="width:690px; margin:6px 0 3px 20px;"><img src="img/bt_cat.gif" align="absmiddle"><font face="arial" size="1"><b> Variável Categórica - Pode ser usada nas linhas ou colunas da tabela</b> [<a href="#" onClick="return AbreNota('interno_var_categ');" class="a1">defini&ccedil;&atilde;o</a>]</font></div>
			<div style="width:690px; margin:0 0 0 20px;"><img src="img/bt_quant.gif" align="absmiddle"><font face="arial" size="1"><b> Variável Quantitativa - Pode ser usada como conteúdo da tabela ou usada nos cálculos</b> [<a href="#" onClick="return AbreNota('interno_var_quant');" class="a1">defini&ccedil;&atilde;o</a>]</font></div>
			<div style="width:690px; margin:3px 0 0 20px;"><img src="img/bt_mista.gif" align="absmiddle"><font face="arial" size="1"><b> Variável Mista - Pode ser usada como Variável Categórica ou Quantitativa</b> [<a href="#" onClick="return AbreNota('interno_var_mista');" class="a1">defini&ccedil;&atilde;o</a>]</font></div>
	        <br>
	<table cellpadding="0" cellspacing="0" border="0" width="734">
		<tr><td><img src="img/tabin_bottom_left.gif"></td><td align="right"><img src="img/tabin_bottom_right.gif"></td></tr>
	</table>
	</div>

	<br>

	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="4AB9E6" height="5">
		<tr valign="bottom"><td><img src="img/tabex_bottom_left.gif"></td><td align="right"><img src="img/tabex_bottom_right.gif"></td></tr>
	</table>
</div>
</form>
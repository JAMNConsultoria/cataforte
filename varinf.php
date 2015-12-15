<script language="javascript">
function Go(val) {
	s = document.getElementById(val).style;
	if (s.display == '') {
		s.display = 'none';
	} else {
		s.display = '';
	}
}
</script>
<?php
$txt_cv = <<< END
� uma das medidas de precis�o de um indicador, sendo definido como a raz�o entre o desvio padr�o do estimador e o valor do indicador da popula��o. Em uma pesquisa amostral, pode ser interpretado da seguinte forma: se � obtido um dado com coeficiente de varia��o de 30%, o valor apresentado pode variar at� 58,9% para mais ou para menos, em 95% das amostras que forem utilizadas para investigar o fen�meno.
Neste sistema, optou-se por uma representa��o de cores de fundo das c�lulas da tabela: as verdes cont�m dados com coeficientes de varia��o considerados aceit�veis (menores que 30%) e as vermelhas com aqueles n�o aceit�veis (maiores ou iguais a 30%).
Dentro desses limites a cor varia gradativamente entre o verde para 0% e o amarelo para 30%, e tons gradativamente mais escuros de vermelho para valores acima de 30%.
Al�m da representa��o coropl�tica, este sistema fornece os valores calculados de coeficiente de varia��o, clicando-se no link "Exibir os coeficiente de varia��o relativos a esta tabela".
END;

$txt_var_categ = <<< END
Vari�vel que representa a classifica��o dos indiv�duos pesquisados, de acordo com determinadas qualidades ou atributos, n�o podendo ser utilizada para c�lculos.
END;

$txt_var_quant = <<< END
Vari�vel que resulta da contagem ou mensura��o, podendo ser usada para c�lculos.
END;

$txt_var_mista = <<< END
Vari�vel que representa uma qualidade ou atributo do indiv�duo pesquisado, expressa atrav�s de n�meros, podendo ser usada para c�lculos.
END;

$txt_emp = <<< END
A empresa � a organiza��o com personalidade jur�dica de direito privado, de natureza mercantil, voltada para a explora��o de atividade econ�mica e que responde pelas atividades produtoras de bens e/ou servi�os de uma ou mais unidades locais.  Os dados referem-se ao conjunto de suas atividades, independentemente da regi�o onde atuam. Uma empresa sediada no Estado de S�o Paulo, por exemplo, pode ter unidades no pr�prio Estado e tamb�m em outras Unidades da Federa��o. A empresa dever� ser escolhida como refer�ncia quando o pesquisador estiver interessado em assuntos como reestrutura��o patrimonial, informa��es econ�mico-financeiras, inova��o tecnol�gica, uso de tecnologias de informa��o e comunica��o.
END;

$txt_ul = <<< END
As ULs correspondem �s diversas unidades, no caso de empresa multilocal, ou � �nica quando for unilocal, onde s�o desenvolvidas as opera��es. As ULs s�o classificadas de acordo com a atividade nelas realizada, que pode ser, ou n�o, a mesma da empresa. A unidade local dever� ser escolhida como refer�ncia quando o pesquisador estiver interessado em assuntos como regionaliza��o da atividade econ�mica, requisitos locacionais, requisitos de contrata��o e instrumentos de sele��o de pessoal.
END;

$internas = array(
	'interno_CV' => array(
		'var_nome' => 'Coeficiente de Varia��o',
		'var_formato' => 0,
		'var_conceito' => $txt_cv
	),
	'interno_var_categ' => array(
		'var_nome' => 'Vari�vel Categ�rica',
		'var_formato' => 0,
		'var_conceito' => $txt_var_categ
	),
	'interno_var_quant' => array(
		'var_nome' => 'Vari�vel Quantitativa',
		'var_formato' => 0,
		'var_conceito' => $txt_var_quant
	),
	'interno_var_mista' => array(
		'var_nome' => 'Vari�vel Mista',
		'var_formato' => 0,
		'var_conceito' => $txt_var_mista
	),
	'interno_emp' => array(
		'var_nome' => 'Empresa',
		'var_formato' => 0,
		'var_conceito' => $txt_emp
	),
	'interno_ul' => array(
		'var_nome' => 'Unidade Local - UL',
		'var_formato' => 0,
		'var_conceito' => $txt_ul
	)
);

if (isset($_REQUEST['var'])) {
	$vars = array($_REQUEST['var']);
} elseif (isset($_SESSION['posicao'])) {
	$vars = array();
	foreach ($_SESSION['posicao'] as $var => $row) if ($row['pos'] != 'O')
		$vars[] = $var;
} else {
	$vars = array();
}

$init = 0;
foreach ($vars as $var) {
	if (strstr($var, 'interno_')) {
		$row = $internas[$var];
	} else {
		$rs = $GLOBALS['app']->db->Query("SELECT * FROM tb_{$_SESSION['base']}_variavel WHERE var_cod = '$var'");
		$row = $rs->Row();
		$rs->Close();
	}
	if (!empty($row)) { ?>
<table border="0" cellpadding="5" cellspacing="0" width="560" bgcolor="#E3EFFC" class="tabela" align="center">
	<tr>
		<td colspan="2" height="30">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><font size=2 face="Arial" color="#800000"><b><?php echo $row['var_nome']; ?></b></font></td>
<?php if ($init++) { ?>
					<td class="v11">&nbsp;</td>
<?php } else { ?>
					<td align="right" valign="top" class="v11">[<a href="javascript:self.print();" class="a1">imprimir</a>]&nbsp;[<a href="javascript:window.close();" class="a1">fechar</a>]</td>
<?php } ?>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><font face=verdana size=1><?php echo nl2br($row['var_conceito']); ?></font><br></td>
	</tr>
<?php if ($row['var_formato'] != 0) {
	$rs2 = $GLOBALS['app']->db->Query("SELECT DISTINCT {$row['var_cod']} AS valores FROM tb_{$_SESSION['base']}_dados");
	$valores = array();
	while ($val = $rs2->Row()) if (!is_null($val['valores'])) $valores[] = $val['valores'];
	$rs2->Close();
	if (count($valores) <= 10) { ?>
	<tr>
		<td colspan="2" valign="top"><b>Categorias encontradas na base :</b></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td>
<?php } else { ?>
	<tr>
		<td colspan="2" valign="top"><b><a href="#" onClick="Go('val_<?php echo
$row['var_cod']; ?>');" style="color:#0000FF;">Categorias encontradas na base  (<?php echo count($valores); ?>)</a></b></td>
	</tr>
	<tr id="val_<?php echo $row['var_cod']; ?>" style="display:none">
	<td>&nbsp;</td>
	<td><ul>
<?php }
	$rsf = $GLOBALS['app']->db->Query("SELECT * FROM tb_formato_valor WHERE fmt_cod = {$row['var_formato']} AND fmt_val IN (".implode(", ", $valores).") ORDER BY fmt_ordem");
	while ($fmt = $rsf->Row()) {
?>
<li><font face=verdana size=1><?php echo $fmt['fmt_desc']; ?></font></li>
<?php
	}
	$rsf->Close();
?>
</ul></td></tr>
<?php } ?>
</table><br />
<?php } } ?>
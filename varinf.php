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
É uma das medidas de precisão de um indicador, sendo definido como a razão entre o desvio padrão do estimador e o valor do indicador da população. Em uma pesquisa amostral, pode ser interpretado da seguinte forma: se é obtido um dado com coeficiente de variação de 30%, o valor apresentado pode variar até 58,9% para mais ou para menos, em 95% das amostras que forem utilizadas para investigar o fenômeno.
Neste sistema, optou-se por uma representação de cores de fundo das células da tabela: as verdes contêm dados com coeficientes de variação considerados aceitáveis (menores que 30%) e as vermelhas com aqueles não aceitáveis (maiores ou iguais a 30%).
Dentro desses limites a cor varia gradativamente entre o verde para 0% e o amarelo para 30%, e tons gradativamente mais escuros de vermelho para valores acima de 30%.
Além da representação coroplética, este sistema fornece os valores calculados de coeficiente de variação, clicando-se no link "Exibir os coeficiente de variação relativos a esta tabela".
END;

$txt_var_categ = <<< END
Variável que representa a classificação dos indivíduos pesquisados, de acordo com determinadas qualidades ou atributos, não podendo ser utilizada para cálculos.
END;

$txt_var_quant = <<< END
Variável que resulta da contagem ou mensuração, podendo ser usada para cálculos.
END;

$txt_var_mista = <<< END
Variável que representa uma qualidade ou atributo do indivíduo pesquisado, expressa através de números, podendo ser usada para cálculos.
END;

$txt_emp = <<< END
A empresa é a organização com personalidade jurídica de direito privado, de natureza mercantil, voltada para a exploração de atividade econômica e que responde pelas atividades produtoras de bens e/ou serviços de uma ou mais unidades locais.  Os dados referem-se ao conjunto de suas atividades, independentemente da região onde atuam. Uma empresa sediada no Estado de São Paulo, por exemplo, pode ter unidades no próprio Estado e também em outras Unidades da Federação. A empresa deverá ser escolhida como referência quando o pesquisador estiver interessado em assuntos como reestruturação patrimonial, informações econômico-financeiras, inovação tecnológica, uso de tecnologias de informação e comunicação.
END;

$txt_ul = <<< END
As ULs correspondem às diversas unidades, no caso de empresa multilocal, ou à única quando for unilocal, onde são desenvolvidas as operações. As ULs são classificadas de acordo com a atividade nelas realizada, que pode ser, ou não, a mesma da empresa. A unidade local deverá ser escolhida como referência quando o pesquisador estiver interessado em assuntos como regionalização da atividade econômica, requisitos locacionais, requisitos de contratação e instrumentos de seleção de pessoal.
END;

$internas = array(
	'interno_CV' => array(
		'var_nome' => 'Coeficiente de Variação',
		'var_formato' => 0,
		'var_conceito' => $txt_cv
	),
	'interno_var_categ' => array(
		'var_nome' => 'Variável Categórica',
		'var_formato' => 0,
		'var_conceito' => $txt_var_categ
	),
	'interno_var_quant' => array(
		'var_nome' => 'Variável Quantitativa',
		'var_formato' => 0,
		'var_conceito' => $txt_var_quant
	),
	'interno_var_mista' => array(
		'var_nome' => 'Variável Mista',
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
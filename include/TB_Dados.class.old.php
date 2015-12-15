<?php
require_once('phpvortex/TB_Base.class.php');

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

function instr($haysack, $neddle) {
	if (strpos($haysack, $neddle) !== FALSE) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function strcmp_semacento ($a, $b) {
	return strcmp(semacento($a), semacento($b));
}

function SemAno($v) {
	$tmp = explode('_', $v);
	return $tmp[0];
}

class TB_Dados extends TB_Base {
	function ShowCross($base, $posicao, $filtro, $csv = FALSE) {
		$var_cods = array();
		foreach ($_SESSION['vars'] as $var) {
			$pc = explode("_", $var);
			$var_cods[] = $pc[0];
		}

		$linhas = $colunas = $conteudos = array();
		foreach ($posicao as $var => $row) {
			if (instr($row['pos'], 'S') || instr($row['pos'], 'M') || instr($row['pos'], 'F')) {
				$conteudos[sprintf("%05d", $row['ordem']).$var] = $var;
			} elseif (instr($row['pos'], 'L')) {
				$linhas[sprintf("%05d", $row['ordem']).$var] = $var;
			} elseif (instr($row['pos'], 'C')) {
				$colunas[sprintf("%05d", $row['ordem']).$var] = $var;
			}
                    /*old
			if (instr($row['pos'], 'L')) {
				$linhas[sprintf("%05d", $row['ordem']).$var] = $var;
			} elseif (instr($row['pos'], 'C')) {
				$colunas[sprintf("%05d", $row['ordem']).$var] = $var;
			} elseif (instr($row['pos'], 'S') || instr($row['pos'], 'M') || instr($row['pos'], 'F')) {
				$conteudos[sprintf("%05d", $row['ordem']).$var] = $var;
			}*/
		}
		ksort($linhas);
		$linhas = array_values($linhas);
		ksort($colunas);
		$colunas = array_values($colunas);
		ksort($conteudos);
		$conteudos = array_values($conteudos);

		$sql = "SELECT var_cod, var_nome, var_formato FROM tb_{$base}_variavel WHERE var_cod IN ('".implode("', '", $var_cods)."')";
		$rs = $this->db->Query($sql);
		$vars = array('frequencia' => array('nome' => 'Freqüência'));
		while ($row = $rs->Row()) {
			$vars[$row['var_cod']]['nome'] = $row['var_nome'];
			if ($row['var_formato'] > 0) {
				$sql = "SELECT fmt_val, fmt_desc, fmt_ordem FROM tb_formato_valor WHERE fmt_cod = {$row['var_formato']} ORDER BY fmt_ordem";
				$rsf = $this->db->Query($sql);
				while ($rowf = $rsf->Row()) {
					$vars[$row['var_cod']]['formato'][$rowf['fmt_val']] = $rowf['fmt_desc'];
					$vars[$row['var_cod']]['formato_ordem'][$rowf['fmt_val']] = sprintf("%09d", $rowf['fmt_ordem']);
				}
				$rsf->Close();
			}
		}
		$rs->Close();
		
		$linhas_usadas = array();
		$colunas_usadas = array();
		
		$sql = $this->GeraSQL($base, $posicao, $filtro);
		$rs = $this->db->Query($sql);
		while ($row = $rs->Row()) {
			$il = $ol = $ic = $oc = '';
			foreach ($linhas as $linha) {
				$il .= (strlen($il)?'|':'') . $row[$linha];
				$ol .= (strlen($ol)?'|':'') . $vars[SemAno($linha)]['formato_ordem'][$row[$linha]];
			}
			foreach ($colunas as $coluna) {
				$ic .= (strlen($ic)?'|':'') . $row[$coluna];
				$oc .= (strlen($oc)?'|':'') . $vars[SemAno($coluna)]['formato_ordem'][$row[$coluna]];
			}
			$linhas_usadas[$ol] = $il;
			$colunas_usadas[$oc] = $ic;
			$i = $il . '-' . $ic;
			foreach ($conteudos as $conteudo) {
				if ($posicao[$conteudo]['pos'] == 'S') {
					$dados[$i][$conteudo] = $row[$conteudo];
					if (!isset($total_col[$ic][$conteudo])) $total_col[$ic][$conteudo] = 0;
					$total_col[$ic][$conteudo] += $row[$conteudo];
					if (!isset($total_row[$il][$conteudo])) $total_row[$il][$conteudo] = 0;
					$total_row[$il][$conteudo] += $row[$conteudo];
					if (!isset($total[$conteudo])) $total[$conteudo] = 0;
					$total[$conteudo] += $row[$conteudo];
				} elseif (($posicao[$conteudo]['pos'] == 'M') && ($row[$conteudo.'_count'] > 0)) {
					$dados[$i][$conteudo] = $row[$conteudo] / $row[$conteudo.'_count'];
					if (!isset($total_col[$ic][$conteudo])) $total_col[$ic][$conteudo]['n'] = $total_col[$ic][$conteudo]['d'] = 0;
					$total_col[$ic][$conteudo]['n'] += $row[$conteudo];
					$total_col[$ic][$conteudo]['d'] += $row[$conteudo.'_count'];
					if (!isset($total_row[$il][$conteudo])) $total_row[$il][$conteudo]['n'] = $total_row[$il][$conteudo]['d'] = 0;
					$total_row[$il][$conteudo]['n'] += $row[$conteudo];
					$total_row[$il][$conteudo]['d'] += $row[$conteudo.'_count'];
					if (!isset($total[$conteudo])) $total[$conteudo]['n'] = $total[$conteudo]['d'] = 0;
					$total[$conteudo]['n'] += $row[$conteudo];
					$total[$conteudo]['d'] += $row[$conteudo.'_count'];
				} elseif ($posicao[$conteudo]['pos'] == 'F') {
					$dados[$i][$conteudo] = $row['contagem'];
					if (!isset($total_col[$ic][$conteudo])) $total_col[$ic][$conteudo] = 0;
					$total_col[$ic][$conteudo] += $row['contagem'];
					if (!isset($total_row[$il][$conteudo])) $total_row[$il][$conteudo] = 0;
					$total_row[$il][$conteudo] += $row['contagem'];
					if (!isset($total[$conteudo])) $total[$conteudo] = 0;
					$total[$conteudo] += $row['contagem'];
				}
			}
		}
		$rs->Close();

		uksort($linhas_usadas, "strcmp_semacento");
		uksort($colunas_usadas, "strcmp_semacento");
//		ksort($linhas_usadas);
//		ksort($colunas_usadas);
		
		if ($csv) {
			$buff = '';
			if (!empty($linhas)) {
				foreach ($linhas as $linha) {
					$pc = explode('_', $linha);
					$buff .= (empty($buff)?'':';') . '"' . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . '"';
				}
			}
			$ops = array('S' => 'Soma de ', 'M' => 'Média de ', 'F' => '');
			if (!empty($colunas)) {
				foreach ($colunas_usadas as $cu) {
					$col_buff = '';
					$pc = explode('|', $cu);
					for ($i = 0;$i < count($pc);$i++) {
						$col_buff .= (empty($col_buff)?'':' -- ') . $vars[SemAno($colunas[$i])]['nome'] . ' = ' . $vars[SemAno($colunas[$i])]['formato'][$pc[$i]];
					}
					foreach ($conteudos as $conteudo) {
						$pc = explode('_', $conteudo);
						$buff .= (empty($buff)?'':';') . '"' . (empty($col_buff)?'':($col_buff . ' -- ')) . $ops[$posicao[$conteudo]['pos']] . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . '"';
					}
				}
				$buff .= (empty($buff)?'':';') . '"Total"';
			} else {
				foreach ($conteudos as $conteudo) {
					$pc = explode('_', $conteudo);
					$buff .= (empty($buff)?'':';') . '"' . $ops[$posicao[$conteudo]['pos']] . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . '"';
				}
			}
			echo "$buff\n";
			foreach ($linhas_usadas as $ol => $lu) {
				$buff = '';
				if (!empty($linhas)) {
					$pc = explode('|', $lu);
					$i = 0;
					foreach ($pc as $tl) {
						$npc = explode('_', $linhas[$i]);
						$buff .= (empty($buff)?'':';') . '"' . $vars[$npc[0]]['formato'][$tl] . '"';
					}
				}
				foreach ($colunas_usadas as $cu) {
					foreach ($conteudos as $c) if (isset($dados[$lu.'-'.$cu][$c])) {
						$buff .= (empty($buff)?'':';') . number_format($dados[$lu.'-'.$cu][$c], (($posicao[$c]['pos'] == 'M')?1:0), ',', '.');
					} else {
						$buff .= (empty($buff)?'':';') . '"-"';
					}
				}
				if (!empty($colunas)) {
					foreach ($conteudos as $c) if (isset($total_row[$lu][$c])) {
						if (($posicao[$c]['pos'] == 'S') || ($posicao[$c]['pos'] == 'F') && ($total_row[$lu][$c] != 0)) {
							$buff .= ';' . number_format($total_row[$lu][$c], 0, ',', '.');
						} elseif (($posicao[$c]['pos'] == 'M') && ($total_row[$lu][$c]['d'] > 0)) {
							$buff .= ';' . number_format($total_row[$lu][$c]['n']/$total_row[$lu][$c]['d'], 1, ',', '.');
						} else {
							$buff .= ';"-"';
						}
					} else {
						$buff .= ';"-"';
					}
				}
				echo "$buff\n";
			}
			if (!empty($linhas)) {
				$buff = 'Total' . str_repeat(";", count($linhas) - 1);
				foreach ($colunas_usadas as $cu) {
					foreach ($conteudos as $c) if (isset($total_col[$cu][$c])) {
						if (($posicao[$c]['pos'] == 'S') || ($posicao[$c]['pos'] == 'F') && ($total_col[$cu][$c] != 0)) {
							$buff .= ';' . number_format($total_col[$cu][$c], 0, ',', '.');
						} elseif (($posicao[$c]['pos'] == 'M') && ($total_col[$cu][$c]['d'] > 0)) {
							$buff .= ';' . number_format($total_col[$cu][$c]['n']/$total_col[$cu][$c]['d'], 1, ',', '.');
						} else {
							$buff .= ';"-"';
						}
					} else {
						$buff .= ';"-"';
					}
				}
				if (!empty($colunas)) {
					foreach ($conteudos as $c) if (isset($total[$c])) {
						if (($posicao[$c]['pos'] == 'S') || ($posicao[$c]['pos'] == 'F') && ($total[$c] != 0)) {
							$buff .= ';' . number_format($total[$c], 0, ',', '.');
						} elseif (($posicao[$c]['pos'] == 'M') && ($total[$c]['d'] > 0)) {
							$buff .= ';' . number_format($total[$c]['n']/$total[$c]['d'], 1, ',', '.');
						} else {
							$buff .= ';"-"';
						}
					} else {
						$buff .= ';"-"';
					}
				}
				echo "$buff\n";
			}
		} else {		
			echo "<div class='div_cross'><table id='tb_dados'>\n<thead>\n<tr>\n";
			if (!empty($linhas)) {
				$mapa_lin = array();
				foreach ($linhas_usadas as $lu) {
					$pc = explode('|', $lu);
					for ($i = 0;$i < count($pc);$i++) {
						$p = '';
						for ($j = 0;$j <= $i;$j++) $p .= (empty($p)?'':'|') . $pc[$j];
						if (!isset($mapa_lin[$linhas[$i]][$p])) {
							$mapa_lin[$linhas[$i]][$p]['label'] = $vars[SemAno($linhas[$i])]['formato'][$pc[$i]];
							$mapa_lin[$linhas[$i]][$p]['rows'] = 0;
						}
						$mapa_lin[$linhas[$i]][$p]['rows']++;
					}
				}
				foreach ($linhas as $linha) {
					$pc = explode('_', $linha);
					echo "<th rowspan='".(count($colunas) + 2)."'>" . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . "</th>\n";
					$lin = 0;
					foreach ($mapa_lin[$linha] as $ml) {
						$head_linhas[$linha][$lin]['rows'] = $ml['rows'];
						$head_linhas[$linha][$lin]['label'] = $ml['label'];
						$lin += $ml['rows'];
					}
				}
			}
			if (!empty($colunas)) {
				$tc = '';
				$mapa = array();
				
				foreach ($colunas as $coluna) {
					$pc = explode('_', $coluna);
					$tc .= (empty($tc)?'':' X ') . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]);
				}
				foreach ($colunas_usadas as $cu) {
					$pc = explode('|', $cu);
					for ($i = 0;$i < count($pc);$i++) {
						$p = '';
						for ($j = 0;$j <= $i;$j++) $p .= (empty($p)?'':'|') . $pc[$j];
						if (!isset($mapa[$colunas[$i]][$p])) {
							$mapa[$colunas[$i]][$p]['label'] = $vars[SemAno($colunas[$i])]['formato'][$pc[$i]];
							$mapa[$colunas[$i]][$p]['rows'] = 0;
						}
						$mapa[$colunas[$i]][$p]['rows']++;
					}
				}
				echo "<th colspan='".(count($colunas_usadas) * count($conteudos))."'>$tc</th>\n<th rowspan='".(count($colunas) + 1)."' colspan='".count($conteudos)."'>Total</th>\n</tr>\n<tr>\n";
				foreach ($colunas as $coluna) {
					foreach ($mapa[$coluna] as $col) {
						echo "<th colspan='" . ($col['rows'] * count($conteudos)) . "' align='left'>{$col['label']}</th>\n";
					}
					echo "</tr>\n<tr>\n";
				}
			}
			$ops = array('S' => 'Soma de ', 'M' => 'Média de ', 'F' => '');
			foreach ($colunas_usadas as $cu) {
				foreach ($conteudos as $conteudo) {
					$pc = explode('_', $conteudo);
					echo "<th>" . $ops[$posicao[$conteudo]['pos']] . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . "</th>\n";
				}
			}
			if (!empty($colunas)) {
				foreach ($conteudos as $conteudo) {
					$pc = explode('_', $conteudo);
					echo "<th>" . $ops[$posicao[$conteudo]['pos']] . $vars[$pc[0]]['nome'] . (empty($pc[1])?'':' - ' . $pc[1]) . "</th>\n";
				}
			}
			echo "</tr>\n</thead>\n<tbody>\n";
			$lin = 0;
			foreach ($linhas_usadas as $ol => $lu) {
				echo "<tr>\n";
				if (!empty($linhas)) {
					foreach ($linhas as $linha) if (isset($head_linhas[$linha][$lin])) {
						 echo "<th rowspan='{$head_linhas[$linha][$lin]['rows']}' align='left'>{$head_linhas[$linha][$lin]['label']}</th>\n";
					}
				}
				$lin++;
				foreach ($colunas_usadas as $cu) {
					foreach ($conteudos as $c) if (isset($dados[$lu.'-'.$cu][$c])) {
						echo "<td class='fd_dados' nowrap>" . number_format($dados[$lu.'-'.$cu][$c], (($posicao[$c]['pos'] == 'M')?1:0), ',', '.') . "</td>";
					} else {
						echo "<td class='fd_dados' nowrap>-</td>";
					}
				}
				if (!empty($colunas)) {
					foreach ($conteudos as $c) if (isset($total_row[$lu][$c])) {
						if (($posicao[$c]['pos'] == 'S') || ($posicao[$c]['pos'] == 'F') && ($total_row[$lu][$c] != 0)) {
							echo "<td class='fd_dados' nowrap>" . number_format($total_row[$lu][$c], 0, ',', '.') . "</td>\n";
						} elseif (($posicao[$c]['pos'] == 'M') && ($total_row[$lu][$c]['d'] > 0)) {
							echo "<td class='fd_dados' nowrap>" . number_format($total_row[$lu][$c]['n']/$total_row[$lu][$c]['d'], 1, ',', '.') . "</td>\n";
						} else {
							echo "<td class='fd_dados' nowrap>-</td>";
						}
					} else {
						echo "<td class='fd_dados' nowrap>-</td>";
					}
				}
				echo "</tr>\n";
			}
			if (!empty($linhas)) {
				echo "<tr>\n<th colspan='".count($linhas)."' align='left'>Total</th>\n";
				foreach ($colunas_usadas as $cu) {
					foreach ($conteudos as $c) if (isset($total_col[$cu][$c])) {
						if (($posicao[$c]['pos'] == 'S') || ($posicao[$c]['pos'] == 'F') && ($total_col[$cu][$c] != 0)) {
							echo "<td class='fd_dados' nowrap>" . number_format($total_col[$cu][$c], 0, ',', '.') . "</td>\n";
						} elseif (($posicao[$c]['pos'] == 'M') && ($total_col[$cu][$c]['d'] > 0)) {
							echo "<td class='fd_dados' nowrap>" . number_format($total_col[$cu][$c]['n']/$total_col[$cu][$c]['d'], 1, ',', '.') . "</td>\n";
						} else {
							echo "<td class='fd_dados' nowrap>-</td>";
						}
					} else {
						echo "<td class='fd_dados' nowrap>-</td>";
					}
				}
				if (!empty($colunas)) {
					foreach ($conteudos as $c) if (isset($total[$c])) {
						if (($posicao[$c]['pos'] == 'S') || ($posicao[$c]['pos'] == 'F') && ($total[$c] != 0)) {
							echo "<td class='fd_dados' nowrap>" . number_format($total[$c], 0, ',', '.') . "</td>\n";
						} elseif (($posicao[$c]['pos'] == 'M') && ($total[$c]['d'] > 0)) {
							echo "<td class='fd_dados' nowrap>" . number_format($total[$c]['n']/$total[$c]['d'], 1, ',', '.') . "</td>\n";
						} else {
							echo "<td class='fd_dados' nowrap>-</td>";
						}
					} else {
						echo "<td class='fd_dados' nowrap>-</td>";
					}
				}
				echo "</tr>\n";
			}
			echo "</tbody>\n</table></div>\n";
		} // $csv

		#dv(2, "vars", $vars);
		#dv(2, "linhas", $linhas);
		#dv(2, "colunas", $colunas);
		#dv(2, "conteudos", $conteudos);
		#dv(2, "linhas_usadas", $linhas_usadas);
		#dv(2, "colunas_usadas", $colunas_usadas);
	}

	function GeraSQL($base, $posicao, $filtro) {
		$anos = array();
		$g = '';
		$s = 'COUNT(*) AS contagem';
		$w = '';
		foreach ($posicao as $var => $row) if ($var != 'frequencia') {
			if (instr($row['pos'], 'L') || instr($row['pos'], 'C')) {
				$s .= ", $var";
				$g .= (empty($g)?'':', ') . $var;
				if (instr($row['pos'], 'X')) $w .= (empty($w)?'':' AND ') . "(NOT ISNULL($var))";
			} elseif (instr($row['pos'], 'S') || instr($row['pos'], 'M')) {
				$s .= ", SUM($var) AS $var";
				if (instr($row['pos'], 'M')) $s .= ", COUNT($var) AS {$var}_count";
			}
		}
		foreach ($filtro as $cod => $row) {
			if (!empty($row['tipo']) && !strcmp($row['tipo'], 'C')) {
				$w .= (empty($w)?'':' AND ') . "($cod IN (".implode(', ', $row['lista'])."))";
			} elseif (!empty($row['tipo']) && !strcmp($row['tipo'], 'L') && is_numeric($row['val'])) { 
				$w .= (empty($w)?'':' AND ') . "($cod {$row['op']} {$row['val']})";
			}
		}
		$sw = '';
		if (!empty($w)) $sw = "WHERE $w";
		$sg = '';
		if (!empty($g)) $sg = "GROUP BY $g";
		$select = "SELECT $s FROM tb_{$base}_dados $sw $sg";
		$sqls = "SELECT $s FROM tb_{$base}_dados $sw $sg;\n";
		#dv(2, "SQLs", $sqls);
		return $select;
	}
}

?>
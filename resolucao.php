<?php
    require("functions.php");
    require("Template.class.php");
    $tpl = new Template("resolucao.html");
    $value = $_POST["FPnum"];
    $tpl->VALOR = $value;

    if($value != 0){
        if($value < 0){
	    $tpl->block("BLOCK_NEGATIVO");
            $sinal = 1;
        }
        else{
            $tpl->block("BLOCK_POSITIVO");
            $sinal = 0;
        }
        $tpl->NUM_BINARIO = convert_bin($value);
        $bin = $tpl->NUM_BINARIO;
        $tpl->EXPOENTE = get_exp($bin,$value);
        $exp = $tpl->EXPOENTE;
        //Lembrar a questao da faixa de valores, para saber se devera usara funcao normalizada, ou nao
        $tpl->EXP_EXC = 3 + $exp;
	$tpl->EXP_EXC_BIN = decbin(3 + $exp);
	$tpl->MANTISSA = get_mantissa($bin);
	$tpl->VALOR_FP = implode([$sinal,$tpl->EXP_EXC_BIN,$tpl->MANTISSA]);
	$tpl->block("BLOCK_RESOLUCAO");
    }
 
    $tpl -> show();

?>

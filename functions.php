<?php
    
    function exp_hash_norm($exp){
        $exps = array(-2 => "001", -1 => "010", 0 => "011", 1 => "100", 2 => "101", 3 => "110");
	return $exps[$exp];
    }
//===========================================================================================
    function exp_hash_to_dec($exp){
        $exps = array(-2 => "001", -1 => "010", 0 => "011", 1 => "100", 2 => "101", 3 => "110");
        return array_search($exp,$exps);
    }
//===========================================================================================
    //funcao que faz a conversao no numero no caso nao normalizado
    function NNcalcFP($binary,$number){
	$binary = str_split($binary);
	$converted = array();
        $aux = array();
	if($number > 0){
	    array_push($converted,0);
	}
	else{
            array_push($converted,1);
	}
	array_push($converted,"000");
	for($i = array_search(',',$binary)+3 ; $i < count($binary) || count($aux) < 4;$i++)
	{
            array_push($aux,$binary[$i]);
	}
        
	if(count($aux) < 4){
	    array_push($aux,0);
	}

        return implode(array(implode($converted),implode($aux)));
    }

//===========================================================================================
    //funcao para calcular o valor do expoente
    function get_exp($binary, $number){
        $binary = str_split($binary);

        if($number > 1 || $number < 0){
	     $exp = (array_search(',',$binary) - 1);
	} 
	else{
	     $exp = (-(array_search('1',$binary)) + array_search(',',$binary));
        }		
	return $exp;
    }


    //funcao para extrair a mantissa do numero
    function get_mantissa($binary){ 
	$binary = str_split($binary);
	$n = count($binary);
        $mant = array();
	$counter = 0;
	for($i = 1; $i < $n; $i++){
	    if($counter == 4){
	        break;
            }
	    if(strcmp($binary[$i], ',') != 0){
	        $counter++;
		array_push($mant,$binary[$i]);
	    }
        }
	while($counter < 4){
	    array_push($mant,0);
	    $counter++;
		    
	}
        return implode($mant);
    }
//===========================================================================================

    function calcFP($binary,$number){
	$converted = array();//vetor que contera o resultado final
	$binary = str_split($binary);
        $bin_is_negative = False;
        
	//selecionando o sinal do numero
	if($number > 0){
            array_push($converted,0);
	}
	else{
	    array_push($converted,1);
            $bin_is_negative = True;
	    $number = -$number;
	}
        //selecao do expoente
	if($number > 1 || $number < 0){
	    $exp = (array_search(',',$binary) - 1);
	}
	else{
	    $exp = (-(array_search('1',$binary)) + array_search(',',$binary));
	}
	array_push($converted,exp_hash_norm($exp));

	$n = count($binary);
	$counter = 0;
	//extraindo a parte do numero apos o primeiro 1
	//no caso nao normalizado
	if($number > 0 && $number < 1){
            $first_one = array_search('1',$binary);
	    for($i = $first_one+1; $i < $n; $i++){
                if($counter == 4){
                    break;
		}
		$counter++;
		array_push($converted,$binary[$i]);
	    }

	}
	//caso normalizado
	else{
	    for($i = 1; $i < $n; $i++){
	        if($counter == 4){
	            break;
                }
	        if(strcmp($binary[$i], ',') != 0){
	            $counter++;
		    array_push($converted,$binary[$i]);
	        }
            }
	}
	//caso ainda falte numeros pra completar os bits, preenche com zeros
	while($counter < 4){
	    array_push($converted,0);
	    $counter++;
		    
	}
	return implode($converted);

  }

//===========================================================================================
    //funcao para converter a parte fracionaria do numero para binario
    //OBS: ainda generalizar para mais representacoes
    function calcFracPart($fraction){
        $frac_bin = array();
	$mult_times = 0;
	$pos = 0;
	while($mult_times < 7){
            $fraction = $fraction * 2;
	    if($fraction >= 1){
                $frac_bin[$pos] = 1;
		$pos++;
		$fraction--;
	    }
	    else{
                $frac_bin[$pos] = 0;
		$pos++;
	    }

	    if($fraction == 0){
                break;
	    }
	    $mult_times++;
	}
	return implode($frac_bin);
    }

//===========================================================================================
    //funcao que faz a conversao do numero para FP
    function convertFP($numToConvert){
	$num_copy = $numToConvert;

	if($numToConvert < 0){
            $num_copy = -$numToConvert;
	}
        //extraindo a parte fracionaria da parte inteira
	$num_dec_part = (int) $num_copy;
	$num_frac_part = $num_copy - $num_dec_part;
	//convertendo a parte inteira para binario
	$bin_dec_part = decbin($num_dec_part);
	//chamando a funcao para pegar a parte fracionaria em binario
	$bin_frac_part = calcFracPart($num_frac_part);
	//juntando os valores em uma unica string
        $num_bin_full = implode(array($bin_dec_part,',', $bin_frac_part));
        
	//caso da notacao nao normalizada
	if($num_copy < 0.25){
            $num_converted = NNcalcFP($num_bin_full,$numToConvert);
	}
	//caso da notacao normalizada
	else{
            $num_converted = calcFP($num_bin_full,$numToConvert);
	}
	return $num_converted;
    }

//=========================================================================================
  
    function rebuildFP($num_fp){
        $sinal = $num_fp[0];//sinal do numero
        $expoente_fp = array();//expoente do numero em FP
	$mantissa = array();//mantissa do numero em FP
	$mantissa_decimal = array();//o valor da mantissa em decimal
	$parte_inteira = array();//a parte inteira no numero em decimal
	$parte_fracionaria = array();//a parte fracionária do numero em decimal
       
	//extraindo o expoente do numero em FP
	for($i = 1; $i < 4;$i++){
            array_push($expoente_fp,$num_fp[$i]);
	}
	//extraindo a mantissa do numero em FP
	for($i = 4; $i < 8; $i++){
            array_push($mantissa,$num_fp[$i]);
	}
        
	$expoente_fp = implode($expoente_fp);
	$expoente_decimal = exp_hash_to_dec($expoente_fp);//convertendo o expoente para decimal

        //de acordo com o valor do expoente, ajusta como serao extraidos os valores da mantissa
	if($expoente_decimal < 0){
            array_push($mantissa_decimal,0);
	    array_push($mantissa_decimal,',');
	    $aux = (-$expoente_decimal) - 1;
	    for($i = 0; $i < $aux; $i++){
	        array_push($mantissa_decimal,$mantissa[$i]);
	    }
	}

	else{
	    array_push($mantissa_decimal,'1');
	    for($i = 0; $i < count($mantissa); $i++){
                if ($i == $expoente_decimal){
                   array_push($mantissa_decimal,',');
		}
		array_push($mantissa_decimal,$mantissa[$i]);
	    }
	}

	$aux = array_search(',',$mantissa_decimal);

        for($i = 0; $i < $aux; $i++){
            array_push($parte_inteira,$mantissa_decimal[$i]);
	}

	$parte_inteira = array_reverse($parte_inteira);

	for($i = $aux + 1; $i < count($mantissa_decimal);$i ++){
            array_push($parte_fracionaria,$mantissa_decimal[$i]);
	}

	$resultado = 0;

        //calculando o resultado em decimal, parte inteira
	for($i = 0; $i < count($parte_inteira);$i++){
            if(strcmp($parte_inteira[$i],'0') == 0){
                continue;
	    }
	    $resultado += pow(2,$i);
	}
        //calculando o resultado em decimal, parte fracionaria
	for($i = 0; $i < count($parte_fracionaria); $i++){
            if(strcmp($parte_fracionaria[$i],'0') == 0){
                continue;
	    }
	    $resultado += pow(2,-($i+1));
	}

	if(strcmp($sinal,'0') == 0){
            return implode(array("",$resultado));
	}
	else{
            return implode(array("-",$resultado));
	}
    }

//=========================================================================================
    //Funcao que retorna o numero completo em binario
    function convert_bin($numToConvert){
		$num_copy = $numToConvert;

		if($numToConvert < 0){
	            $num_copy = -$numToConvert;
		}

		$num_dec_part = (int) $num_copy;
		$num_frac_part = $num_copy - $num_dec_part;
		$bin_dec_part = decbin($num_dec_part);
		$bin_frac_part = calcFracPart($num_frac_part);
		$num_bin_full = implode(array($bin_dec_part,',', $bin_frac_part));
		return $num_bin_full;
	}
?>

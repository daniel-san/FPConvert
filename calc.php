<?php
    require("functions.php");
    require("Template.class.php");
    $tpl = new Template("calc.html");
    $value = $_POST["FPnum"];
    if($value != 0){	    
        $tpl->RESULTADO = convertFP($value);
	$tpl->block("BLOCK_RESULTADO");
    }
    $tpl -> show();
?>

<?php
    require("functions.php");
    require("Template.class.php");
    $tpl = new Template("reconstrucao.html");
    $value = $_POST["FPnum"];
    if($value != 0){	    
        $tpl->RESULTADO = rebuildFP($value);
	$tpl->block("BLOCK_RESULTADO");
    }
    $tpl -> show();

?>

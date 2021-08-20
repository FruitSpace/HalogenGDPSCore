<?php

//Simple error handler
require __DIR__ . "/../../conf/mainconfig.php";

$errTypes= array(
    'warn'=>'<font color="yellow">WARNING</font>',
    'err'=>'<font color="red">ERROR</font>',
    'fatal'=>'<font color="darkred">FATAL</font>',
	'verbose'=>'<font color="gray">INFO</font>'
);

function err_handle($module, $errLevel, $message, $die=true){
    global $errTypes;
    $prefix="[<strong>".$errTypes[$errLevel]."</strong> | ".date("d/m/Y H:i:s")."]";
    $message=$prefix." <strong>$module</strong>:<br><pre>\t".htmlspecialchars($message)."</pre><hr>";
    $fd=fopen( __DIR__ . "/../../files/" .LOG_FILE,"a");
    fwrite($fd,$message);
    if($errLevel=="warn" or $errLevel=="verbose") $die=false;
    if($die) die();
}
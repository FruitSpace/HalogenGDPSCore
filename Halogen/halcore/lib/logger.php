<?php

//Simple error handler
require "../../conf/mainconfig.php";

$errTypes= array(
    'warn'=>'<font color="yellow">WARNING</font>',
    'err'=>'<font color="red">ERROR</font>',
    'fatal'=>'<font color="darkred">FATAL</font>'
);

function err_handle($module, $errLevel, $message, $die=true){
    global $errTypes;
    $prefix="[<strong>".$errTypes[$errLevel]."</strong> | ".date("d/m/Y H:i:s")."]";
    $message=$prefix." <strong>$module</strong>:<br><pre>\t$message</pre><hr>";
    $fd=fopen(LOG_FILE,"a");
    fwrite($fd,$message);
    if($die) die();
}
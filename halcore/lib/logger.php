<?php
//Simple error handler
require __DIR__ . "/../../conf/mainconfig.php";

if(!defined('LOG_HTML')) define("LOG_HTML",false);

$errTypes= array(
    'warn'=>'<font color="yellow">WARNING</font>',
    'err'=>'<font color="red">ERROR</font>',
    'fatal'=>'<font color="darkred">FATAL</font>',
	'verbose'=>'<font color="gray">INFO</font>'
);

function log_html($module,$errLevel, $message){
	global $errTypes;
	$prefix="[<strong>".$errTypes[$errLevel]."</strong> | ".date("d/m/Y H:i:s")."]";
	$message=$prefix." <strong>$module</strong>:<br><pre>\t".htmlspecialchars($message)."</pre><hr>";
	$fd=fopen( __DIR__ . "/../../files/" .LOG_FILE.".html","a");
	fwrite($fd,$message);
}

function err_handle($module, $errLevel, $message, $die=true){
    if(LOG_HTML) log_html($module, $errLevel, $message);
	$prefix="[".$errLevel." | ".date("d/m/Y H:i:s")."]";
	$message=$prefix."$module:\t".str_replace("\n","\t",str_replace("\t"," ",$message))."\n";
	$fd=fopen( __DIR__ . "/../../files/" .LOG_FILE,"a");
	fwrite($fd,$message);
    if($errLevel=="warn" or $errLevel=="verbose") $die=false;
    if($die) die();
}
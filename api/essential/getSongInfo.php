<?php
if(!isset($_POST['secret']) or empty($_POST['songID'])) die();
require_once __DIR__."/../../halcore/lib/DBManagement.php";
require_once __DIR__."/../../halcore/CMusic.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
    header('HTTP/1.1 403 Forbidden');
    die('This IP is banned for security reasons');
}

$dbm=new DBManagement();
$cm=new CMusic($dbm);
if($cm->getSong((int)$_POST['songID'])>0){
    echo "1~|~".$cm->id."~|~2~|~".$cm->name."~|~3~|~1~|~4~|~".$cm->artist."~|~5~|".$cm->size."~|~6~|~~|~10~|~".$cm->url;
}else{
    echo "-1";
}
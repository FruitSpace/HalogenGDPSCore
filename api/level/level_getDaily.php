<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CQuests.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(!isset($_POST['secret'])) die();
$weekly=(empty($_POST['weekly'])?false:true);
$dbm=new DBManagement();
$cq=new CQuests($dbm);
echo $cq->getDailyLevel($weekly);
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__;
	err_handle("ENDPOINT","verbose",$former);
}
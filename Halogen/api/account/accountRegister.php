<?php
require_once __DIR__."/../../halcore/lib/DBManagement.php";
require_once __DIR__."/../../halcore/CAccount.php";
require_once __DIR__."/../../halcore/lib/legacy.php";
require_once __DIR__."/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['userName']) and isset($_POST['password']) and isset($_POST['email']) and $_POST['userName']!=""
	and $_POST['password']!="" and $_POST['email']!=""){
	$uname=exploitPatch_remove($_POST['userName']);
	$pass=exploitPatch_remove($_POST['password']);
	$email=exploitPatch_remove($_POST['email']);
	$dbm=new DBManagement();
	$acc=new CAccount($dbm);
	echo $acc->register($uname,$pass,$email,$ip);
	$r=0;
}else{
	echo "-1";
	$r=1;
}
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__." ::with".($r==1?"out":"")." auth data";
	err_handle("ENDPOINT","verbose",$former);
}
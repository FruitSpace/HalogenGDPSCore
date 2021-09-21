<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/ThunderAES.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['userName']) and isset($_POST['password']) and $_POST['userName']!="" and $_POST['password']!="") {
	$uname = exploitPatch_remove($_POST['userName']);
	$pass = exploitPatch_remove($_POST['password']);
	$dbm=new DBManagement();
	$acc=new CAccount($dbm);
	if($acc->logIn($uname,$pass,$ip)>=0){
		$fh= __DIR__ . "/../../files/savedata/" .$acc->uid.".hal";
		if(file_exists($fh)){
			$taes= new ThunderAES();
			$taes->genkey($pass);
			$dat=$taes->decrypt(file_get_contents($fh));
			echo $dat.";21;30;a;a";
		}else{
			echo "-1";
		}
	}else{
		echo "-2";
	}
	$r=0;
}else{
	echo "-1";
	$r=1;
}
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__." ::with".($r==1?"out":"")." auth data";
	err_handle("ENDPOINT","verbose",$former);
}

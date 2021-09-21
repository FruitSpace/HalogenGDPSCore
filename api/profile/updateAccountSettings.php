<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['gjp']) and $_POST['accountID']!="" and $_POST['gjp']!=""){
	$uid=(int)$_POST['accountID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$acc=new CAccount($dbm);
		$acc->uid=$uid;
		$acc->mS=(empty($_POST['mS'])?0:(int)$_POST['mS']);
		$acc->frS=(empty($_POST['frS'])?0:(int)$_POST['frS']);
		$acc->cS=(empty($_POST['cS'])?0:(int)$_POST['cS']);
		$acc->youtube=(empty($_POST['yt'])?"":exploitPatch_remove($_POST['yt']));
		$acc->twitter=(empty($_POST['twitter'])?"":exploitPatch_remove($_POST['twitter']));
		$acc->twitch=(empty($_POST['twitch'])?"":exploitPatch_remove($_POST['twitch']));
		$acc->pushSettings();
		echo "1";
	}else{
		echo "-1";
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
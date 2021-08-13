<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__."/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['str']) and $_POST['str']!=""){
	$str=exploitPatch_remove($_POST['str']);
	$dbm=new DBManagement();
	$acc=new CAccount($dbm);
	$uid=$acc->searchUsers($str);
	if(empty($uid)){
		echo "-1";
	}else{
		$acc->uid=$uid['uid'];
		$acc->loadAuth();
		$acc->loadVessels();
		$acc->loadStats();
		echo "1:".$acc->uname.":2:".$acc->uid.":13:".$acc->coins.":17:".$acc->ucoins.":9:".$acc->getShownIcon().":10:".$acc->colorPrimary.":11:".$acc->colorSecondary.":14:".$acc->iconType.":15:".$acc->special.":16:".$acc->uid.":3:".$acc->stars.":8:".$acc->cpoints.":4:".$acc->demons."#999:0:10";
	}
}else{
	echo "-1";
}
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__;
	err_handle("ENDPOINT","verbose",$former);
}
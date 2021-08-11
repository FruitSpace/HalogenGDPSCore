<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/CFriendship.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__;
	err_handle("ENDPOINT","verbose",$former);
}
if(isset($_POST['targetAccountID']) and $_POST['targetAccountID']!=""){
	$uid=(int)$_POST['targetAccountID'];
	$dbm=new DBManagement();
	$uid_self=0;
	if (isset($_POST['accountID']) and $_POST['accountID']!=""){
		$uid_self=(int)$_POST['accountID'];
		$gjp=exploitPatch_remove($_POST['gjp']);
		if(!$lsec->verifySession($dbm, $uid_self, $ip, $gjp)) {
			die("-1");
		}
	}
	$acc=new CAccount($dbm);
	if(!$acc->exists($uid)) die("-1");
	$acc->uid=$uid;
	$acc->loadAll();

	//check blacklist status
	$blacklist=explode(",",$acc->blacklist);
	if(in_array($uid_self,$blacklist)) die("-1");
	$rank=($acc->isBanned>0?0:$acc->getLeaderboardRank($uid));
	if($uid==$uid_self){

	}
}else{
	echo "-1";
}
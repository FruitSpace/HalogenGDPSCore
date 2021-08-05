<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CFriendship.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['toAccountID']) and isset($_POST['gjp']) and $_POST['accountID']!=""
	and $_POST['toAccountID']!="" and $_POST['gjp']!=""){
	$uid=(int)$_POST['accountID'];
	$uid_dest=(int)$_POST['toAccountID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$comment=(isset($_POST['comment']) and $_POST['comment']!=""?exploitPatch_remove($_POST['comment']):null);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cf=new CFriendship($dbm);
		echo $cf->requestFriend($uid, $uid_dest, $comment);
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
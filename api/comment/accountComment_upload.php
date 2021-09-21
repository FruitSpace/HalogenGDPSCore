<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CComment.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__ . "/../../halcore/lib/halhost.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['comment']) and isset($_POST['gjp']) and $_POST['accountID']!=""
	and $_POST['comment']!="" and $_POST['gjp']!=""){
	$uid=(int)$_POST['accountID'];
	$comment=exploitPatch_remove($_POST['comment']);
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cc = new CComment($dbm);
		if(checkPosts($cc->countAccComments())) {
			$cc->uid = $uid;
			$cc->comment = $comment;
			echo $cc->postAccComment();
		}else{
			echo "-1";
		}
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

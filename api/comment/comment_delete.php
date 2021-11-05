<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CComment.php";
require_once __DIR__ . "/../../halcore/CLevel.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__ . "/../../halcore/CHalogen.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['commentID']) and isset($_POST['gjp']) and isset($_POST['levelID'])
	and $_POST['accountID']!="" and $_POST['commentID']!="" and $_POST['gjp']!=""  and $_POST['levelID']!=""){
	$uid=(int)$_POST['accountID'];
	$id=(int)$_POST['commentID'];
	$lvl_id=(int)$_POST['levelID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cc=new CComment($dbm);
		$cl=new CLevel($dbm);
        $ch=new CHalogen($dbm);
		$cl->id=$lvl_id;
		if($cl->isOwnedBy($uid)) {
			$cc->deleteOwnerLvlComment($id, $lvl_id);
		}else{
			$cc->deleteLvlComment($id, $uid);
		}
        $ch->onComment();
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
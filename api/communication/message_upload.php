<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CMessage.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__ . "/../../halcore/CProtect.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['gjp']) and isset($_POST['toAccountID']) and $_POST['accountID']!="" and $_POST['gjp']!=""
	and $_POST['toAccountID']!="" and isset($_POST['body']) and $_POST['body']!=""){
	$uid=(int)$_POST['accountID'];
	$uid_dest=(int)$_POST['toAccountID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$body=exploitPatch_remove($_POST['body']);
	$subject=(isset($_POST['subject'])?exploitPatch_remove($_POST['subject']):"");
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cm=new CMessage($dbm);
		$cm->uid_src=$uid;
		$cm->uid_dest=$uid_dest;
		$cm->subject=$subject;
		$cm->message=$body;
        $protect=new CProtect($dbm);
        if($protect->detectMessages($uid)) {
            echo $cm->sendMessageObj();
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
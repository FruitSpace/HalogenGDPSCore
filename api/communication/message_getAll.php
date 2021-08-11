<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CMessage.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['gjp']) and $_POST['accountID']!="" and $_POST['gjp']!=""){
	$uid=(int)$_POST['accountID'];
	$getSent=(empty($_POST['getSent'])?0:1);
	$page=(empty($_POST['page'])?0:(int)$_POST['page'])*10;
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cm=new CMessage($dbm);
		$r=$cm->getMessagesForUid($uid, $page, (bool)$getSent);
		if($r=="-2"){
			echo "-2";
		}else{
			$cnt=$r['cnt'];
			unset($r['cnt']);
			$msg_str="";
			foreach ($r as $msg){
				$ago=getDateAgo(strtotime($msg['date']));
				$msg_str.= "1:".$msg['id'].":2:".$msg['uid'].":3:".$msg['uid'].":4:".$msg['subject'].":5:".$msg['message'].":6:".$msg['uname'].":7:".$ago.":8:".((int)(!$msg['isNew'])).":9:".$getSent."|";
			}
			echo substr($msg_str,0,-1)."#$cnt:$page:10";
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
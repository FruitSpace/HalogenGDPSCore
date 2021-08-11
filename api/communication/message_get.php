<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CMessage.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['gjp']) and isset($_POST['messageID']) and $_POST['accountID']!="" and $_POST['gjp']!="" and $_POST['messageID']!=""){
	$uid=(int)$_POST['accountID'];
	$id=(int)$_POST['messageID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cm=new CMessage($dbm);
		if($cm->exists($id)){
			$cm->loadMessageById($id);
			if($uid==$cm->uid_dest or $uid==$cm->uid_src){
				$uidx=($uid==$cm->uid_dest?$cm->uid_src:$cm->uid_dest);
				$acc=new CAccount($dbm);
				$acc->uid=$uidx;
				$acc->loadAuth();
				$ago=getDateAgo(strtotime($cm->postedtime));
				echo "1:".$cm->id.":2:".$uidx.":3:".$uidx.":4:".$cm->subject.":5:".$cm->message.":6:".$acc->uname.":7:".$ago.":8:".((int)(!$cm->isNew)).":9:0";
			}else{
				echo "-1";
			}
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
<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
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
	$type=(empty($_POST['type'])?0:1);
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$acc=new CAccount($dbm);
		$acc->uid=$uid;
		$acc->loadSocial();
		if($type==1){
			$blacklist=explode(",",$acc->blacklist);
			if(empty($blacklist) or empty($acc->blacklist)){
				echo "-2";
			}else {
				$bstring = "";
				foreach ($blacklist as $buid) {
					$bacc = new CAccount($dbm);
					$bacc->uid = $buid;
					$bacc->loadAuth();
					$bacc->loadVessels();
					$bacc->loadStats();
					$bstring .= "1:" . $bacc->uname . ":2:" . $buid . ":9:" . $bacc->getShownIcon() . ":10:" . $bacc->colorPrimary . ":11:" . $bacc->colorSecondary . ":14:" . $bacc->iconType . ":15:" . $bacc->special . ":16:" . $buid . ":18:0:41:1|";
				}
				echo substr($bstring, 0, -1);
			}
		}else{
			if($acc->friendsCount==0){
				echo "-2";
			}
			else{
				require_once __DIR__ . "/../../halcore/CFriendship.php";
				$friends=explode(",",$acc->friendshipIds);
				$fstring="";
				$cf=new CFriendship($dbm);
				foreach ($friends as $fid) {
					$fx=$cf->getFriendByFID($fid);
					$fuid=($fx['uid1']==$uid?$fx['uid2']:$fx['uid1']);
					$facc = new CAccount($dbm);
					$facc->uid = $fuid;
					$facc->loadAuth();
					$facc->loadVessels();
					$facc->loadStats();
					$fstring .= "1:" . $facc->uname . ":2:" . $fuid . ":9:" . $facc->getShownIcon() . ":10:" . $facc->colorPrimary . ":11:" . $facc->colorSecondary . ":14:" . $facc->iconType . ":15:" . $facc->special . ":16:" . $fuid . ":18:0:41:0|";
				}
				echo substr($fstring, 0, -1);
			}
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
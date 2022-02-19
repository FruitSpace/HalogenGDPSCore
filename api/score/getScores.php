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
if(!isset($_POST['secret'])) die();
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__;
	err_handle("ENDPOINT","verbose",$former);
}

$type=(empty($_POST['type'])?"top":$_POST['type']);
$dbm=new DBManagement();
$acc=new CAccount($dbm);
switch($type){
	case "relative":
		$uid=(int)$_POST['accountID'];
		$gjp=exploitPatch_remove($_POST['gjp']);
		if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
			$acc->uid=$uid;
			$acc->loadStats();
			$users = $acc->getLeaderboard(CLEADERBOARD_GLOBAL,null,$acc->stars);
		}else{
			$users=array();
		}
		break;
	case "friends":
        if(empty($_POST['accountID'])) die('-1');
		$uid=(int)$_POST['accountID'];
		$gjp=exploitPatch_remove($_POST['gjp']);
		if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
			$acc->uid=$uid;
			$acc->loadSocial();
			require_once __DIR__ . "/../../halcore/CFriendship.php";
			$cf=new CFriendship($dbm);
			if($acc->friendsCount==0){$users=array();break;}
			$friendships=explode(",",$acc->friendshipIds);
			$friend_ids=array();
			array_push($friend_ids,$uid);
			foreach ($friendships as $frid){
                if(!is_numeric($frid)) continue;
				$ids=$cf->getFriendByFID($frid);
                if($ids===null) continue;
				$fid=($ids['uid1']==$uid?$ids['uid2']:$ids['uid1']);
				array_push($friend_ids,$fid);
			}
			$users = $acc->getLeaderboard(CLEADERBOARD_FRIENDS,$friend_ids);
		}else{
			$users=array();
		}
		break;
	case "creators":
		$users=$acc->getLeaderboard(CLEADERBOARD_BY_CPOINTS);
		break;
	case "top":
	default:
		$users=$acc->getLeaderboard(CLEADERBOARD_BY_STARS);
}

if(empty($users)) die('-1');
$output="";
$lk=0;
foreach ($users as $usr){
	$cacc=new CAccount($dbm);
	$cacc->uid=$usr;
	$cacc->loadAuth();
	$cacc->loadVessels();
	$cacc->loadStats();
	$lk++;
	$output.="1:".$cacc->uname.":2:".$cacc->uid.":3:".$cacc->stars.":4:".$cacc->demons.":6:".$lk.":7:".$cacc->uid.":8:".$cacc->cpoints;
	$output.=":9:".$cacc->getShownIcon().":10:".$cacc->colorPrimary.":11:".$cacc->colorSecondary.":13:".$cacc->coins.":14:".$cacc->iconType;
	$output.=":15:".$cacc->special.":16:".$cacc->uid.":17:".$cacc->ucoins.":46:".$cacc->diamonds."|";
}
echo substr($output,0,-1);
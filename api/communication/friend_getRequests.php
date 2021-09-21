<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CFriendship.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
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
		$cf=new CFriendship($dbm);
		$r=$cf->getFriendRequests($uid,$page,(bool)$getSent);
		if($r=="-2"){
			echo "-2";
		}else{
			$cnt=$r['cnt'];
			unset($r['cnt']);
			$frrq_str="";
			foreach ($r as $frq){
				$ago=getDateAgo(strtotime($frq['date']));
				$frrq_str.= "1:".$frq["uname"].":2:".$frq["uid"].":9:".$frq["iconId"].":10:".$frq["clr_primary"].":11:".$frq["clr_secondary"].":14:".$frq["iconType"].":15:".$frq["special"].":16:".$frq['uid'].":32:".$frq["id"].":35:".$frq["comment"].":41:".$frq["isNew"].":37:".$ago."|";
			}
			echo substr($frrq_str,0,-1)."#$cnt:$page:10";
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
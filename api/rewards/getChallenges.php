<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__."/../../halcore/CQuests.php";

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
if(isset($_POST['udid']) and isset($_POST['chk'])  and $_POST['udid']!="" and $_POST['chk']!=""){
	$uid=(empty($_POST['accountID'])?0:(int)$_POST['accountID']);
	$udid=exploitPatch_remove($_POST['udid']);
	$chk=exploitPatch_remove($_POST['chk']);
	$dbm=new DBManagement();
	$cq=new CQuests($dbm);
	if($cq->exists(QUEST_TYPE_CHALLENGE)) {
		$chk = doXOR(base64_decode(substr($chk, 5)), 19847);
		$quests = $cq->getQuests();
		$output = "SaKuJ:" . $uid . ":" . $chk . ":" . $udid . ":" . $uid . ":" . strtotime("tomorrow midnight") . ":" . $quests;
		$output = str_replace("+", "-", str_replace("/", "_", base64_encode(doXOR($output, 19847))));
		echo "SaKuJ" . $output . "|" . genhash_genSolo3($output);
	}else{
		echo "-2";
	}
}else{
	echo "-1";
}
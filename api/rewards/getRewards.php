<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

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
if(isset($_POST['accountID']) and isset($_POST['udid']) and isset($_POST['gjp']) and isset($_POST['chk'])
	and $_POST['accountID']!="" and $_POST['udid']!="" and $_POST['gjp']!=""  and $_POST['chk']!=""){
	$uid=(int)$_POST['accountID'];
	$udid=exploitPatch_remove($_POST['udid']);
	$chk=exploitPatch_remove($_POST['chk']);
	$gjp=exploitPatch_remove($_POST['gjp']);
	$type=(empty($_POST['rewardType'])?0:((int)$_POST['rewardType'])%3);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		require_once __DIR__ . "/../../halcore/CAccount.php";
		require_once __DIR__ . "/../../conf/chests.php";
		$acc=new CAccount($dbm);
		$chk=doXOR(base64_decode(substr($chk,5)),59182);
		$acc->uid=$uid;
		$acc->loadChests();
		$chestSmallLeft=max(0,CHEST_SMALL_WAIT-time()+$acc->chestSmallTime);
		$chestBigLeft=max(0,CHEST_BIG_WAIT-time()+$acc->chestBigTime);
		//GenContent: Orbs,Diamonds,Shards,Keys
		$chestSmallRewards=rand(CHEST_SMALL_ORBS_MIN,CHEST_SMALL_ORBS_MAX).",".rand(CHEST_SMALL_DIAMONDS_MIN,CHEST_SMALL_DIAMONDS_MAX);
		$chestSmallRewards.=",".rand(CHEST_SMALL_SHARDS_MIN,CHEST_SMALL_SHARDS_MAX).",".rand(CHEST_SMALL_KEYS_MIN,CHEST_SMALL_KEYS_MAX);
		$chestBigRewards=rand(CHEST_BIG_ORBS_MIN,CHEST_BIG_ORBS_MAX).",".rand(CHEST_BIG_DIAMONDS_MIN,CHEST_BIG_DIAMONDS_MAX);
		$chestBigRewards.=",".rand(CHEST_BIG_SHARDS_MIN,CHEST_BIG_SHARDS_MAX).",".rand(CHEST_BIG_KEYS_MIN,CHEST_BIG_KEYS_MAX);
		//Open chests
		if($type==1){
			if($chestSmallLeft==0) {
				$acc->chestSmallCount++;
				$acc->pushChests(CREWARD_CHEST_SMALL);
			}else{
				die("-1");
			}
		}elseif($type==2){
			if($chestBigLeft==0){
				$acc->chestBigCount++;
				$acc->pushChests(CREWARD_CHEST_BIG);
			}else{
				die("-1");
			}
		}
		$output="1:".$uid.":".$chk.":".$udid.":".$uid.":".$chestSmallLeft.":".$chestSmallRewards.":".$acc->chestSmallCount.":".$chestBigLeft.":".$chestBigRewards.":".$acc->chestBigCount.":".$type;
		$output=str_replace("+","-",str_replace("/","_",base64_encode(doXOR($output,59182))));
		echo "SaKuJ".$output."|".genhash_genSolo4($output);
	}else{
		echo "-1";
	}
}else{
	echo "-1";
}
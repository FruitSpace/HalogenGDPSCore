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
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$acc=new CAccount($dbm);
		$acc->uid=$uid;
		$acc->loadStats();
		$acc->colorPrimary=(empty($_POST['color1'])?0:(int)$_POST['color1']);
		$acc->colorSecondary=(empty($_POST['color2'])?0:(int)$_POST['color2']);
		$acc->stars=(empty($_POST['stars'])?0:(int)$_POST['stars']);
		$acc->demons=(empty($_POST['demons'])?0:(int)$_POST['demons']);
		$acc->diamonds=(empty($_POST['diamonds'])?0:(int)$_POST['diamonds']);
		$acc->iconType=(empty($_POST['iconType'])?0:(int)$_POST['iconType']);
		$acc->coins=(empty($_POST['coins'])?0:(int)$_POST['coins']);
		$acc->ucoins=(empty($_POST['userCoins'])?0:(int)$_POST['userCoins']);
		$acc->special=(empty($_POST['special'])?0:(int)$_POST['special']);
		$acc->cube=(empty($_POST['accIcon'])?0:(int)$_POST['accIcon']);
		$acc->ship=(empty($_POST['accShip'])?0:(int)$_POST['accShip']);
		$acc->ball=(empty($_POST['accBall'])?0:(int)$_POST['accBall']);
		$acc->ufo=(empty($_POST['accBird'])?0:(int)$_POST['accBird']);
		$acc->wave=(empty($_POST['accDart'])?0:(int)$_POST['accDart']);
		$acc->robot=(empty($_POST['accRobot'])?0:(int)$_POST['accRobot']);
		$acc->spider=(empty($_POST['accSpider'])?0:(int)$_POST['accSpider']);
		$acc->trace=(empty($_POST['accGlow'])?0:(int)$_POST['accGlow']);
		$acc->death=(empty($_POST['accExplosion'])?0:(int)$_POST['accExplosion']);
		$acc->pushStats();
		$acc->pushVessels();
		echo $uid;
	}else{
		echo "0";
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
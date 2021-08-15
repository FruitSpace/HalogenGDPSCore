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
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__;
	err_handle("ENDPOINT","verbose",$former);
}
$dbm=new DBManagement();
$acc=new CAccount($dbm);
$users=$acc->getLeaderboard(CLEADERBOARD_BY_CPOINTS);
if(empty($users)) die("");
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
//:17::15::16::46:|";
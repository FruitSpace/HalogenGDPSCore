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
if(isset($_POST['itemID']) and isset($_POST['type']) and $_POST['itemID']!="" and $_POST['type']!=""){
	$id=(int)$_POST['itemID'];
	$type=(int)$_POST['type'];
	$like=(isset($_POST['like'])?(empty($_POST['like'])?false:true):true);
	err_handle("TMP","verbose","Liked: ".(int)$like);
	$dbm=new DBManagement();
	switch($type){
		case 1:
			require_once __DIR__ . "/../../halcore/CLevel.php";
			$cl=new CLevel($dbm);
			if($cl->exists($id)){
				$cl->likeLevel($id,($like?CLEVEL_ACTION_LIKE:CLEVEL_ACTION_DISLIKE));
			}else{
				echo "-1";
			}
			break;
		case 2:
			require_once __DIR__ . "/../../halcore/CComment.php";
			$cc=new CComment($dbm);
			if($cc->existsLvlComment($id)){
				$cc->likeLvlComment($id,($like?CCOMMENT_ACTION_LIKE:CCOMMENT_ACTION_DISLIKE));
			}else{
				echo "-1";
			}
			break;
		case 3:
			require_once __DIR__ . "/../../halcore/CComment.php";
			$cc=new CComment($dbm);
			if($cc->existsAccComment($id)){
				$cc->likeAccComment($id,($like?CCOMMENT_ACTION_LIKE:CCOMMENT_ACTION_DISLIKE));
			}else{
				echo "-1";
			}
			break;

	}
}else{
	echo "-1";
}
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__;
	err_handle("ENDPOINT","verbose",$former);
}
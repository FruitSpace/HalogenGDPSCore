<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CComment.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";

$ip=$_SERVER['REMOTE_ADDR'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(!isset($_POST['secret'])) die();
if(isset($_POST['accountID']) and isset($_POST['page']) and $_POST['accountID']!="" and $_POST['page']!=""){
	$uid=(int)$_POST['accountID'];
	$page=(int)$_POST['page'];
	$dbm = new DBManagement();
	$cc=new CComment($dbm);
	$comments=$cc->getAllAccComments($uid, $page);
	if(empty($comments)) {
		echo "#0:0:0"; //No comments lol
	}else{
		$output="";
		$commentcount=$cc->countAccComments($uid);
		foreach($comments as $comm){
			$age=getDateAgo(strtotime($comm->postedDate));
			$output.="2~".$comm->comment."~3~".$comm->uid."~4~".$comm->likes."~5~0~7~".$comm->isSpam."~9~".$age."~6~".$comm->id."|";
		}
		echo substr($output,0,-1)."#".$commentcount.":".($page*10).":10";
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
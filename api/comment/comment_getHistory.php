<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CComment.php";
require_once __DIR__."/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";

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
if(isset($_POST['userID']) and $_POST['userID']!=""){
	$id=(int)$_POST['userID'];
	$page=(empty($_POST['page'])?0:((int)$_POST['page'])*10);
	$dbm = new DBManagement();
	$cc=new CComment($dbm);
	$acc=new CAccount($dbm);
	$sortmode=(empty($_POST['mode'])?false:true);
	if(!$acc->exists($id)) die("-1");
	$acc->uid=$id;
	$acc->loadAuth();
	$acc->loadVessels();
	$roleObj=$acc->getRoleObj();
	$comments=$cc->getAllCommentsHistory($id,$page,$sortmode);
	if(empty($comments)) {
		echo "#0:0:0"; //No comments lol
	}else{
		$output="";
		$commentcount=$cc->countCommentHistory($id);
		foreach($comments as $comm){
			$age=getDateAgo(strtotime($comm->postedDate));
			$output.="1~".$comm->lvl_id."~2~".$comm->comment."~3~".$id."~4~".$comm->likes."~5~0~7~".$comm->isSpam."~9~".$age."~10~".$comm->percent;
			$output.="~11~".(empty($roleObj)?"0":$roleObj['level']).(empty($roleObj)?"":"~12~".$roleObj['color'])."~6~".$comm->id.":";
			//user part, Force No glow
			$output.="1~".$acc->uname."~9~".$acc->getShownIcon()."~10~".$acc->colorPrimary."~11~".$acc->colorSecondary."~14~".$acc->iconType."~15~0~16~".$acc->uid."|";
		}
		echo substr($output,0,-1)."#".$commentcount.":".($page*10).":10";
	}
}else{
	echo "-1";
}
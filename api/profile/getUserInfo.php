<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/CFriendship.php";
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
if(isset($_POST['targetAccountID']) and $_POST['targetAccountID']!=""){
	$uid=(int)$_POST['targetAccountID'];
	$dbm=new DBManagement();
	$uid_self=0;
	$acc=new CAccount($dbm);
	if(!$acc->exists($uid)) die("-1");
	$acc->uid=$uid;
	$acc->loadAll();
	$cf=new CFriendship($dbm);
	$isFriend=($uid_self>0?$cf->isAlreadyFriend($uid,$uid_self):0);
	$roleObj=$acc->getRoleObj();
	$output="1:".$acc->uname.":2:".$acc->uid.":3:".$acc->stars.":4:".$acc->demons.":6:".$acc->getLeaderboardRank().":7:".$acc->uid;
	$output.=":8:".$acc->cpoints.":9:".$acc->getShownIcon().":10:".$acc->colorPrimary.":11:".$acc->colorSecondary.":13:".$acc->coins;
	$output.=":14:".$acc->iconType.":15:".$acc->special.":16:".$acc->uid.":17:".$acc->ucoins.":18:".$acc->mS.":19:".$acc->frS;
	$output.=":20:".$acc->youtube.":21:".$acc->cube.":22:".$acc->ship.":23:".$acc->ball.":24:".$acc->ufo.":25:".$acc->wave.":26:".$acc->robot;
	$output.=":28:".$acc->trace.":29:1:30:".$acc->getLeaderboardRank().":31:".$isFriend.":43:".$acc->spider.":44:".$acc->twitter;
	$output.=":45:".$acc->twitch.":46:".$acc->diamonds.":48:".$acc->death.":49:".(empty($roleObj)?"0":$roleObj['level']).":50:".$acc->cS;
	//check blacklist status
	$blacklist=explode(",",$acc->blacklist);
	if(in_array($uid_self,$blacklist)) die("-1");
	$rank=($acc->isBanned>0?0:$acc->getLeaderboardRank($uid));
    if (isset($_POST['accountID']) and $_POST['accountID']!=""){
        $uid_self=(int)$_POST['accountID'];
        $gjp=exploitPatch_remove($_POST['gjp']);
        if($lsec->verifySession($dbm, $uid_self, $ip, $gjp)) {
            if($uid==$uid_self){
                require_once __DIR__ . "/../../halcore/CMessage.php";
                $cm=new CMessage($dbm);
                $fr_req=$cf->countFriendRequests($uid,true);
                $msg_new=$cm->countMessages($uid,true);
                $output.=":38:".$msg_new.":39:".$fr_req.":40:0";
            }
        }
    }
	echo $output;
}else{
	echo "-1";
}
<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CComment.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/CLevel.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__ . "/../../halcore/lib/halhost.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['comment']) and isset($_POST['gjp']) and isset($_POST['levelID'])
	and $_POST['accountID']!="" and $_POST['comment']!="" and $_POST['gjp']!="" and $_POST['levelID']!=""){
	$uid=(int)$_POST['accountID'];
	$id=(int)$_POST['levelID'];
	$percent=(empty($_POST['percent'])?0:((int)$_POST['percent'])%101);
	$comment=exploitPatch_remove($_POST['comment']);
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cl=new CLevel($dbm);
		if($cl->exists($id)) {
			$cl->id=$id;
			$acc = new CAccount($dbm);
			$acc->uid = $uid;
			$acc->loadAuth();
			$role = $acc->getRoleObj(true);
			$own=$cl->isOwnedBy($uid);
			if (!empty($role) or $own) {
				$modComment = base64_decode($comment);
				if ($modComment[0] == "!") {
					$cl->loadBase();
					require_once __DIR__ . "/../../halcore/lib/modCommandProcessor.php";
					$state = invokeCommands($dbm, $cl, $acc, $modComment, $own, (empty($role)?null:$role['privs']));
					if($state>0){
						echo "1";
					}else{
						echo "-1";
					}
				}else{
					$cc = new CComment($dbm);
					if (checkPosts($cc->countLevelComments())) {
						$cc->uid = $uid;
						$cc->lvl_id = $id;
						$cc->comment = $comment;
						$cc->percent = $percent;
						echo $cc->postLvlComment();
					} else {
						echo "-1";
					}
				}
			}else {
				$cc = new CComment($dbm);
				if (checkPosts($cc->countLevelComments())) {
					$cc->uid = $uid;
					$cc->lvl_id = $id;
					$cc->comment = $comment;
					$cc->percent = $percent;
					echo $cc->postLvlComment();
				} else {
					echo "-1";
				}
			}
		}else{
			echo "-1";
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

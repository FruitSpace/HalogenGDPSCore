<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/CScores.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['gjp']) and isset($_POST['levelID']) and $_POST['accountID']!=""
	and $_POST['gjp']!="" and $_POST['levelID']!=""){
	$uid=(int)$_POST['accountID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$id=(int)$_POST['levelID'];
	$mode=abs(empty($_POST['type'])?0:(int)$_POST['type'])%4;
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cs = new CScores($dbm);
		if (!empty($_POST['percent']) and !empty($_POST['s1'])) {
			$percent = abs((int)$_POST['percent']);
			$attempts = abs(((int)$_POST['s1'] < 8355 ? 1 : (int)$_POST['s1'] - 8354));
			$coins = abs(((int)$_POST['s9']<5820?0:(int)$_POST['s9']-5819));
			//cancel buff
			$coins = ($coins>3?3:$coins);
			$percent=($percent>100?100:$percent);
			$cs->uid=$uid;
			$cs->lvl_id=$id;
			$cs->percent=$percent;
			$cs->attempts=$attempts;
			$cs->coins=$coins;
			if($cs->scoreExistsByUid($uid,$id)){
				$cs->updateLevelScore();
			}else{
				$cs->uploadLevelScore();
			}
		}
		//Now the retrival part
		$scores=$cs->getScoresForLevelId($id,$mode+400);
		if(empty($scores)){
			echo "";
		}else{
			$output="";
			foreach ($scores as $score){
				$acc=new CAccount($dbm);
				$acc->uid=$score['uid'];
				$acc->loadAuth();
				$acc->loadVessels();
				//Ignore Glow/Special here
				$output.="1:".$acc->uname.":2:".$acc->uid.":3:".$score['percent'].":6:".$score['ranking'].":9:".$acc->getShownIcon();
				$output.=":10:".$acc->colorPrimary.":11:".$acc->colorSecondary.":13:".$score['coins'].":14:".$acc->iconType.":15:0:16:".$acc->uid;
				$output.=":42:".getDateAgo(strtotime($score['date']))."|";
			}
			echo substr($output,0,-1);
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
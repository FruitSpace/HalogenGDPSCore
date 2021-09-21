<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CLevel.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['gameVersion']) and isset($_POST['gjp']) and $_POST['accountID']!=""
	and $_POST['gameVersion']!="" and $_POST['gjp']!="" and isset($_POST['levelString']) and $_POST['levelString']!=""){
	$uid=(int)$_POST['accountID'];
	$gameVersion=(int)$_POST['gameVersion'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$stringLevel=exploitPatch_remove($_POST['levelString']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cl=new CLevel($dbm);
		$cl->uid=$uid;
		$cl->versionGame=$gameVersion;
		$cl->stringLevel=$stringLevel;
		$cl->name=(empty($_POST['levelName'])?"Unnamed":exploitPatch_remove($_POST['levelName']));
		$cl->description=(empty($_POST['levelDesc'])?"":exploitPatch_remove($_POST['levelDesc']));
		$cl->version=(empty($_POST['levelVersion'])?1:(int)$_POST['levelVersion']);
		$cl->length=(empty($_POST['levelLength'])?0:(int)$_POST['levelLength']);
		$cl->track_id=(empty($_POST['audioTrack'])?(isset($_POST['audioTrack'])?0:1):(int)$_POST['audioTrack']);
		$cl->password=(empty($_POST['password'])?0:(int)$_POST['password']);
		$cl->origId=(empty($_POST['original'])?0:(int)$_POST['original']);
		$cl->is2p=(empty($_POST['twoPlayer'])?0:1);
		$cl->song_id=(empty($_POST['songID'])?0:(int)$_POST['songID']);
		$cl->objects=(empty($_POST['objects'])?1:(int)$_POST['objects']);
		$cl->ucoins=(empty($_POST['coins'])?0:(int)$_POST['coins']);
		$cl->starsRequested=(empty($_POST['requestedStars'])?1:(int)$_POST['requestedStars']);
		$cl->isUnlisted=(empty($_POST['unlisted'])?0:1);
		$cl->isLDM=(empty($_POST['ldm'])?0:1);
		$cl->stringExtra=(empty($_POST['extraString'])?"29_29_29_40_29_29_29_29_29_29_29_29_29_29_29_29":exploitPatch_remove($_POST['extraString']));
		$cl->stringLevelInfo=(empty($_POST['levelInfo'])?"":exploitPatch_remove($_POST['levelInfo']));
		$cl->versionBinary=(empty($_POST['binaryVersion'])?0:(int)$_POST['binaryVersion']);
		if(!empty($_POST['levelID'])){
			$cl->id=(int)$_POST['levelID'];
			if($cl->isOwnedBy($uid)>0){
				$res=$cl->updateLevel();
				echo $res;
				if($res>0) {
					$xdata = array(
						"name" => $cl->name,
						"version" => $cl->version,
						"objects" => $cl->objects,
						"starsReq" => $cl->starsRequested
					);
					require_once __DIR__ . "/../../halcore/lib/actions.php";
					registerAction(ACTION_LEVEL_UPLOAD, $uid, $res, $xdata, $dbm);
				}
			}else{
				echo "-1";
			}
		}else{
			$res=$cl->uploadLevel();
			echo $res;
			if($res>0){
				$xdata=array(
					"name"=>$cl->name,
					"version"=>$cl->version,
					"objects"=>$cl->objects,
					"starsReq"=>$cl->starsRequested
				);
				require_once __DIR__."/../../halcore/lib/actions.php";
				registerAction(ACTION_LEVEL_UPLOAD,$uid,$res,$xdata,$dbm);
			}
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
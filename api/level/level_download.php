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
if(!isset($_POST['secret'])) die();
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__;
	err_handle("ENDPOINT","verbose",$former);
}
if(isset($_POST['levelID']) and $_POST['levelID']!=""){
	$id=(int)$_POST['levelID'];
	$dbm=new DBManagement();
	if(empty($_POST["gameVersion"])){
		$gameVersion = 1;
	}else {
		$gameVersion = (int)$_POST["gameVersion"];
	}
	$dailylvl=false;
	if($id<0){
		require_once __DIR__."/../../halcore/CQuests.php";
		$cq=new CQuests($dbm);
		$dailylvl=true;
		if($id==-1){
			if($cq->exists(QUEST_TYPE_DAILY)){
				$mid=$cq->getDaily();
				$id=$mid['lvl_id'];
				$wid=$mid['id'];
			}else{
				die("-2");
			}
		}else{
			if($cq->exists(QUEST_TYPE_WEEKLY)){
				$mid=$cq->getWeekly();
				$id=$mid['lvl_id'];
				$wid=$mid['id']+100001; //RobTop Why???
			}else{
				die("-2");
			}
		}
	}
	$cl=new CLevel($dbm);
	if(!$cl->exists($id)) die("-1");
	$cl->id=$id;
	$cl->loadAll();
	$cl->onDownloadLevel();
	$auto=0;
	$password=($cl->password==0?"0":base64_encode(doXOR($cl->password,26364)));
    $phash=$cl->password;
    if(isset($_POST['accountID']) and isset($_POST['gjp']) and $_POST['accountID']!="" and $_POST['gjp']!=""){
        $uid=(int)$_POST['accountID'];
        $id=(int)$_POST['levelID'];
        $gjp=exploitPatch_remove($_POST['gjp']);
        if($lsec->verifySession($dbm, $uid, $ip, $gjp)){
            require_once __DIR__ . "/../../halcore/CAccount.php";
            $acc=new CAccount($dbm);
            $acc->uid=$uid;
            $acc->loadAuth();
            $roles=$acc->getRoleObj(true);
            err_handle("TMPDIFF","warn","GOT AUTH - E?".empty($roles)." VAL=".$roles['privs']);
            if(!empty($roles) and $roles['privs']['cLvlAccess']==1){
                $password=base64_encode(doXOR("1",26364));
                $phash=1;
                err_handle("TMPDIFF","warn","P_ORIG: $cl->password | P_CH: $password + $phash");
            }
        }
    }
	if($cl->difficulty<0){
		$auto=1;
		$cl->difficulty=0;
	}
	if($cl->suggestDifficultyCnt>0 and $cl->starsGot==0){
		$diffCount=round($cl->suggestDifficulty);
		switch ($diffCount){
			case 1:
				$diffName="Auto";
				break;
			case 2:
				$diffName="Easy";
				break;
			case 3:
				$diffName="Normal";
				break;
			case 4:
			case 5:
				$diffName="Hard";
				break;
			case 6:
			case 7:
				$diffName="Harder";
				break;
			case 8:
			case 9:
				$diffName="Insane";
				break;
			case 10:
				$diffName="Demon";
				break;
			default:
				$diffName="Unspecified";
		}
		$suggestDiffText=" [Suggest: $diffName ($diffCount)]";
		$cl->description=base64_encode(base64_decode($cl->description).$suggestDiffText);
	}
	$output="1:".$cl->id.":2:".$cl->name.":3:".$cl->description.":4:".$cl->stringLevel.":5:".$cl->version.":6:".$cl->uid.":8:".($cl->difficulty>0?10:0).":9:".$cl->difficulty;
	$output.=":10:".$cl->downloads.":12:".$cl->track_id.":13:".$cl->versionGame.":14:".$cl->likes.":15:".$cl->length.":17:".($cl->demonDifficulty>=0?"1":"0");
	$output.=":18:".$cl->starsGot.":19:".$cl->isFeatured.":25:".$auto.":27:".$password.":28:".getDateAgo(strtotime($cl->uploadDate)).":29:".getDateAgo(strtotime($cl->updateDate));
	$output.=":30:".$cl->origId.":31:".$cl->is2p.":35:".$cl->song_id.":36:".$cl->stringExtra.":37:".$cl->ucoins.":38:".($cl->coins>0?1:0).":39:".$cl->starsRequested;
	$output.=":40:".$cl->isLDM.":42:".$cl->isEpic.":43:".((int)$cl->demonDifficulty>=0?$cl->demonDifficulty:"3").":45:".$cl->objects.":46:1:47:2";
	$output.=":48:1".($dailylvl?":41:".$wid:""); //GD 2.2 and daily/weekly

	//2.1 hashing
	$solo_str=$cl->uid.",".$cl->starsGot.",".($cl->demonDifficulty>=0?1:0).",".$cl->id.",".($cl->coins>0?1:0).",".$cl->isFeatured.",".$phash.",".($dailylvl?$wid:0);
	$output.="#".genhash_genSolo($cl->stringLevel)."#".genhash_genSolo2($solo_str);
    if($dailylvl){
        require_once __DIR__."/../../halcore/CAccount.php";
        $acc=new CAccount($dbm);
        $acc->uid=$cl->uid;
        $acc->loadAuth();
        $output.="#".$acc->uid.":".$acc->uname.":".$acc->uid;
    }
	echo $output;
}else{
	echo "-1";
}
<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CLevel.php";
require_once __DIR__ . "/../../halcore/CAccount.php";
require_once __DIR__ . "/../../halcore/CLevelFilter.php";
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
$versionGame=(empty($_POST['gameVersion'])?30:(int)$_POST['gameVersion']);
if($versionGame == 20){
	$versionBinary = (int)$_POST["binaryVersion"];
	if($versionBinary > 27) $versionGame++;
}


//Init empty array
$param=array();

err_handle("TMPR","verbose",json_encode($_POST));

$param['versionGame']=$versionGame;
$type=(empty($_POST['type'])?0:(int)$_POST['type']);
if(!empty($_POST['str'])){
	$param["sterm"]=exploitPatch_remove($_POST['str']);
}
if(!(empty($_POST['diff']) or preg_replace("/[^0-9,-]/", '',$_POST['diff'])=="-" or preg_replace("/[^0-9,-]/", '',$_POST['diff'])==",")){
	$diff=explode(",",$_POST['diff']);
	$xdiff=array();
	foreach ($diff as $df) {
		switch ((int)$df) {
			case -1:
				array_push($xdiff,"0"); // N/A
				break;
			case -2:
				$param['isDemon'] = true;
				if (!empty($_POST['demonFilter'])) {
					switch ((int)$_POST['demonFilter']) {
						case 1:
							$param['demonDiff'] = 3; //Demon Easy
							break;
						case 2:
							$param['demonDiff'] = 4; //Demon Medium
							break;
						case 3:
							$param['demonDiff'] = 0; //Demon Hard
							break;
						case 4:
							$param['demonDiff'] = 5; //Demon Insane
							break;
						case 5:
							$param['demonDiff'] = 6; //Demon Extreme
							break;
					}
				}
				break;
			case -3:
				array_push($xdiff,"-1"); //AUTO
				break;
			case 1:
				array_push($xdiff,"10"); //EASY
				break;
			case 2:
				array_push($xdiff,"20"); //NORMAL
				break;
			case 3:
				array_push($xdiff,"30"); //HARD
				break;
			case 4:
				array_push($xdiff,"40"); //HARDER
				break;
			case 5:
				array_push($xdiff,"50"); //INSANE
				break;
			default:
				array_push($xdiff,"-1"); //NOPE EM OUT
		}
	}
	$param['diff']=implode(",",$xdiff);
}

$page=abs((empty($_POST['page'])?0:((int)$_POST['page'])*10))%10000;

if(isset($_POST['len']) and !(preg_replace("/[^0-9,-]/", '',$_POST['len'])=="-") and !(preg_replace("/[^0-9,-]/", '',$_POST['len'])==",")){
	$len=explode(",",$_POST['len']);
	$xlen=array();
	foreach ($len as $ln){
		array_push($xlen,abs((int)$ln)%5);
	}
	$param['length']=implode(",",$xlen);
}

if(!empty($_POST['uncompleted'])) $param['completed']=false;
if(!empty($_POST['onlyCompleted'])) $param['completed']=true;
if(!empty($_POST['completedLevels'])){
	$lvls=explode(",",preg_replace("/[^0-9,-]/", '',$_POST['completedLevels']));
	$xlvls=array();
	foreach ($lvls as $lv){
		array_push($xlvls,(int)$lv);
	}
	$param['completedLevels']=implode(",",$xlvls);
}else{
	unset($param['completed']);
}
if(!empty($_POST['featured'])) $param['isFeatured']=true;
if(!empty($_POST['original'])) $param['isOrig']=true; //WHERE origId=0
if(!empty($_POST['twoPlayer'])) $param['is2p']=true;
if(!empty($_POST['coins'])) $param['coins']=true; //anycoins or nocoins
if(!empty($_POST['epic'])) $param['isEpic']=true;
if(!empty($_POST['star'])) $param['star']=true; //stars>0
if(!empty($_POST['noStar'])) $param['star']=false; //stars=0
if(!empty($_POST['song'])) $param['songid']=(int)$_POST['song'];
if(!empty($_POST['customSong'])) $param['songCustom']=true; //Track if not else ng

$dbm=new DBManagement();
$filter=new CLevelFilter($dbm);
if(empty($_POST['gauntlet']) or !is_numeric($_POST['gauntlet'])) {
	$isGau=false;
	switch ($type) {
		case 1:
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_MOSTDOWNLOADED); //most downloaded
			break;
		case 3:
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_TRENDING); //Trending
			break;
		case 4:
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_LATEST); //latest
			break;
		case 5:
			$levels = $filter->searchUserLevels($page, $param); //user level (uid in str)
			break;
		case 6:
		case 17:
			$param['isFeatured'] = true;
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_LATEST); //featured
			break;
		case 7:
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_MAGIC); //magic banana (10k+obj and long)
			break;
//MOD LEVELD DISABLED
		case 10:
			//list levels from str comma-sep
			if (empty($_POST['str'])) die("-1");
			$param['sterm'] = preg_replace("/[^0-9,]/", '', $param['sterm']);
			$levels = $filter->searchListLevels($page, $param);
			break;
		case 11:
			$param['star'] = true;
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_LATEST); //awarded order by date desc
			break;
		case 12:
			//follow who level
			if (empty($_POST['followed'])) die("-1");
			$param['followList'] = preg_replace("/[^0-9,]/", '', exploitPatch_remove($_POST['followed']));
			$levels = $filter->searchUserLevels($page, $param, true);
			break;
		case 13:
			//friend-ish
			break;
		case 16:
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_HALL); //Hall of fame order by stars desc
			break;
		case 0:
		case 2:
		case 15:
		default:
			$levels = $filter->searchLevels($page, $param, CLEVELFILTER_MOSTLIKED); //most liked
	}
}else{
	$isGau=true;
$levels=$filter->getGauntletLevels(abs((int)$_POST['gauntlet']));
}
if(empty($levels)) die("-2");
$output="";
$userstring="";
$hashstr="";
$xcl=new CLevel($dbm);
$count=$xcl->countLevels();
foreach($levels as $slevel){
	$cl=new CLevel($dbm);
	$cl->id=$slevel;
	$cl->loadAll();
	$acc=new CAccount($dbm);
	$acc->uid=$cl->uid;
	$acc->loadAuth();
	$userstring.=$acc->uid.":".$acc->uname.":".$acc->uid."|";
	$auto=0;
	if($cl->difficulty<0){
		$auto=1;
		$cl->difficulty=0;
	}
	//(:8:)(($cl->difficulty>0?10:0))  ":17:".($cl->demonDifficulty>=0?"1":"0")
	$output.="1:".$cl->id.":2:".$cl->name.":3:".$cl->description.":5:".$cl->version.":6:".$cl->uid.":8:".($cl->difficulty>0?10:0).":9:".$cl->difficulty.":10:".$cl->downloads;
	$output.=":12:".$cl->track_id.":13:".$cl->versionGame.":14:".$cl->likes.":15:".$cl->length.":17:".($cl->demonDifficulty>=0?"1":"0").":18:".$cl->starsGot;
	$output.=":19:".$cl->isFeatured.":25:".$auto.":30:".$cl->origId.":31:".$cl->is2p.":35:".$cl->song_id.":37:".$cl->ucoins.":38:".($cl->coins>0?1:0);
	$output.=":39:".$cl->starsRequested.":42:".$cl->isEpic.":43:".((int)$cl->demonDifficulty>=0?$cl->demonDifficulty:"3").($isGau?":44:1":"").":45:".$cl->objects.":46:1:47:2|";

	$hashstr.=((string)$cl->id)[0].((string)$cl->id)[strlen(((string)$cl->id))-1].$cl->starsGot.($cl->coins>0?1:0);
}
echo substr($output,0,-1)."#".substr($userstring,0,-1)."##".$count.":".$page.":10#".genhash_genSolo2($hashstr);

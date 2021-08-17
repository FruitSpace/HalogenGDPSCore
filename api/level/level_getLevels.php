<?php

die("1:11940:2:Level Easy:5:1:6:2565:8:10:9:20:10:51533144:12:0:13:7:14:3322464:17::43:4:25::18:3:19:14:42:0:45:0:3:Q29keQ==:15:3:30:0:31:0:37:0:38:0:39:0:46:1:47:2:35:0|1:490078:2:Easy :5:4:6:389329:8:10:9:30:10:17631529:12:0:13:20:14:1209623:17::43:0:25::18:4:19:161:42:0:45:0:3:Tm90IHNvIGhhcmQ=:15:3:30:0:31:0:37:3:38:1:39:2:46:1:47:2:35:0|1:59767:2:stereo madness v2:5:11:6:31245:8:10:9:20:10:15705278:12:0:13:18:14:966568:17::43:4:25::18:3:19:90:42:0:45:0:3:U3RlcmVvIG1hZG5lc3MgdjI=:15:3:30:0:31:0:37:0:38:0:39:0:46:1:47:2:35:0|1:506009:2:Stereo Madness X:5:1:6:6988:8:10:9:30:10:12234222:12:0:13:7:14:962014:17::43:0:25::18:4:19:170:42:0:45:0:3:RmluZCB0aHJlZSBjb2lucyAh:15:3:30:0:31:0:37:0:38:0:39:0:46:1:47:2:35:0|1:1512012:2:UFO Madness:5:1:6:114346:8:10:9:20:10:20782780:12:0:13:21:14:946449:17::43:4:25::18:3:19:2000:42:0:45:5447:3:QW1hemluZyBhcnQgdXBkYXRlIHRoaXMgaXMgYmV0dGVyIHRoYW4gZ29kIGVhdGVy:15:3:30:0:31:0:37:2:38:0:39:0:46:1:47:2:35:0|1:513124:2:Stereo Madness v2:5:13:6:322511:8:10:9:30:10:10765227:12:0:13:20:14:885498:17::43:0:25::18:4:19:170:42:0:45:0:3:ICAgICB2MTE6IFJlZGVzaWduZWQgdGhlIHdob2xlIGxldmVsISB2MTI6IEFkZGVkIGNvaW5zISAgICAgU3Vic2NyaWJlIHRvIG15IFlvdVR1YmUgY2hhbm5lbDogU3Vtc2FyIQ==:15:3:30:0:31:0:37:3:38:0:39:3:46:1:47:2:35:0|1:61757:2:square adventure:5:6:6:46587:8:10:9:30:10:14850262:12:0:13:19:14:756015:17::43:0:25::18:4:19:19:42:0:45:0:3:WW91IGNhbiBkbyBpdCE=:15:3:30:0:31:0:37:0:38:0:39:0:46:1:47:2:35:0|1:70196:2:Practice Level:5:1:6:36314:8:10:9:10:10:5776669:12:0:13:7:14:546598:17::43:3:25::18:2:19:0:42:0:45:0:3::15:3:30:0:31:0:37:0:38:0:39:0:46:1:47:2:35:0|1:364445:2:stage of madness:5:1:6:220662:8:10:9:30:10:8098501:12:0:13:7:14:529447:17::43:0:25::18:5:19:151:42:0:45:0:3:OXRoIHN0YWdlLiBsZXZlbCB1cA==:15:3:30:0:31:0:37:0:38:0:39:0:46:1:47:2:35:0|1:281148:2:Partition Madness:5:1:6:1187:8:10:9:30:10:5293357:12:0:13:20:14:483934:17::43:0:25::18:4:19:100:42:0:45:0:3:UGFydGl0aW9uLnMgTWFkbmVzcy4gaW5zZXJ0Y29pbiEgcGx6IHN1YnNjcmliZSBteSB5b3V0dWJlIGNoYW5uZWwuIHBhcnRpdGlvbiBnZW9tZXRyeQ==:15:3:30:281148:31:0:37:3:38:0:39:0:46:1:47:2:35:0#1187:Partition:13781|31245:IIINePtunEIII:1741|36314:YunHaSeu14:1187377|114346:ZelLink:677|220662:nurong3:134576|322511:Sumsar:92|389329:MrCheeseTigrr:40664##9999:0:10#73ee8f6e8e1429dce6c0875235bbe65512fc47df");

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
		}
	}
	$param['diff']=implode(",",$xdiff);
}

$page=(empty($_POST['page'])?0:((int)$_POST['page'])*10);

if(isset($_POST['len'])){
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
	$lvls=explode(",",$_POST['completedLevels']);
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
if(isset($_POST['coins'])) $param['coins']=((int)$_POST['coins']>0?true:false); //anycoins or nocoins
if(!empty($_POST['epic'])) $param['isEpic']=true;
if(!empty($_POST['star'])) $param['star']=true; //stars>0
if(!empty($_POST['noStar'])) $param['star']=false; //stars=0
if(!empty($_POST['song'])) $param['songid']=(int)$_POST['song'];
if(!empty($_POST['customSong'])) $param['songCustom']=true; //Track if not else ng

$dbm=new DBManagement();
$filter=new CLevelFilter($dbm);
switch($type){
	case 1:
		$levels=$filter->searchLevels($page,$param, CLEVELFILTER_MOSTDOWNLOADED); //most downloaded
		break;
	case 3:
		$levels=$filter->searchLevels($page,$param,CLEVELFILTER_TRENDING); //Trending
		break;
	case 4:
		$levels=$filter->searchLevels($page,$param,CLEVELFILTER_LATEST); //latest
		break;
	case 5:
		$levels=$filter->searchUserLevels($page,$param); //user level (uid in str)
		break;
	case 6:
	case 17:
		$param['isFeatured']=true;
		$levels=$filter->searchLevels($page,$param, CLEVELFILTER_LATEST); //featured
		break;
	case 7:
		$levels=$filter->searchLevels($page,$param, CLEVELFILTER_MAGIC); //magic banana (10k+obj and long)
		break;
//MOD LEVELD DISABLED
	case 10:
		//list levels from str comma-sep
		if(empty($_POST['str'])) die("-1");
		break;
	case 11:
		$param['star']=true;
		$levels=$filter->searchLevels($page,$param,CLEVELFILTER_LATEST); //awarded order by date desc
		break;
	case 12:
		//follow who level
		if(empty($_POST['followed'])) die("-1");
		break;
	case 13:
		//friend-ish
		break;
	case 16:
		$levels=$filter->searchLevels($page,$param, CLEVELFILTER_HALL); //Hall of fame order by stars desc
		break;
	case 0:
	case 2:
	case 15:
	default:
		$levels=$filter->searchLevels($page,$param, CLEVELFILTER_MOSTLIKED); //most liked
}

if(empty($levels)) die("-2");
$output="";
$userstring="";
$hashstr="";
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
	$output.="1:".$cl->id.":2:".$cl->name.":3:".$cl->description.":5:".$cl->version.":6:".$cl->uid.":8:10:9:".$cl->difficulty.":10:".$cl->downloads;
	$output.=":12:".$cl->track_id.":13:".$cl->versionGame.":14:".$cl->likes.":15:".$cl->length.":17:".$cl->demonDifficulty.":18:".$cl->starsGot;
	$output.=":19:".$cl->isFeatured.":25:".$auto.":29:".":30:".$cl->origId.":31:".$cl->is2p.":35:".$cl->song_id.":37:".$cl->ucoins.":38:".$cl->coins;
	$output.=":39:".$cl->starsRequested.":40:".$cl->isLDM.":42:".$cl->isEpic.":43:".$cl->demonDifficulty.":45:".$cl->objects.":46:1:47:2|";

	$hashstr.=$cl->id[0].$cl->id[strlen($cl->id)-1].$cl->starsGot.$cl->coins;
}
echo substr($output,0,-1)."#".substr($userstring,0,-1)."##".count($levels).":".$page.":10#".genhash_genSolo2($hashstr);
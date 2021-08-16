<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CLevel.php";
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
$versionGame=(empty($_POST['gameVersion'])?0:(int)$_POST['gameVersion']);
if($versionGame == 20){
	$versionBinary = (int)$_POST["binaryVersion"];
	if($versionBinary > 27) $versionGame++;
}
$param=array();
$type=(empty($_POST['type'])?0:(int)$_POST['type']);
if(!empty($_POST['str'])){
	$param["sterm"]=exploitPatch_remove($_POST['str']);
}
if(!empty($_POST['diff'])){
	switch((int)$_POST['diff']){
		case -1:
			$param['diff']=0; //N/A
			break;
		case -2:
			$param['isDemon']=true;
			if(!empty($_POST['demonFilter'])){
				switch ((int)$_POST['demonFilter']){
					case 1:
						$param['demonDiff']=3; //Demon Easy
						break;
					case 2:
						$param['demonDiff']=4; //Demon Medium
						break;
					case 3:
						$param['demonDiff']=0; //Demon Hard
						break;
					case 4:
						$param['demonDiff']=5; //Demon Insane
						break;
					case 5:
						$param['demonDiff']=6; //Demon Extreme
						break;
				}
			}
			break;
		case -3:
			$param['diff']=-1; //AUTO
			break;
		case 1:
			$param['diff']=10; //EASY
			break;
		case 2:
			$param['diff']=20; //NORMAL
			break;
		case 3:
			$param['diff']=30; //HARD
			break;
		case 4:
			$param['diff']=40; //HARDER
			break;
		case 5:
			$param['diff']=50; //INSANE
			break;
		}
}

if(isset($_POST['len'])){
	$param['length']=abs((int)$_POST['len'])%5;
}
$page=(empty($_POST['page'])?0:((int)$_POST['page'])*10);

if(!empty($_POST['uncompleted'])) $param['completed']=false;
if(!empty($_POST['onlyCompleted'])) $param['completed']=true;
if(!empty($_POST['featured'])) $param['isFeatured']=true;
if(!empty($_POST['original'])) $param['isOrig']=true; //WHERE origId=0
if(!empty($_POST['twoPlayer'])) $param['is2p']=true;
if(isset($_POST['coins'])) $param['coins']=((int)$_POST['coins']>0?true:false); //anycoins or nocoins
if(!empty($_POST['epic'])) $param['isEpic']=true;
if(!empty($_POST['star'])) $param['star']=true; //stars>0
if(!empty($_POST['noStar'])) $param['star']=false; //stars=0
if(!empty($_POST['song'])) $param['songid']=(int)$_POST['song'];
if(!empty($_POST['customSong'])) $param['songCustom']=true; //Track if not else ng
switch($type){
	case 0:
	case 2:
	case 15:
		//most liked
		break;
	case 1:
		//most downloaded
		break;
	case 3:
		//trending
		break;
	case 4:
		//latest
		break;
	case 5:
		//user level (uid in str)
		break;
	case 6:
	case 17:
		//fetured
		break;
	case 7:
		//magic banana (10k+obj and long)
		break;
	case 8:
		//mod levels (req uid and gjp)
		break;
	case 10:
		//list levels from str comma-sep
		if(empty($_POST['str'])) die("-1");
		break;
	case 11:
		//awarded order by date desc
		break;
	case 12:
		//follow who level
		if(empty($_POST['followed'])) die("-1");
		break;
	case 13:
		//friend-ish
		break;
	case 16:
		//Hall of fame order by stars desc
		break;
	default:
		die("Type was escaped in code");
}
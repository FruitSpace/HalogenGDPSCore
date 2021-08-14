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
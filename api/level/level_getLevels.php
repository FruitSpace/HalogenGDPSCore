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
	$dbm = new DBManagement();
	$cl=new CLevel($dbm);
	$cl->id=3;
	$cl->loadAll();
	$auto=0;
	$password=($cl->password==0?"0":doXOR($cl->password,26364));
	$output="1:".$cl->id.":2:".$cl->name.":3:".$cl->description.":4:".$cl->stringLevel.":5:".$cl->version.":6:".$cl->uid.":8:10:9:".$cl->difficulty;
	$output.=":10:".$cl->downloads.":12:".$cl->track_id.":13:".$cl->versionGame.":14:".$cl->likes.":15:".$cl->length.":17:".$cl->demonDifficulty;
	$output.=":18:".$cl->starsGot.":19:".$cl->isFeatured.":25:".$auto.":27:".$password.":28:".getDateAgo(strtotime($cl->uploadDate)).":29:".getDateAgo(strtotime($cl->updateDate));
	$output.=":30:".$cl->origId.":31:".$cl->is2p.":35:".$cl->song_id.":36:".$cl->stringExtra.":37:".$cl->ucoins.":38:".$cl->coins.":39:".$cl->starsRequested;
	$output.=":40:".$cl->isLDM.":42:".$cl->isEpic.":43:".$cl->demonDifficulty.":45:".$cl->objects.":46:1:47:2";
	$output.=":48:1".($dailylvl?":41:".$mid['id']:""); //GD 2.2 and daily/weekly
	echo $output;

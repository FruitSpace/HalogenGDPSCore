<?php

function getGauntletString(DBManagement $dbm){
	require_once __DIR__."/lib/legacy.php";
	$req=$dbm->query("SELECT packName,levels FROM levelpacks WHERE packType=1 ORDER BY CAST(packName as int) ASC");
	if($dbm->isEmpty($req)) return "-2";
	$reqm=array();
	while($res=$req->fetch_assoc()) $reqm[]=$res;
	$gau="";
	$hashstr="";
	foreach ($reqm as $sreq){
		$lvls=explode(",",$sreq['levels']);
		if(count($lvls)!=5) continue;
		if(!is_numeric($sreq['packName'])) continue;
		$gau.="1:".$sreq['packName'].":3:".$sreq['levels']."|";
		$hashstr.=$sreq['packName'].$sreq['levels'];
	}
	if(empty($gau)) return "-2";
	return substr($gau,0,-1)."#".genhash_genSolo2($hashstr);
}
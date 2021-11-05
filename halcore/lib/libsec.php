<?php

class LibSec{
	public $iplist;

	function __construct(){
		$this->loadIPBlacklist();
	}

	function loadIPBlacklist(){
		$this->iplist = explode("\n", file_get_contents(__DIR__ . "/../../files/ban_ip.txt"));
	}

	function saveIPBlacklist(){
		file_put_contents(__DIR__ . "/../../files/ban_ip.txt", implode("\n",$this->iplist));
	}

	function banIP($ip){
		if(!$this->isIPBlacklisted($ip)) array_push($this->iplist, $ip);
	}

	function unbanIP($ip){
		if($this->isIPBlacklisted($ip)) unset($this->iplist[array_search($ip,$this->iplist)]);
	}

	function isIPBlacklisted($ip){
		return in_array($ip,$this->iplist);
	}

	function verifySession(DBManagement $db, int $uid, $ip, $gjp){
		$req=$db->query("SELECT accessDate, lastIP, isBanned FROM users WHERE uid=$uid");
		if($db->isEmpty($req)) return 0;
		$req=$req->fetch_assoc();
		if($req['isBanned']>0) return 0;
		if($ip==$req['lastIP'] and (time()-strtotime($req['accessDate']))<3600) return 1;
		require_once __DIR__ . "/legacy.php";
		require_once __DIR__ . "/../CAccount.php";
		$gjp=str_replace("-","+",str_replace("_","/",$gjp));
		$gjp=doXOR(base64_decode($gjp),37526);
		$acc=new CAccount($db);
		if($acc->logIn(null,$gjp,$ip,$uid)>0) return 1;
		return 0;
	}
}
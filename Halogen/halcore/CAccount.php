<?php

define("CAUTH_UID",17);
define("CAUTH_UNAME",26);
define("CAUTH_EMAIL",35);
define("CBAN_BAN", 44);
define("CBAN_UNBAN", 53);
define("CBLACKLIST_BLOCK", 62);
define("CBLACKLIST_UNBLOCK",71);

class CAccount{

	public $uid, $uname, $passhash, $email, $role_id, $isBanned;
	public int $stars, $diamonds, $coins, $ucoins, $demons, $cpoints, $orbs, $special;
	public $regDate, $accessDate, $lastIP, $gameVer, $lvlsCompleted;
	public $blacklist, $friendsCount, $friendshipIds;
	public $iconType, $colorPrimary, $colorSecondary, $cube, $ship, $ball, $ufo, $wave, $robot, $spider, $trace, $death;
	public $chestSmallCount, $chestSmallTime, $chestBigCount, $chestBigTime;
	public $frS, $cS, $mS, $youtube, $twitch, $twitter;

	public DBManagement $db;

	function __construct($db){
		$this->db=$db;
	}

	function loadSettings(){
		$req=$this->db->query("SELECT settings FROM users WHERE uid=$this->uid");
		$reqd=json_decode($req->fetch_assoc()['settings']);
		$this->frS=$reqd['frS'];
		$this->cS=$reqd['cS'];
		$this->mS=$reqd['mS'];
		$this->youtube=$reqd['youtube'];
		$this->twitch=$reqd['twitch'];
		$this->twitter=$reqd['twitter'];
	}

	function loadChests(){
		$req=$this->db->query("SELECT chests FROM users WHERE uid=$this->uid");
		$reqd=json_decode($req->fetch_assoc()['chests']);
		$this->chestSmallCount=$reqd['small_count'];
		$this->chestBigCount=$reqd['big_count'];
		$this->chestSmallTime=$reqd['small_time'];
		$this->chestBigTime=$reqd['big_time'];
	}

	function loadVessels(){
		$req=$this->db->query("SELECT iconType,vessels FROM users WHERE uid=$this->uid");
		$reqd=json_decode($req->fetch_assoc()['vessels']);
		$this->iconType=$req->fetch_assoc()['iconType'];
		$this->colorPrimary=$reqd['clr_primary'];
		$this->colorSecondary=$reqd['clr_secondary'];
		$this->cube=$reqd['cube'];
		$this->ship=$reqd['ship'];
		$this->ball=$reqd['ball'];
		$this->ufo=$reqd['ufo'];
		$this->wave=$reqd['wave'];
		$this->robot=$reqd['robot'];
		$this->spider=$reqd['spider'];
		$this->trace=$reqd['trace'];
		$this->death=$reqd['death'];
	}

	function loadStats(){
		$req=$this->db->query("SELECT stars,diamonds,coins,ucoins,demons,cpoints,orbs,special FROM users WHERE uid=$this->uid")->fetch_assoc();
		$this->stars=$req['stars'];
		$this->diamonds=$req['diamonds'];
		$this->coins=$req['coins'];
		$this->ucoins=$req['ucoins'];
		$this->demons=$req['demons'];
		$this->cpoints=$req['cpoints'];
		$this->orbs=$req['orbs'];
		$this->special=$req['special'];
	}

	function loadAuth($method=CAUTH_UID){
		switch ($method){
			case CAUTH_UID:
				$req=$this->db->query("SELECT uid,uname,passhash,email,role_id,isBanned FROM users WHERE uid=$this->uid")->fetch_assoc();
				break;
			case CAUTH_UNAME:
				$req=$this->db->preparedQuery("SELECT uid,uname,passhash,email,role_id,isBanned FROM users WHERE uname=?","s",$this->uname)->fetch_assoc();
				break;
			case CAUTH_EMAIL:
				$req=$this->db->preparedQuery("SELECT uid,uname,passhash,email,role_id,isBanned FROM users WHERE email=?","s",$this->email)->fetch_assoc();
				break;
			default:
				return 1;

		}
		$this->uid=$req['uid'];
		$this->uname=$req['uname'];
		$this->passhash=$req['passhash'];
		$this->email=$req['email'];
		$this->role_id=$req['role_id'];
		$this->isBanned=$req['isBanned'];
	}

	function loadTechnical(){
		$req=$this->db->query("SELECT regDate,accessDate,lastIP,gameVer,lvlsCompleted FROM users WHERE uid=$this->uid")->fetch_assoc();
		$this->regDate=$req['regDate'];
		$this->accessDate=$req['accessDate'];
		$this->lastIP=$req['lastIP'];
		$this->gameVer=$req['gameVer'];
		$this->lvlsCompleted=$req['lvlsCompleted'];
	}

	function loadSocial(){
		$req=$this->db->query("SELECT blacklist,friends_cnt,friendship_ids FROM users WHERE uid=$this->uid")->fetch_assoc();
		$this->blacklist=$req['blacklist'];
		$this->friendsCount=$req['friends_cnt'];
		$this->friendshipIds=$req['friendship_ids'];
	}

	function loadAll(){
		$req=$this->db->query("SELECT * FROM users WHERE uid=$this->uid")->fetch_assoc();
		$this->uid=$req['uid'];
		$this->uname=$req['uname'];
		$this->passhash=$req['passhash'];
		$this->email=$req['email'];
		$this->role_id=$req['role_id'];
		$this->isBanned=$req['isBanned'];
		$this->stars=$req['stars'];
		$this->diamonds=$req['diamonds'];
		$this->coins=$req['coins'];
		$this->ucoins=$req['ucoins'];
		$this->demons=$req['demons'];
		$this->cpoints=$req['cpoints'];
		$this->orbs=$req['orbs'];
		$this->special=$req['special'];
		$this->regDate=$req['regDate'];
		$this->accessDate=$req['accessDate'];
		$this->lastIP=$req['lastIP'];
		$this->gameVer=$req['gameVer'];
		$this->lvlsCompleted=$req['lvlsCompleted'];
		$this->blacklist=$req['blacklist'];
		$this->friendsCount=$req['friends_cnt'];
		$this->friendshipIds=$req['friendship_ids'];
		$reqd=json_decode($req->fetch_assoc()['vessels']);
		$this->iconType=$req['iconType'];
		$this->colorPrimary=$reqd['clr_primary'];
		$this->colorSecondary=$reqd['clr_secondary'];
		$this->cube=$reqd['cube'];
		$this->ship=$reqd['ship'];
		$this->ball=$reqd['ball'];
		$this->ufo=$reqd['ufo'];
		$this->wave=$reqd['wave'];
		$this->robot=$reqd['robot'];
		$this->spider=$reqd['spider'];
		$this->trace=$reqd['trace'];
		$this->death=$reqd['death'];
		$reqc=json_decode($req->fetch_assoc()['chests']);
		$this->chestSmallCount=$reqc['small_count'];
		$this->chestBigCount=$reqc['big_count'];
		$this->chestSmallTime=$reqc['small_time'];
		$this->chestBigTime=$reqc['big_time'];
		$reqm=json_decode($req['settings']);
		$this->frS=$reqm['frS'];
		$this->cS=$reqm['cS'];
		$this->mS=$reqm['mS'];
		$this->youtube=$reqm['youtube'];
		$this->twitch=$reqm['twitch'];
		$this->twitter=$reqm['twitter'];
	}

	function getUIDbyUname($uname, $autoSave=false){ //returns UID on success and -1 on failure
		$req=$this->db->preparedQuery("SELECT uid FROM users WHERE uname=?","s",$uname);
		if ($this->db->isEmpty($req)) return -1;
		$uid=$req->fetch_assoc()['uid'];
		if ($autoSave) $this->uid=$uid;
		return $uid;
	}

	function updateIP($ip){
		$this->lastIP=$ip;
		$this->db->preparedQuery("UPDATE users SET lastIP=? WHERE uid=?","si",$ip,$this->uid);
	}

	function countIPs($ip){
		return $this->db->preparedQuery("COUNT(*) FROM users WHERE lastIP=?","s",$ip)[0];
	}

	function updateBlacklist($action=CBLACKLIST_BLOCK, $uid){
		$this->loadSocial();
		$blacklist=explode(",",$this->blacklist);
		if($action==CBLACKLIST_BLOCK and !in_array($uid,$blacklist)) array_push($blacklist,$uid);
		if($action==CBLACKLIST_UNBLOCK and in_array($uid,$blacklist)){
			unset($blacklist[array_search($uid, $blacklist)]);
		}
		$this->blacklist=implode(",",$blacklist);
		$this->db->preparedQuery("UPDATE users SET blacklist=? WHERE uid=$this->uid","s",$this->blacklist);
	}

	function pushStats(){
		$this->db->preparedQuery("UPDATE users SET stars=?,diamonds=?,coins=?,ucoins=?,demons=?,cpoints=?,orbs=?,special=? WHERE uid=$this->uid",
		"iiiiiiii",$this->stars,$this->diamonds,$this->coins,$this->ucoins,$this->demons,$this->cpoints,$this->orbs,$this->special);
	}

	function updateAccessTime(){
		$this->db->query("UPDATE users SET accessDate=".date("d-m-Y H:i:s")." WHERE uid=$this->uid");
	}

	function banUser($action=CBAN_BAN){
		if ($action==CBAN_BAN) $ban=1;
		if ($action==CBAN_UNBAN) $ban=0;
		$this->isBanned=$ban;
		$this->db->query("UPDATE users SET isBanned=$ban WHERE uid=$this->uid");
	}

	function logIn($uname, $pass, $ip){ //returns UID on success and -1 on failure
		$uid=$this->getUIDbyUname($uname, true);
		if ($uid>0) {
			$this->loadAuth(CAUTH_UID);
			if($this->isBanned==1){
				return -12;
			}
			$pass = password_hash($pass);
			$this->updateIP($ip);
			if ($this->passhash == $pass) {
				return $uid;
			}
		}
			return -1;
	}

	function register($uname, $pass, $email, $ip){
		if($this->getUIDbyUname($uname)!=-1) return -2;
		$req=$this->db->preparedQuery("SELECT uid FROM users WHERE email=?","s",$email);
		if(!$this->db->isEmpty($req)) return -3;
		$pass=password_hash($pass);
		$q="INSERT INTO users (uname,passhash,email,regDate,accessDate) VALUES (?,?,?,?,?)";
		$date=date("d-m-Y H:i:s");
		$this->db->preparedQuery($q,"sssss",$uname,$pass,$email,$date,$date);
		if($this->getUIDbyUname($uname, true)==-1) return -1;
		$this->updateIP($ip);
		return 1;
	}
}
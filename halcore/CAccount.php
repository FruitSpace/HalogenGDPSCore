<?php

define("CAUTH_UID",17);
define("CAUTH_UNAME",26);
define("CAUTH_EMAIL",35);
define("CBAN_BAN", 44);
define("CBAN_UNBAN", 53);
define("CBLACKLIST_BLOCK", 62);
define("CBLACKLIST_UNBLOCK",71);
define("CFRIENDSHIP_ADD", 37);
define("CFRIENDSHIP_REMOVE", 38);

class CAccount{

	public $uid, $uname, $passhash, $email, $role_id, $isBanned;
	public $stars, $diamonds, $coins, $ucoins, $demons, $cpoints, $orbs, $special, $lvlsCompleted;
	public $regDate, $accessDate, $lastIP, $gameVer;
	public $blacklist, $friendsCount, $friendshipIds;
	public $iconType, $colorPrimary, $colorSecondary, $cube, $ship, $ball, $ufo, $wave, $robot, $spider, $trace, $death;
	public $chestSmallCount, $chestSmallTime, $chestBigCount, $chestBigTime;
	public $frS, $cS, $mS, $youtube, $twitch, $twitter;

	public $db; //! REMOVE DBM

	function __construct($db){
		$this->db=$db;
	}

	function countUsers(){
		return $this->db->query("SELECT count(*) as cnt FROM users")->fetch_assoc()['cnt'];
	}

	function exists($uid){
		$req=$this->db->query("SELECT uname FROM users WHERE uid=$uid");
		return !$this->db->isEmpty($req);
	}

	function loadSettings(){
		$req=$this->db->query("SELECT settings FROM users WHERE uid=$this->uid");
		$reqd=json_decode($req->fetch_assoc()['settings'],true);
		$this->frS=$reqd['frS'];
		$this->cS=$reqd['cS'];
		$this->mS=$reqd['mS'];
		$this->youtube=$reqd['youtube'];
		$this->twitch=$reqd['twitch'];
		$this->twitter=$reqd['twitter'];
	}

	function loadChests(){
		$req=$this->db->query("SELECT chests FROM users WHERE uid=$this->uid")->fetch_assoc();
		$reqd=json_decode($req['chests'],true);
		$this->chestSmallCount=$reqd['small_count'];
		$this->chestBigCount=$reqd['big_count'];
		$this->chestSmallTime=$reqd['small_time'];
		$this->chestBigTime=$reqd['big_time'];
	}

	function loadVessels(){
		$req=$this->db->query("SELECT iconType,vessels FROM users WHERE uid=$this->uid")->fetch_assoc();
		$reqd=json_decode($req['vessels'],true);
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
	}

	function loadStats(){
		$req=$this->db->query("SELECT stars,diamonds,coins,ucoins,demons,cpoints,orbs,special,lvlsCompleted FROM users WHERE uid=$this->uid")->fetch_assoc();
		$this->stars=$req['stars'];
		$this->diamonds=$req['diamonds'];
		$this->coins=$req['coins'];
		$this->ucoins=$req['ucoins'];
		$this->demons=$req['demons'];
		$this->cpoints=$req['cpoints'];
		$this->orbs=$req['orbs'];
		$this->special=$req['special'];
		$this->lvlsCompleted=$req['lvlsCompleted'];
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
				return 0;

		}
		$this->uid=$req['uid'];
		$this->uname=$req['uname'];
		$this->passhash=$req['passhash'];
		$this->email=$req['email'];
		$this->role_id=$req['role_id'];
		$this->isBanned=$req['isBanned'];
		return 1;
	}

	function loadTechnical(){
		$req=$this->db->query("SELECT regDate,accessDate,lastIP,gameVer FROM users WHERE uid=$this->uid")->fetch_assoc();
		$this->regDate=$req['regDate'];
		$this->accessDate=$req['accessDate'];
		$this->lastIP=$req['lastIP'];
		$this->gameVer=$req['gameVer'];
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
		$reqd=json_decode($req['vessels'],true);
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
		$reqc=json_decode($req['chests'],true);
		$this->chestSmallCount=$reqc['small_count'];
		$this->chestBigCount=$reqc['big_count'];
		$this->chestSmallTime=$reqc['small_time'];
		$this->chestBigTime=$reqc['big_time'];
		$reqm=json_decode($req['settings'],true);
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
		return $this->db->preparedQuery("SELECT count(*) as cnt FROM users WHERE lastIP=?","s",$ip)->fetch_assoc()['cnt'];
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

	function updateFriendships($action=CFRIENDSHIP_ADD, int $id){
		$this->loadSocial();
		$friendships=(empty($this->friendshipIds)?array():explode(",",$this->friendshipIds));
		if($action==CFRIENDSHIP_ADD and !in_array($id,$friendships)) {
			$this->friendsCount++;
			array_push($friendships, $id);
			$this->friendshipIds = implode(",", $friendships);
			$this->db->preparedQuery("UPDATE users SET friends_cnt=?, friendship_ids=? WHERE uid=$this->uid", "is",
				$this->friendsCount, $this->friendshipIds);
			return 1;
		}
		if($action==CFRIENDSHIP_REMOVE and !empty($friendships) and $this->friendsCount>0 and in_array($id,$friendships)){
			$this->friendsCount--;
			unset($friendships[array_search($id, $friendships)]);
			$this->friendshipIds=implode(",",$friendships);
			$this->db->preparedQuery("UPDATE users SET friends_cnt=?, friendship_ids=? WHERE uid=$this->uid","is",
			$this->friendsCount,$this->friendshipIds);
			return 1;
		}
		return -1;
	}

	function pushStats(){
		$this->db->preparedQuery("UPDATE users SET stars=?,diamonds=?,coins=?,ucoins=?,demons=?,cpoints=?,orbs=?,special=?,lvlsCompleted=? WHERE uid=$this->uid",
		"iiiiiiiii",$this->stars,$this->diamonds,$this->coins,$this->ucoins,$this->demons,$this->cpoints,$this->orbs,$this->special,$this->lvlsCompleted);
	}

	function pushSettings(){
		$settings=array(
			"frS"=>$this->frS,
			"cS"=>$this->cS,
			"mS"=>$this->mS,
			"youtube"=>$this->youtube,
			"twitch"=>$this->twitch,
			"twitter"=>$this->twitter
		);
		$settings=json_encode($settings);
		$this->db->preparedQuery("UPDATE users SET settings=? WHERE uid=$this->uid","s",$settings);
	}

	function getShownIcon(){
		switch($this->iconType){
			case 1:
				return $this->ship;
			case 2:
				return $this->ball;
			case 3:
				return $this->ufo;
			case 4:
				return $this->wave;
			case 5:
				return $this->robot;
			case 6:
				return $this->spider;
			case 0:
			default:
				return $this->cube;
		}
	}

	function getLeaderboardRank(){
		$req=$this->db->query("SELECT count(*) as cnt FROM users WHERE stars>= $this->stars AND isBanned=0");
		if($this->db->isEmpty($req)) return 0;
		return $req->fetch_assoc()['cnt']+1;
	}

	function updateRole($role_id){
		$this->role_id=$role_id;
		$this->db->preparedQuery("UPDATE users SET role_id=? WHERE uid=$this->uid","i",$role_id);
	}

	function pushVessels(){
		$vessels=array(
			'clr_primary'=>$this->colorPrimary,
			'clr_secondary'=>$this->colorSecondary,
			'cube'=>$this->cube,
			'ship'=>$this->ship,
			'ball'=>$this->ball,
			'ufo'=>$this->ufo,
			'wave'=>$this->wave,
			'robot'=>$this->robot,
			'spider'=>$this->spider,
			'trace'=>$this->trace,
			'death'=>$this->death
		);
		$vessels=json_encode($vessels);
		$this->db->preparedQuery("UPDATE users SET iconType=?,vessels=? WHERE uid=$this->uid","is",$this->iconType,$vessels);
	}

	function updateAccessTime(){
		$this->db->query("UPDATE users SET accessDate=".date("Y-m-d H:i:s")." WHERE uid=$this->uid");
	}

	function banUser($action=CBAN_BAN){
		if ($action==CBAN_BAN) $ban=1;
		if ($action==CBAN_UNBAN) $ban=0;
		$this->isBanned=$ban;
		$this->db->query("UPDATE users SET isBanned=$ban WHERE uid=$this->uid");
	}

	function logIn($uname, $pass, $ip, $uid=null){ //returns UID on success and -1 on failure
		$uid=($uid==null?$this->getUIDbyUname($uname, true):(int)$uid);
		$this->uid=$uid;
		if ($uid>0) {
			$this->loadAuth(CAUTH_UID);
			if($this->isBanned==1){
				return -12;
			}
			$pass=md5(md5($pass."HalogenCore1704")."ae07").substr(md5($pass),0,4);
			if ($this->passhash == $pass) {
				$this->updateIP($ip);
				return $uid;
			}
		}
			return -1;
	}

	function register($uname, $pass, $email, $ip){
		if(strlen($uname)>16) return -1;
		if($this->getUIDbyUname($uname)!=-1) return -2;
		$req=$this->db->preparedQuery("SELECT uid FROM users WHERE email=?","s",$email);
		if(!$this->db->isEmpty($req)) return -3;
		$pass=md5(md5($pass."HalogenCore1704")."ae07").substr(md5($pass),0,4);
		$q="INSERT INTO users (uname,passhash,email,regDate,accessDate) VALUES (?,?,?,?,?)";
		$date=date("Y-m-d H:i:s");
		$this->db->preparedQuery($q,"sssss",$uname,$pass,$email,$date,$date);
		if($this->getUIDbyUname($uname, true)==-1) return -1;
		$this->updateIP($ip);
		return 1;
	}
}
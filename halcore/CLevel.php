<?php

class CLevel{
	public $db; //REMOVE DBM!!

	public $id, $name, $description, $uid, $password, $version, $length, $difficulty, $demonDifficulty;
	public $track_id, $song_id, $versionGame, $versionBinary, $stringExtra, $stringLevel, $stringLevelInfo, $origId;
	public $objects, $starsRequested, $starsGot, $ucoins, $coins, $downloads, $likes, $reports;
	public $is2p, $isVerified, $isFeatured, $isHall, $isEpic, $isUnlisted, $isLDM;
	public $uploadDate, $updateDate;

	function __construct($db){
		$this->db=$db;
	}

	function exists(int $lvlId){
		$req=$this->db->query("SELECT uid FROM levels WHERE id=$lvlId");
		return !$this->db->isEmpty($req);
	}

	function loadParams(){
		$req=$this->db->query("SELECT is2p, isVerified, isFeatured, isHall, isEpic, isUnlisted, isLDM FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->is2p=$req['is2p'];
		$this->isVerified=$req['isVerified'];
		$this->isFeatured=$req['isFeatured'];
		$this->isHall=$req['isHall'];
		$this->isEpic=$req['isEpic'];
		$this->isUnlisted=$req['isUnlisted'];
		$this->isLDM=$req['isLDM'];
	}

	function pushParams(){
		$this->db->query("UPDATE levels SET is2p=$this->is2p,isVerified=$this->isVerified,isFeatured=$this->isFeatured,isHall=$this->isHall,isEpic=$this->isEpic,isUnlisted=$this->isUnlisted,isLDM=$this->isLDM WHERE id=$this->id");

	}

	function loadDates(){
		$req=$this->db->query("SELECT uploadDate, updateDate FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->updateDate=$req['updateDate'];
		$this->uploadDate=$req['uploadDate'];
	}

	function loadLevel(){
		$req=$this->db->query("SELECT track_id, song_id,versionGame,versionBinary,stringExtra,stringLevel,stringLevelInfo, original_id FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->track_id=$req['track_id'];
		$this->song_id=$req['song_id'];
		$this->versionGame=$req['versionGame'];
		$this->versionBinary=$req['versionBinary'];
		$this->stringExtra=$req['stringExtra'];
		$this->stringLevel=$req['stringLevel'];
		$this->stringLevelInfo=$req['stringLevelInfo'];
		$this->origId=$req['original_id'];
	}

	function loadStats(){
		$req=$this->db->query("SELECT objects,starsRequested,starsGot,ucoins,coins,downloads,likes,reports FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->objects=$req['objects'];
		$this->starsRequested=$req['starsRequested'];
		$this->starsGot=$req['starsGot'];
		$this->ucoins=$req['ucoins'];
		$this->coins=$req['coins'];
		$this->downloads=$req['downloads'];
		$this->likes=$req['likes'];
		$this->reports=$req['reports'];
	}

	function loadMain(){
		$req=$this->db->query("SELECT name,description,uid,password,versionBinary,length,difficulty,demonDifficulty FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->name=$req['name'];
		$this->description=$req['description'];
		$this->uid=$req['uid'];
		$this->password=$req['password'];
		$this->version=$req['version'];
		$this->length=$req['length'];
		$this->difficulty=['difficulty'];
		$this->demonDifficulty=$req['demonDifficulty'];
	}

	function LoadAll(){
		$req=$this->db->query("SELECT * FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->name=$req['name'];
		$this->description=$req['description'];
		$this->uid=$req['uid'];
		$this->password=$req['password'];
		$this->version=$req['version'];
		$this->length=$req['length'];
		$this->difficulty=['difficulty'];
		$this->demonDifficulty=$req['demonDifficulty'];
		$this->track_id=$req['track_id'];
		$this->song_id=$req['song_id'];
		$this->versionGame=$req['versionGame'];
		$this->versionBinary=$req['versionBinary'];
		$this->stringExtra=$req['stringExtra'];
		$this->stringLevel=$req['stringLevel'];
		$this->stringLevelInfo=$req['stringLevelInfo'];
		$this->origId=$req['original_id'];
		$this->objects=$req['objects'];
		$this->starsRequested=$req['starsRequested'];
		$this->starsGot=$req['starsGot'];
		$this->ucoins=$req['ucoins'];
		$this->coins=$req['coins'];
		$this->downloads=$req['downloads'];
		$this->likes=$req['likes'];
		$this->reports=$req['reports'];
		$this->is2p=$req['is2p'];
		$this->isVerified=$req['isVerified'];
		$this->isFeatured=$req['isFeatured'];
		$this->isHall=$req['isHall'];
		$this->isEpic=$req['isEpic'];
		$this->isUnlisted=$req['isUnlisted'];
		$this->isLDM=$req['isLDM'];
		$this->updateDate=$req['updateDate'];
		$this->uploadDate=$req['uploadDate'];
	}

	function loadBase(){
		$req=$this->db->query("SELECT uid,name,version FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->uid=$req['uid'];
		$this->name=$req['name'];
		$this->version=$req['version'];
	} //name, uid, version

	function isOwnedBy(int $uid){
		if(!$this->exists($this->id)) return -1;
		$this->loadBase();
		return $uid==$this->uid;
	}

	function checkParams(){
		if(strlen($this->name)>32  or strlen($this->description)>256
			or $this->password>99999999 or $this->version<1
			or $this->version>127 or $this->length<0
			or $this->length>4 or $this->track_id<0
			or $this->song_id<0 or $this->versionGame<0
			or $this->versionBinary<0 or strlen($this->stringLevel)<16
			or $this->origId<0 or $this->objects<100
			or $this->starsRequested<0 or $this->starsRequested>10
			or $this->ucoins<0 or $this->ucoins>3
		) return 0;
		$this->is2p=(empty($this->is2p)?0:1);
		$this->isVerified=(empty($this->isVerified)?0:1);
		$this->isFeatured=(empty($this->isFeatured)?0:1);
		$this->isHall=(empty($this->isHall)?0:1);
		$this->isEpic=(empty($this->isEpic)?0:1);
		$this->isUnlisted=(empty($this->isUnlisted)?0:1);
		$this->isLDM=(empty($this->isLDM)?0:1);
		return 1;
	}

	function deleteLevel(){
		$this->db->query("DELETE FROM levels WHERE id=$this->id");
	}

	function uploadLevel(){
		if(!$this->checkParams()) return -1;
		$query="INSERT INTO levels (name, description, uid, password, version, length, track_id, song_id, versionGame, versionBinary, stringExtra, stringLevel, stringLevelInfo, original_id, objects, starsRequested, ucoins, is2p, isVerified, isUnlisted, isLDM, uploadDate, updateDate) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->db->preparedQuery($query,"ssiiiiiiiisssiiiiiiiiii",$this->name,$this->description,$this->uid,$this->password,$this->version,$this->length,$this->track_id,$this->song_id,$this->versionGame,$this->versionBinary,$this->stringExtra,$this->stringLevel,$this->stringLevelInfo,$this->origId,$this->objects,
		$this->starsRequested,$this->ucoins,$this->is2p,$this->isVerified,$this->isUnlisted,$this->isLDM,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"));
		return $this->db->getDB()->insert_id;
	}
}
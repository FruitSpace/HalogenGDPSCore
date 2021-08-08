<?php

class CLevel{
	public DBManagement $db; //REMOVE DBM!!

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

	function deleteLevel(){
		$this->db->query("DELETE FROM levels WHERE id=$this->id");
	}
}
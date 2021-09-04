<?php


define("CLEVEL_ACTION_LIKE",300);
define("CLEVEL_ACTION_DISLIKE",301);
class CLevel{
	public $db; //REMOVE DBM!!

	public $id, $name, $description, $uid, $password, $version, $length, $difficulty, $demonDifficulty;
	public $suggestDifficulty, $suggestDifficultyCnt; //fetched with Main
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

	function countLevels(){
		return $this->db->query("SELECT count(*) as cnt FROM levels")->fetch_assoc()['cnt'];
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

	function loadLevel(bool $level=true){
		$req=$this->db->query("SELECT track_id, song_id,versionGame,versionBinary,".($level?"stringExtra,stringLevel,stringLevelInfo,":"")."original_id FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->track_id=$req['track_id'];
		$this->song_id=$req['song_id'];
		$this->versionGame=$req['versionGame'];
		$this->versionBinary=$req['versionBinary'];
		if($level) {
			$this->stringExtra = $req['stringExtra'];
			$this->stringLevel = $req['stringLevel'];
			$this->stringLevelInfo = $req['stringLevelInfo'];
		}
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

	function onDownloadLevel(){
		$this->db->query("UPDATE levels SET downloads=downloads+1 WHERE id=$this->id");
	}

	function loadMain(){
		$req=$this->db->query("SELECT name,description,uid,password,version,length,difficulty,demonDifficulty,suggestDifficulty,suggestDifficultyCnt FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->name=$req['name'];
		$this->description=$req['description'];
		$this->uid=$req['uid'];
		$this->password=$req['password'];
		$this->version=$req['version'];
		$this->length=$req['length'];
		$this->difficulty=$req['difficulty'];
		$this->demonDifficulty=$req['demonDifficulty'];
		$this->suggestDifficulty=$req['suggestDifficulty'];
		$this->suggestDifficultyCnt=$req['suggestDifficultyCnt'];
	}

	function loadAll(){
		$req=$this->db->query("SELECT * FROM levels WHERE id=$this->id")->fetch_assoc();
		$this->name=$req['name'];
		$this->description=$req['description'];
		$this->uid=$req['uid'];
		$this->password=$req['password'];
		$this->version=$req['version'];
		$this->length=$req['length'];
		$this->difficulty=$req['difficulty'];
		$this->demonDifficulty=$req['demonDifficulty'];
		$this->suggestDifficulty=$req['suggestDifficulty'];
		$this->suggestDifficultyCnt=$req['suggestDifficultyCnt'];
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
		$this->db->preparedQuery($query,"ssiiiiiiiisssiiiiiiiiss",$this->name,$this->description,$this->uid,$this->password,$this->version,$this->length,$this->track_id,$this->song_id,$this->versionGame,$this->versionBinary,$this->stringExtra,$this->stringLevel,$this->stringLevelInfo,$this->origId,$this->objects,
		$this->starsRequested,$this->ucoins,$this->is2p,$this->isVerified,$this->isUnlisted,$this->isLDM,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"));
		return $this->db->getDB()->insert_id;
	}

	function updateLevel(){
		if(!$this->checkParams()) return -1;
		$query="UPDATE levels SET name=?, description=?, password=?, version=?, length=?, track_id=?, song_id=?, versionGame=?, versionBinary=?, stringExtra=?, stringLevel=?, stringLevelInfo=?, original_id=?, objects=?, starsRequested=?, ucoins=?, is2p=?, isVerified=?, isUnlisted=?, isLDM=?, updateDate=? WHERE id=$this->id";
		$this->db->preparedQuery($query,"ssiiiiiiisssiiiiiiiis",$this->name,$this->description,$this->password,$this->version,$this->length,$this->track_id,$this->song_id,$this->versionGame,$this->versionBinary,$this->stringExtra,$this->stringLevel,$this->stringLevelInfo,$this->origId,$this->objects,
			$this->starsRequested,$this->ucoins,$this->is2p,$this->isVerified,$this->isUnlisted,$this->isLDM,date("Y-m-d H:i:s"));
		return $this->id;
	}

	function updateDescription($desc){
		if(strlen($this->description)>256) return -1;
		$this->db->preparedQuery("UPDATE levels SET description=? WHERE id=$this->id","s",$desc);
		return 1;
	}

	function doSuggestDifficulty($difficulty){
		$this->suggestDifficulty=($this->suggestDifficulty*$this->suggestDifficultyCnt+$difficulty)/($this->suggestDifficultyCnt+1);
		$this->suggestDifficultyCnt++;
		$this->db->preparedQuery("UPDATE levels SET suggestDifficulty=?,suggestDifficultyCnt=? WHERE id=$this->id","di",$this->suggestDifficulty,$this->suggestDifficultyCnt);
	}

	function rateLevel(int $stars){
		$this->starsGot=$stars;
		$postfix=",demonDifficulty=-1";
		switch ($stars){
			case 1:
				$diff=-1; //Auto
				break;
			case 2:
				$diff=10; //Easy
				break;
			case 3:
				$diff=20; //Normal
				break;
			case 4:
			case 5:
				$diff=30; //Hard
				break;
			case 6:
			case 7:
				$diff=40; //Harder
				break;
			case 8:
			case 9:
				$diff=50; //Insane
				break;
			case 10:
				$diff=50; //Demon
				$postfix=",demonDifficulty=3";
				break;
			default:
				$diff=0; //unspecified
		}
		$this->db->query("UPDATE levels SET difficulty=$diff,starsGot=$stars".$postfix." WHERE id=$this->id");
		$this->recalculateCPoints($this->uid);
	}

	function rateDemon(int $diff){
		$this->db->query("UPDATE levels SET demonDifficulty=$diff WHERE id=$this->id");
	}

	function featureLevel(bool $feature=false){
		$this->db->query("UPDATE levels SET isFeatured=".($feature?"1":"0")." WHERE id=$this->id");
		$this->recalculateCPoints($this->uid);
	}

	function epicLevel(bool $epic=false){
		$this->db->query("UPDATE levels SET isEpic=".($epic?"1":"0")." WHERE id=$this->id");
		$this->recalculateCPoints($this->uid);
	}

	function likeLevel(int $lvl_id, int $uid, int $action=CLEVEL_ACTION_LIKE){
		require_once __DIR__."/lib/actions.php";
		if(isLiked(ITEMTYPE_LEVEL,$uid,$lvl_id,$this->db)) return -1;
		$this->db->query("UPDATE levels SET likes=likes".($action==CLEVEL_ACTION_DISLIKE?"-":"+")."1 WHERE id=$lvl_id");
		registerAction(ACTION_LEVEL_LIKE,$uid,$lvl_id,array("type"=>($action==CLEVEL_ACTION_DISLIKE?"Dislike":"Like")),$this->db);
	}

	function verifyCoins(bool $verify=false){
		$this->db->query("UPDATE levels SET coins=".($verify?"ucoins":"0")." WHERE id=$this->id");
	}

	function reportLevel(){
		$this->db->query("UPDATE levels SET reports=reports+1 WHERE id=$this->id");
	}

	function recalculateCPoints(int $uid){
		$req=$this->db->query("SELECT starsGot,isFeatured,isEpic FROM levels WHERE uid=$uid");
		if($this->db->isEmpty($req)) return -2;
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$cpoints=0;
		foreach ($reqm as $sreq){
			if($sreq['starsGot']>0) $cpoints++;
			if($sreq['isFeatured']==1) $cpoints++;
			if($sreq['isEpic']==1) $cpoints++;
		}
		$this->db->query("UPDATE users SET cpoints=$cpoints WHERE uid=$uid");
	}
}
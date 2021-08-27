<?php

define("QUEST_TYPE_DAILY",200);
define("QUEST_TYPE_WEEKLY",201);
define("QUEST_TYPE_CHALLENGE",202);

class CQuests{
	public $db;

	function __construct($db){
		$this->db=$db;
	}

	function exists(int $type=QUEST_TYPE_DAILY){
		$type="=".($type-200);
		if($type=2) $type=">1";
		return $this->db->query("SELECT count(*) as cnt FROM quests WHERE type$type")->fetch_assoc()['cnt']>0;
	}

	function getDaily(){
		$req = $this->db->query("SELECT id, lvl_id FROM quests WHERE type=0 AND timeExpire<now() ORDER BY timeExpire DESC LIMIT 1");
		return $req->fetch_assoc();
	}

	function getWeekly(){
		$req = $this->db->query("SELECT id, lvl_id FROM quests WHERE type=1 AND timeExpire<now() ORDER BY timeExpire DESC LIMIT 1");
		return $req->fetch_assoc();
	}

	function publishDaily(int $lvl_id){
		$this->db->query("INSERT INTO quests (type,lvl_id) VALUES (0,$lvl_id)");
		return $this->db->getDB()->insert_id;
	}

	function publishWeekly(int $lvl_id){
		$this->db->query("INSERT INTO quests (type,lvl_id) VALUES (1,$lvl_id)");
		return $this->db->getDB()->insert_id;
	}

	function publishQuest(int $type, int $needed, int $reward, $name){
		$type=$type-200;
		if(strlen($name)>64) return -1;
		$this->db->preparedQuery("INSERT INTO quests (type,needed,reward,name,timeExpire) VALUES (?,?,?,?,?)","iiiss",$type,$needed,$reward,$name,date("Y-m-d H:i:s"));
		return $this->db->getDB()->insert_id;
	}

	function getQuests(){
		$req=$this->db->query("SELECT r1.id,type,needed,reward,name,timeExpire FROM quests AS r1
    		JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM quests)) AS id) AS r2
 			WHERE r1.id >= r2.id AND r1.timeExpire<now() AND r1.type>1 ORDER BY r1.id ASC LIMIT 3");
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		while(count($reqm)<3) array_push($reqm, $reqm[0]);
		$quests="";
		foreach($reqm as $sreq) {
			$quests.=$sreq['id'].",".((int)$sreq['type']-1).",".$sreq['needed'].",".$sreq['reward'].",".$sreq['name'].":";
		}
		return substr($quests,0,-1);
	}

	function getDailyLevel(bool $weekly){
		if($weekly){
			$timeLeft=strtotime("next week midnight")-time();
			$lvl_id=100001; //Why the fuck robtop did this?
		}else{
			$timeLeft=strtotime("tomorrow midnight")-time();
			$lvl_id=0;
		}
		$req=$this->db->query("SELECT id,lvl_id FROM quests WHERE type=".($weekly?"1":"0")." AND timeExpire<now() ORDER BY timeExpire DESC LIMIT 1");
		if($this->db->isEmpty($req)) return "-2";
		$sreq=$req->fetch_assoc();
		return $lvl_id+$sreq['lvl_id']."|$timeLeft";
	}
}

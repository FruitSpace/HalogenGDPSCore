<?php

define("QUEST_TYPE_DAILY",200);
define("QUEST_TYPE_WEEKLY",201);
define("QUEST_TYPE_ORBS",202);
define("QUEST_TYPE_COINS",203);
define("QUEST_TYPE_STARS",204);

class CQuests{
	public $db;

	function __construct($db){
		$this->db=$db;
	}

	function exists(int $type=QUEST_TYPE_DAILY){
		$type=$type-200;
		return $this->db->query("SELECT count(*) as cnt FROM quests WHERE type=$type")->fetch_assoc()['cnt']>0;
	}

	function getDaily(){
		$req = $this->db->preparedQuery("SELECT id, lvl_id FROM quests WHERE type=0 AND timeExpire<? ORDER BY timeExpire DESC LIMIT 1","s",date("Y-m-d H:i:s"));
		return $req->fetch_assoc();
	}

	function getWeekly(){
		$req = $this->db->preparedQuery("SELECT id, lvl_id FROM quests WHERE type=1 AND timeExpire<? ORDER BY timeExpire DESC LIMIT 1","s",date("Y-m-d H:i:s"));
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

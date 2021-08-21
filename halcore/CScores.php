<?php

class CScores{
	public $db; //!  Remove dbm

	public $id, $uid, $lvl_id, $postedTime, $percent, $attempts, $coins;

	function __construct($db){
		$this->db=$db;
	}

	function scoreExistsByUid(int $uid, int $lvl_id){
		return $this->db->query("SELECT count(*) as cnt FROM scores WHERE uid=$uid AND lvl_id=$lvl_id")->fetch_assoc()['cnt']>0;
	}

	function loadScoreById(){
		$req=$this->db->query("SELECT * FROM scores WHERE id=$this->id");
		if($this->db->isEmpty($req)) return 0;
		$req=$req->fetch_assoc();
		$this->uid=$req['uid'];
		$this->lvl_id=$req['lvl_id'];
		$this->postedTime=$req['postedTime'];
		$this->percent=$req['percent'];
		$this->attempts=$req['attempts'];
		$this->coins=$req['coins'];
		return 1;
	}

	function getScoresForLevelId(int $lvl_id){
		$req=$this->db->query("SELECT * FROM scores WHERE lvl_id=$lvl_id ORDER BY percent DESC");
		if($this->db->isEmpty($req)) return array();
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$scores=array();
		foreach ($reqm as $sreq){
			$item=array();
			$item['uid']=$sreq['uid'];
			$item['percent']=$sreq['percent'];
			$item['ranking']=((int)$sreq['percent']==100?1:((int)$sreq['percent']>75?2:3));
			$item['coins']=$sreq['coins'];
			$item['date']=$sreq['postedTime'];
			array_push($scores,$item);
		}
		return $scores;
	}

	function updateLevelScore(){
		$this->db->preparedQuery("UPDATE scores SET postedTime=?,percent=?,attempts=?,coins=? WHERE lvl_id=? AND uid=?",
		"siiiii",date("Y-m-d H:i:s"),$this->percent,$this->attempts,$this->coins,$this->lvl_id,$this->uid);
	}

	function uploadLevelScore(){
		$this->db->preparedQuery("INSERT INTO scores (uid, lvl_id, postedTime, percent, attempts, coins) VALUES (?,?,?,?,?,?)",
			"iisiii",$this->uid,$this->lvl_id,date("Y-m-d H:i:s"),$this->percent,$this->attempts,$this->coins);
	}
}
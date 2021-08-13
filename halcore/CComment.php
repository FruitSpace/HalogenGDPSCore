<?php

class CComment{
	public $db;
	public $likes, $id, $uid, $lvl_id, $percent, $isSpam;
	public $postedDate, $comment;

	function __construct($db){
		$this->db=$db;
	}

	function countAccComments($uid=null){
		$postfix=($uid==null?"":"WHERE uid=".(int)$uid);
		return $this->db->query("SELECT count(*) as cnt FROM acccomments $postfix")->fetch_assoc()['cnt'];
	}

	function loadAccComment(){
		$req=$this->db->query("SELECT uid,comment,postedTime,likes,isSpam FROM acccomments WHERE id=$this->id")->fetch_assoc();
		$this->uid=$req['uid'];
		$this->comment=$req['comment'];
		$this->postedDate=$req['postedTime'];
		$this->likes=$req['likes'];
		$this->isSpam=$req['isSpam'];
	}

	function getAllAccComments(int $uid, int $page){
		$page=$page*10;
		$req=$this->db->query("SELECT id,comment,postedTime,likes,isSpam FROM acccomments WHERE uid=$uid ORDER BY postedTime DESC LIMIT 10 OFFSET $page");
		if($this->db->isEmpty($req)) return array();
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$acc=array();
		foreach($reqm as $sreq){
			$ccObj= new CComment($this->db);
			$ccObj->uid=$uid;
			$ccObj->id=$sreq['id'];
			$ccObj->comment=$sreq['comment'];
			$ccObj->postedDate=$sreq['postedTime'];
			$ccObj->likes=$sreq['likes'];
			$ccObj->isSpam=$sreq['isSpam'];
			array_push($acc,$ccObj);
		}
		return $acc;
	}

	function loadLvlComment(){
		$req=$this->db->query("SELECT uid,lvl_id,comment,postedTime,likes,isSpam,percent FROM comments WHERE id=$this->id")->fetch_assoc();
		$this->uid=$req['uid'];
		$this->lvl_id=$req['lvl_id'];
		$this->comment=$req['comment'];
		$this->postedDate=$req['postedTime'];
		$this->likes=$req['likes'];
		$this->isSpam=$req['isSpam'];
		$this->percent=$req['percent'];
	}

	function getAllLvlComments($lvl_id){
		$req=$this->db->preparedQuery("SELECT id,uid,comment,postedTime,likes,isSpam,percent FROM comments WHERE lvl_id=?","i",$lvl_id);
		if($this->db->isEmpty($req)) return array();
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$lvl=array();
		foreach($reqm as $sreq){
			$ccObj= new CComment($this->db);
			$ccObj->lvl_id=$this->lvl_id;
			$ccObj->uid=$sreq['uid'];
			$ccObj->id=$sreq['id'];
			$ccObj->comment=$sreq['comment'];
			$ccObj->postedDate=$sreq['postedTime'];
			$ccObj->likes=$sreq['likes'];
			$ccObj->isSpam=$sreq['isSpam'];
			$ccObj->percent=$sreq['percent'];
			array_push($lvl,$ccObj);
		}
		return $lvl;
	}

	function postAccComment(){
		if(strlen($this->comment)>128) return -1;
		$this->db->preparedQuery("INSERT INTO acccomments (uid,comment,postedTime) VALUES (?,?,?)","iss",
		$this->uid,$this->comment,date("Y-m-d H:i:s"));
		return 1;
	}

	function postLvlComment(){
		if(strlen($this->comment)>128) return -1;
		$this->db->preparedQuery("INSERT INTO comments (uid,lvl_id,comment,postedTime,percent) VALUES (?,?,?,?,?)",
		"iissi",$this->uid,$this->lvl_id,$this->comment,date("Y-m-d H:i:s"),$this->percent);
	}

	function deleteAccComment($id=null, $uid=null){
		$id=($id==null?$this->id:(int)$id);
		$uid=($uid==null?$this->uid:(int)$uid);
		$this->db->query("DELETE FROM acccomments WHERE id=$id AND $uid=$uid");
	}

	function deleteLvlComment(){
		$this->db->query("DELETE FROM comments WHERE id=$this->id");
	}

	function clean(){
		unset($this->id);
		unset($this->likes);
		unset($this->uid);
		unset($this->lvl_id);
		unset($this->percent);
		unset($this->isSpam);
		unset($this->postedDate);
		unset($this->comment);
	}
}
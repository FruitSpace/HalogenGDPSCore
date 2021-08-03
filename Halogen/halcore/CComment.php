<?php

define("CCOMMENT_TYPE_LEVEL", 3);
define("CCOMMENT_TYPE_USER", 2);

class CComment{
	public int $commentType;
	public DBManagement $db;
	public int $likes, $id, $uid, $lvl_id, $percent, $isSpam;
	public $postedDate, $comment;

	function __construct($commentType, $db){
		$this->commentType=($commentType==CCOMMENT_TYPE_LEVEL?CCOMMENT_TYPE_LEVEL:CCOMMENT_TYPE_USER);
		$this->db=$db;
	}

	function loadAccComment(){
		$req=$this->db->query("SELECT uid,comment,postedTime,likes,isSpam FROM acccomments WHERE id=$this->id")->fetch_assoc();
		$this->uid=$req['uid'];
		$this->comment=$req['comment'];
		$this->postedDate=$req['postedTime'];
		$this->likes=$req['likes'];
		$this->isSpam=$req['isSpam'];
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

	function postAccComment(){
		$this->db->preparedQuery("INSERT INTO acccomments (uid,comment,postedTime) VALUES (?,?,?)","iss",
		$this->uid,$this->comment,date("d-m-Y H:i:s"));
	}

	function postLvlComment(){
		$this->db->preparedQuery("INSERT INTO comments (uid,lvl_id,comment,postedTime,percent) VALUES (?,?,?,?,?)",
		"iissi",$this->uid,$this->lvl_id,$this->comment,date("d-m-Y H:i:s"),$this->percent);
	}
}
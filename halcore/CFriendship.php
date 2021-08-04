<?php

class CFriendship{
	public DBManagement $db;

	function __construct($db){
		$this->db=$db;
	}

	function readFriendRequest(int $id){
		$this->db->query("UPDATE friendreqs SET isNew=0");
	}

	function requestFriend(int $uid, int $uid_dest, $comment=null){
		$comment=($comment==null?'':$comment);
		$this->db->preparedQuery("INSERT INTO friendreqs (uid_src, uid_dest, uploadDate, comment) VALUES (?,?,?,?)",
		"iiss",$uid,$uid_dest,date("d-m-Y H:i:s"),$comment);
	}

	function acceptFriendRequest(int $id){
		$req=$this->db->query("SELECT uid_src, uid_dest FROM friendreqs WHERE id=$id")->fetch_assoc();
	}
}
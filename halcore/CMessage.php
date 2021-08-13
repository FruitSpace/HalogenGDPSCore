<?php

class CMessage{
	public $db; //! Remove DBM

	public $id, $uid_src, $uid_dest, $subject, $message, $postedtime, $isNew;

	function __construct($db){
		$this->db=$db;
	}

	function exists(int $id){
		$req=$this->db->query("SELECT uid_src FROM messages WHERE id=$id");
		return !$this->db->isEmpty($req);
	}

	function countMessages(int $uid, bool $new=false){
		return $this->db->query("SELECT count(*) as cnt FROM messages WHERE uid_dest=$uid".($new?" AND isNew=1":""))->fetch_assoc()['cnt'];
	}

	function loadMessageById(int $id=0){
		$id=($id==0?$this->id:$id);
		$req=$this->db->query("SELECT * FROM messages WHERE id=$id")->fetch_assoc();
		$this->id=$id;
		$this->uid_src=$req['uid_src'];
		$this->uid_dest=$req['uid_dest'];
		$this->subject=$req['subject'];
		$this->message=$req['body'];
		$this->postedtime=$req['postedTime'];
		$this->isNew=$req['isNew'];
		$this->db->query("UPDATE messages SET isNew=0 WHERE id=$id");
	}

	function deleteMessage(int $uid){
		$this->db->query("DELETE FROM messages WHERE id=$this->id AND (uid_src=$uid OR uid_dest=$uid)");
		return 1;
	}

	function sendMessageObj(){
		if(strlen($this->subject)>256 or strlen($this->message)>1024) return -1;
		require_once __DIR__."/CAccount.php";
		$acc=new CAccount($this->db);
		$acc->uid=$this->uid_dest;
		$acc->loadSettings();
		if($acc->mS==2) return -1;
		$acc->loadSocial();
		$blacklist=explode(",",$acc->blacklist);
		if(in_array($this->uid_src, $blacklist)) return -1;
		if($acc->mS==1){
			require_once __DIR__."/CFriendship.php";
			$cf=new CFriendship($this->db);
			if(!$cf->isAlreadyFriend($this->uid_src, $this->uid_dest)) return -1;
		}
		$this->db->preparedQuery("INSERT INTO messages (uid_src, uid_dest, subject, body, postedTime) VALUES (?,?,?,?,?)",
		"iisss",$this->uid_src,$this->uid_dest,$this->subject,date("Y-m-d H:i:s"));
		return 1;
	}

	function getMessagesForUid(int $uid, int $page, bool $sent=false){
		require_once __DIR__."/CAccount.php";
		$cnt=$this->db->query("SELECT count(*) as cnt FROM messages WHERE ".($sent?"uid_src":"uid_dest")."=$uid")->fetch_assoc()['cnt'];
		if($cnt==0) return -2;
		$req=$this->db->query("SELECT * FROM messages WHERE ".($sent?"uid_src":"uid_dest")."=$uid ORDER BY id LIMIT 10 OFFSET $page");
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$output=array('cnt'=>$cnt);
		foreach($reqm as $msg){
			$item=array();
			$item['id']=$msg['id'];
			$item['subject']=$msg['subject'];
			$item['message']=$msg['body'];
			$acc=new CAccount($this->db);
			$acc->uid=($sent?$msg['uid_dest']:$msg['uid_src']);
			$acc->loadAuth(); //Get uname
			$item['uid']=$acc->uid;
			$item['uname']=$acc->uname;
			$item['isNew']=$msg['isNew'];
			$item['date']=$msg['postedTime'];
			array_push($output, $item);
		}
		return $output;
	}
}
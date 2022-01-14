<?php

class CFriendship{
	public $db;

	function __construct($db){
		$this->db=$db;
	}

	function isAlreadyFriend(int $uid_dest, int $uid){
		$req=$this->db->query("SELECT count(*) as cnt FROM friendships WHERE (uid1=$uid AND uid2=$uid_dest) OR (uid2=$uid AND uid1=$uid_dest)")->fetch_assoc();
		if($req['cnt']>0) return 1;
		return 0;
	}

    function isAlreadySentFriend(int $uid_dest, int $uid){
        $req=$this->db->query("SELECT count(*) as cnt FROM friendreqs WHERE uid_src=$uid AND uid_dest=$uid_dest")->fetch_assoc();
        if($req['cnt']>0) return 1;
        return 0;
    }

	function countFriendRequests(int $uid, bool $new=false){
		return $this->db->query("SELECT count(*) as cnt FROM friendreqs WHERE uid_dest=$uid".($new?" AND isNew=1":""))->fetch_assoc()['cnt'];
	}

	function getFriendRequests(int $uid, int $page, bool $sent=false){
		require_once __DIR__."/CAccount.php";
		$cnt=$this->db->query("SELECT count(*) as cnt FROM friendreqs WHERE ".($sent?"uid_src":"uid_dest")."=$uid")->fetch_assoc()['cnt'];
		if($cnt==0) return -2;
		$req=$this->db->query("SELECT * FROM friendreqs WHERE ".($sent?"uid_src":"uid_dest")."=$uid LIMIT 10 OFFSET $page");
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$output=array('cnt'=>$cnt);
		foreach ($reqm as $frq){
			$item=array();
			$item['id']=$frq['id'];
			$item['comment']=$frq['comment'];
			$acc=new CAccount($this->db);
			$acc->uid=($sent?$frq['uid_dest']:$frq['uid_src']);
			$item['uid']=$acc->uid;
			$acc->loadAuth(); //Get uname
			$item['uname']=$acc->uname;
			$item['isNew']=$frq['isNew'];
			$acc->loadStats(); //Get Glow/Special
			$item['special']=$acc->special; //! MAY REDUCE PERFORMANCE. MAY REPLACE WITH CONSTANT ZERO
			$acc->loadVessels(); //Get icons and colors
			$item['iconType']=$acc->iconType;
			$item['clr_primary']=$acc->colorPrimary;
			$item['clr_secondary']=$acc->colorSecondary;
			$item['iconId']=$acc->getShownIcon();
			$item['date']=$frq['uploadDate'];
			//uname,uid,iconId,clr_primary,clr_secodary,iconType,special,id,comment,date,isNew
			array_push($output,$item);
		}
		return $output;
	}

	function getFriendRequestsCount(int $uid, bool $sent=false){
		return $this->db->query("SELECT count(*) as cnt FROM friendreqs WHERE ".($sent?"uid_src":"uid_dest")."=$uid")->fetch_assoc()['cnt'];
	}

	function deleteFriendship(int $uid, int $uid_dest){
		require_once __DIR__ . "/CAccount.php";
		$id=$this->getFriendshipId($uid, $uid_dest);
		$this->db->query("DELETE FROM friendships WHERE (uid1=$uid AND uid2=$uid_dest) OR (uid2=$uid AND uid1=$uid_dest)");
		$acc1=new CAccount($this->db);
		$acc2=new CAccount($this->db);
		$acc1->updateFriendships(CFRIENDSHIP_REMOVE, $id);
		$acc2->updateFriendships(CFRIENDSHIP_REMOVE, $id);

	}

	function getFriendshipId(int $uid, int $uid_dest){
		$req=$this->db->query("SELECT id FROM friendships WHERE (uid1=$uid AND uid2=$uid_dest) OR (uid2=$uid AND uid1=$uid_dest)");
		if($this->db->isEmpty($req)) return -1;
		if($req->num_rows>1){
			require_once __DIR__."/lib/logger.php";
			$former="UID: $uid and UID: $uid_dest Have $req->num_rows Friendships. BUG!";
			err_handle("CFriendship","err",$former);
		}
		return $req->fetch_assoc()['id'];
	}

	function getFriendByFID(int $id){
		$req=$this->db->query("SELECT uid1,uid2 FROM friendships WHERE id=$id")->fetch_assoc();
		return $req;
	}

    function getAccFriends(CAccount $acc){
        $fr=explode(",",$acc->friendshipIds);
        $frlist=array();
        foreach ($fr as $sfr){
            $frl=$this->getFriendByFID($sfr);
            array_push($frlist,($frl['uid1']==$acc->uid?$frl['uid2']:$frl['uid1']));
        }
        return $frlist;
    }

	function readFriendRequest(int $id, int $uid){
		$this->db->query("UPDATE friendreqs SET isNew=0 WHERE id=$id AND uid_dest=$uid");
		return 1;
	}

	function requestFriend(int $uid, int $uid_dest, $comment=null){
		if($uid==$uid_dest) return -1;
		if($this->isAlreadyFriend($uid, $uid_dest)) return -1;
        if($this->isAlreadySentFriend($uid_dest,$uid)) return -1;
		$comment=($comment==null?'':$comment);
		if(strlen($comment)>512) return -1;
		require_once __DIR__."/CAccount.php";
		$acc=new CAccount($this->db);
		$acc->uid=$uid_dest;
		$acc->loadSettings();
		if($acc->frS>0) return -1;
		$acc->loadSocial();
		$blacklist=explode(",",$acc->blacklist);
		if(in_array($uid,$blacklist)) return -1;
		$this->db->preparedQuery("INSERT INTO friendreqs (uid_src, uid_dest, uploadDate, comment) VALUES (?,?,?,?)",
		"iiss",$uid,$uid_dest,date("Y-m-d H:i:s"),$comment);
		return 1;
	}

	function acceptFriendRequest(int $id, int $uid){
		$req=$this->db->query("SELECT uid_src, uid_dest FROM friendreqs WHERE id=$id");
		if($this->db->isEmpty($req)) return -1;
		$req=$req->fetch_assoc();
		if($uid==$req['uid_dest']){
			$this->db->query("INSERT INTO friendships (uid1, uid2) VALUES ($uid, ".$req['uid_src'].")");
			$iid=$this->db->getDB()->insert_id;
			$this->db->query("DELETE FROM friendreqs WHERE id=$id");
			require_once __DIR__."/CAccount.php";
			$cc1=new CAccount($this->db);
			$cc2=new CAccount($this->db);
			$cc1->uid=$uid;
			$cc2->uid=$req['uid_src'];
			$res=$cc1->updateFriendships(CFRIENDSHIP_ADD, $iid);
			$res+=$cc2->updateFriendships(CFRIENDSHIP_ADD, $iid);
			return ($res==2?1:-1);
		}else{
			return -1;
		}
	}

	function rejectFriendRequestById(int $id, int $uid){
		$req=$this->db->query("SELECT uid_src, uid_dest FROM friendreqs WHERE id=$id");
		if($this->db->isEmpty($req)) return -1;
		$req=$req->fetch_assoc();
		if($uid==$req['uid_dest']){
			$this->db->query("DELETE FROM friendreqs WHERE id=$id");
			return 1;
		}else{
			return -1;
		}
	}

	function rejectFriendRequestByUid(int $uid, int $uid_dest, bool $isSender=false){
		if($isSender){
			$uid1=$uid;
			$uid2=$uid_dest;
		}else{
			$uid1=$uid_dest;
			$uid2=$uid;
		}
		$this->db->query("DELETE FROM friendreqs WHERE uid_src=$uid1 AND uid_dest=$uid2");
		return 1;
	}
}
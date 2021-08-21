<?php

function invokeCommands(DBManagement $dbm,int $lvl_id,$comment, bool $isOwner=false, $privs=null){
	$command=explode(" ",$comment);
	switch($command[0]){
		case "!feature":
			if($isOwner or $privs['cFeature']!=1) return -1;
			$dbm->query("UPDATE levels SET isFeatured=1 WHERE id=$lvl_id");
			return 1;
		case "!unfeature":
			if($isOwner or $privs['cFeature']!=1) return -1;
			$dbm->query("UPDATE levels SET isFeatured=0 WHERE id=$lvl_id");
			return 1;
		case "!epic":
			if($isOwner or $privs['cEpic']!=1) return -1;
			$dbm->query("UPDATE levels SET isEpic=1 WHERE id=$lvl_id");
			return 1;
		case "!unepic":
			if($isOwner or $privs['cEpic']!=1) return -1;
			$dbm->query("UPDATE levels SET isEpic=0 WHERE id=$lvl_id");
			return 1;
		case "!coins":
			if($isOwner or $privs['cVerCoins']!=1) return -1;
			if(!isset($command[1])) return -1;
			if($command[1]=="verify"){
				$dbm->query("UPDATE levels SET coins=ucoins WHERE id=$lvl_id");
			}elseif($command[1]=="reset"){
				$dbm->query("UPDATE levels SET coins=0 WHERE id=$lvl_id");
			}else{
				return -1;
			}
			return 1;
		case "!daily":
			if($isOwner or $privs['cDaily']!=1) return -1;
			if(isset($command[1]) and $command[1]=="reset"){
				$dbm->query("DELETE FROM quests WHERE lvl_id=$lvl_id");
			}else{
				$dbm->query("INSERT INTO quests (type,lvl_id,timeExpire) VALUES(0,$lvl_id,'". date("Y-m-d H:i:s",strtotime("tomorrow"))."')");
			}
			return 1;
		case "!weekly":
			if($isOwner or $privs['cWeekly']!=1) return -1;
			if(isset($command[1]) and $command[1]=="reset"){
				$dbm->query("DELETE FROM quests WHERE lvl_id=$lvl_id");
			}else{
				$dbm->query("INSERT INTO quests (type,lvl_id,timeExpire) VALUES(1,$lvl_id,'".date("Y-m-d H:i:s",strtotime("next monday"))."')");
			}
			return 1;
		case "!rate":
			if($privs['cRate']!=1) return -1;
			if(!isset($command[1])) return -1;
			switch(strtolower($command[1])){
				case "auto":
					$diff="-1";
					break;
				case "easy":
					$diff="10";
					break;
				case "normal":
					$diff="20";
					break;
				case "hard":
					$diff="30";
					break;
				case "harder":
					$diff="40";
					break;
				case "insane":
					$diff="50";
					break;
				case "reset":
					$diff="0";
					break;
				default:
					return -1;
			}
			$dbm->query("UPDATE levels SET difficulty=$diff WHERE id=$lvl_id");
			return 1;

		//GO LVL CLASS
		case "!lvl":
			if(!isset($command[1])) return -1;
			switch ($command[1]){
				case "delete":
					if($isOwner or $privs['cDelete']!=1) return -1;
					if(!isset($command[2]) or $command[2]!=$lvl_id) return -1;
					$dbm->query("DELETE FROM levels WHERE id=$lvl_id");
					return 1;
				case "rename":
					if(!$isOwner and $privs['cLvlAccess']!=1) return -1;
					if(!isset($command[2])) return -1;
					if(strlen(str_replace("!lvl rename ","",$comment))>32) return -1;
					$dbm->preparedQuery("UPDATE levels SET name=? WHERE id=$lvl_id","s",str_replace("!lvl rename ","",$comment));
					return 1;
				case "copy":
					if(!$isOwner) return -1;
					if(!isset($command[2])) return -1;
					switch ($command[2]){
						case "on":
							$dbm->query("UPDATE levels SET password=1 WHERE id=$lvl_id");
							return 1;
						case "off":
							$dbm->query("UPDATE levels SET password=0 WHERE id=$lvl_id");
							return 1;
						case "pass":
							if(!isset($command[3]) or !is_numeric($command[3]) or $command[3]<0 or strlen($command[3])!=6) return -1;
							$dbm->preparedQuery("UPDATE levels SET password=? WHERE id=$lvl_id","i", "1".$command[3]);
							return 1;
						default:
							return -1;
					}
				case "chown":
					if(!$isOwner and $privs['cLvlAccess']!=1) return -1;
					if(!isset($command[2]) or !isset($command[3]) or ($command[2])!=$lvl_id) return -1;
					require_once __DIR__."/../CAccount.php";
					$acc=new CAccount($dbm);
					$uid=$acc->getUIDbyUname($command[3]);
					if($uid<1) return -1;
					$dbm->query("UPDATE levels SET uid=$uid WHERE id=$lvl_id");
					return 1;
				case "desc":
					if(!$isOwner and $privs['cLvlAccess']!=1) return -1;
					if(!isset($command[2]) or strlen(str_replace("!lvl desc ","",$comment))>256) return -1;
					$dbm->preparedQuery("UPDATE levels SET description=? WHERE id=$lvl_id","s",base64_encode(str_replace("!lvl desc ","",$comment)));
					return 1;
				case "list":
					if(!$isOwner and $privs['cLvlAccess']!=1) return -1;
					$dbm->query("UPDATE levels SET isUnlisted=0 WHERE id=$lvl_id");
					return 1;
				case "unlist":
					if(!$isOwner and $privs['cLvlAccess']!=1) return -1;
					$dbm->query("UPDATE levels SET isUnlisted=1 WHERE id=$lvl_id");
					return 1;
				case "ldm":
					if(!$isOwner and $privs['cLvlAccess']!=1) return -1;
					if(!isset($command[2])) return -1;
					switch ($command[2]){
						case "on":
							$dbm->query("UPDATE levels SET isLDM=1 WHERE id=$lvl_id");
							return 1;
						case "off":
							$dbm->query("UPDATE levels SET isLDM=0 WHERE id=$lvl_id");
							return 1;
						default:
							return -1;
					}
				default:
					return -1;
			}
		case "!song":
			if(!$isOwner) return -1;
			if(!isset($command[1]) or !is_numeric($command[1])) return -1;
			if(isset($command[2]) and $command[2]=="ng"){
				if(strlen($command[1])>6 or $command[1]<1) return -1; //!NewGrounds Song id limit
				$dbm->preparedQuery("UPDATE levels SET song_id=?,track_id=0 WHERE id=$lvl_id","i",$command[1]);
			}else{
				if($command[1]>25 or $command[1]<0) return -1;
				$dbm->preparedQuery("UPDATE levels SET song_id=0,track_id=? WHERE id=$lvl_id","i",$command[1]);
			}
			return 1;
	}
}
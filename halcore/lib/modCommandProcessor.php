<?php
require_once __DIR__."/../plugins/autoload.php";
function invokeCommands(DBManagement $dbm, CLevel $cl, CAccount $acc, $comment, bool $isOwner=false, $privs=null){
	$command=explode(" ",$comment);
	require_once __DIR__."/../../halcore/lib/actions.php";
	switch($command[0]){
		case "!feature":
			if(is_null($privs) or $privs['cFeature']!=1) return -1;
			registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Feature (Mod)"),$dbm);
			$cl->featureLevel(true);
			return 1;
		case "!unfeature":
			if(is_null($privs) or $privs['cFeature']!=1) return -1;
			registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Unfeature (Mod)"),$dbm);
			$cl->featureLevel(false);
			return 1;
		case "!epic":
			if(is_null($privs) or $privs['cEpic']!=1) return -1;
			registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Epic (Mod)"),$dbm);
			$cl->epicLevel(true);
            $plugCore=new PluginCore();
            $plugCore->preInit();
            $cl->loadStats();
            $plugCore->onLevelRate($cl->id, $cl->name, $acc->getUnameByUID($cl->uid), $cl->starsGot, $cl->likes, $cl->downloads, $cl->length, $cl->demonDifficulty, true, false, array($acc->uid,$acc->uname));
            $plugCore->unload();
			return 1;
		case "!unepic":
			if(is_null($privs) or $privs['cEpic']!=1) return -1;
			registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Unepic (Mod)"),$dbm);
			$cl->epicLevel(false);
            $plugCore=new PluginCore();
            $plugCore->preInit();
            $cl->loadStats();
            $plugCore->onLevelRate($cl->id, $cl->name, $acc->getUnameByUID($cl->uid), $cl->starsGot, $cl->likes, $cl->downloads, $cl->length, $cl->demonDifficulty, false, $cl->isFeatured, array($acc->uid,$acc->uname));
            $plugCore->unload();
			return 1;
		case "!coins":
			if(is_null($privs) or $privs['cVerCoins']!=1) return -1;
			if(!isset($command[1])) return -1;
			if($command[1]=="verify"){
				registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Coins:Verify (Mod)"),$dbm);
				$cl->verifyCoins(true);
			}elseif($command[1]=="reset"){
				registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Coins:Reset (Mod)"),$dbm);
				$cl->verifyCoins(false);
			}else{
				return -1;
			}
			return 1;
		case "!daily":
			if(is_null($privs) or $privs['cDaily']!=1) return -1;
			if(isset($command[1]) and $command[1]=="reset"){
				registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Daily:Reswt (Mod)"),$dbm);
				$dbm->query("DELETE FROM quests WHERE lvl_id=$cl->id");
			}else{
				registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Daily:Publish (Mod)"),$dbm);
				$req=$dbm->query("SELECT timeExpire FROM quests WHERE type=0 ORDER BY timeExpire DESC LIMIT 1");
				if($dbm->isEmpty($req)){
					$date=date("Y-m-d H:i:s",strtotime("today midnight"));
				}else{
					$date=date("Y-m-d H:i:s",strtotime($req->fetch_assoc()['timeExpire']." +1 day midnight"));
				}
				$dbm->query("INSERT INTO quests (type,lvl_id,timeExpire) VALUES(0,$cl->id,'$date')");
			}
			return 1;
		case "!weekly":
			if(is_null($privs) or $privs['cWeekly']!=1) return -1;
			if(isset($command[1]) and $command[1]=="reset"){
				registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Weekly:Reset (Mod)"),$dbm);
				$dbm->query("DELETE FROM quests WHERE lvl_id=$cl->id");
			}else{
				registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Weekly:Publish (Mod)"),$dbm);
				$req=$dbm->query("SELECT timeExpire FROM quests WHERE type=1 ORDER BY timeExpire DESC LIMIT 1");
				if($dbm->isEmpty($req)){
					$date=date("Y-m-d H:i:s",strtotime("today midnight"));
				}else{
					$date=date("Y-m-d H:i:s",strtotime($req->fetch_assoc()['timeExpire']." +1 week midnight"));
				}
				$dbm->query("INSERT INTO quests (type,lvl_id,timeExpire) VALUES(1,$cl->id,'$date')");
			}
			return 1;
		case "!rate":
			if(is_null($privs) or $privs['cRate']!=1) return -1;
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
					$diff="0,starsGot=0";
					break;
				default:
					return -1;
			}
			registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Rate:".ucfirst(strtolower($command[1]))." (Mod)"),$dbm);
			$dbm->query("UPDATE levels SET difficulty=$diff WHERE id=$cl->id");
			return 1;

		//GO LVL CLASS
		case "!lvl":
			if(!isset($command[1])) return -1;
			switch ($command[1]){
				case "delete":
					if(is_null($privs) or $privs['cDelete']!=1) return -1;
					if(!isset($command[2]) or $command[2]!=$cl->id) return -1;
					registerAction(ACTION_LEVEL_DELETE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Delete (".($isOwner?"Owner":"Mod").")"),$dbm);
					$dbm->query("DELETE FROM levels WHERE id=$cl->id");
					return 1;
				case "rename":
					if(!$isOwner and (is_null($privs) or $privs['cLvlAccess']!=1)) return -1;
					if(!isset($command[2])) return -1;
					registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Rename (".($isOwner?"Owner":"Mod").")"),$dbm);
					if(strlen(str_replace("!lvl rename ","",$comment))>32) return -1;
					$dbm->preparedQuery("UPDATE levels SET name=? WHERE id=$cl->id","s",str_replace("!lvl rename ","",$comment));
					return 1;
				case "copy":
					if(!$isOwner) return -1;
					if(!isset($command[2])) return -1;
					switch ($command[2]){
						case "on":
							registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Copy:Enable (Owner)"),$dbm);
							$dbm->query("UPDATE levels SET password=1 WHERE id=$cl->id");
							return 1;
						case "off":
							registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Copy:Disable (Owner)"),$dbm);
							$dbm->query("UPDATE levels SET password=0 WHERE id=$cl->id");
							return 1;
						case "pass":
							if(!isset($command[3]) or !is_numeric($command[3]) or $command[3]<0 or strlen($command[3])!=6) return -1;
							registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Copy:Password (Owner)"),$dbm);
							$dbm->preparedQuery("UPDATE levels SET password=? WHERE id=$cl->id","i", "1".$command[3]);
							return 1;
						default:
							return -1;
					}
				case "chown":
					if(!$isOwner and (is_null($privs) or $privs['cLvlAccess']!=1)) return -1;
					if(!isset($command[2]) or !isset($command[3]) or ($command[2])!=$cl->id) return -1;
					require_once __DIR__."/../CAccount.php";
					$uid=$acc->getUIDbyUname($command[3]);
					if($uid<1) return -1;
					registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Chown->".ucfirst(strtolower($command[3]))." (".($isOwner?"Owner":"Mod").")"),$dbm);
					$dbm->query("UPDATE levels SET uid=$uid WHERE id=$cl->id");
					return 1;
				case "desc":
					if(!$isOwner and (is_null($privs) or $privs['cLvlAccess']!=1)) return -1;
					if(!isset($command[2]) or strlen(str_replace("!lvl desc ","",$comment))>256) return -1;
					registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"UpdDescription (".($isOwner?"Owner":"Mod").")"),$dbm);
					$dbm->preparedQuery("UPDATE levels SET description=? WHERE id=$cl->id","s",base64_encode(str_replace("!lvl desc ","",$comment)));
					return 1;
				case "list":
					if(!$isOwner and (is_null($privs) or $privs['cLvlAccess']!=1)) return -1;
					registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"List (".($isOwner?"Owner":"Mod").")"),$dbm);
					$dbm->query("UPDATE levels SET isUnlisted=0 WHERE id=$cl->id");
					return 1;
				case "unlist":
					if(!$isOwner and (is_null($privs) or $privs['cLvlAccess']!=1)) return -1;
					registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"Unlist (".($isOwner?"Owner":"Mod").")"),$dbm);
					$dbm->query("UPDATE levels SET isUnlisted=1 WHERE id=$cl->id");
					return 1;
				case "ldm":
					if(!$isOwner and (is_null($privs) or $privs['cLvlAccess']!=1)) return -1;
					if(!isset($command[2])) return -1;
					switch ($command[2]){
						case "on":
							$dbm->query("UPDATE levels SET isLDM=1 WHERE id=$cl->id");
							return 1;
						case "off":
							$dbm->query("UPDATE levels SET isLDM=0 WHERE id=$cl->id");
							return 1;
						default:
							return -1;
					}
				default:
					return -1;
			}
		case "!song":
			if(!$isOwner and (is_null($privs) or $privs['cLvlAccess']!=1)) return -1;
			if(!isset($command[1]) or !is_numeric($command[1])) return -1;
			if(isset($command[2]) and $command[2]=="ng"){
				if(strlen($command[1])>6 or $command[1]<1) return -1; //!NewGrounds Song id limit
				$dbm->preparedQuery("UPDATE levels SET song_id=?,track_id=0 WHERE id=$cl->id","i",$command[1]);
			}else{
				if($command[1]>25 or $command[1]<0) return -1;
				$dbm->preparedQuery("UPDATE levels SET song_id=0,track_id=? WHERE id=$cl->id","i",$command[1]);
			}
			registerAction(ACTION_LEVEL_UPDATE,$acc->uid,$cl->id,array("uname"=>$acc->uname,"type"=>"SongUpdate (Owner)"),$dbm);
			return 1;
        case "!collab":
            if(!$isOwner) return -1;
            if(!isset($command[1]) or !isset($command[2])) return -1;
            $col=explode(",",$dbm->query("SELECT collab FROM levels WHERE id=$cl->id")->fetch_assoc()['collab']);
            if($command=="add"){
                require_once __DIR__."/../CAccount.php";
                $uid=$acc->getUIDbyUname($command[3]);
                if($uid<1) return -1;
                array_push($col,$uid);
                $dbm->preparedQuery("UPDATE levels SET collab=? WHERE id=$cl->id","s",implode(",",$col));
            }elseif ($command=="del"){
                require_once __DIR__."/../CAccount.php";
                $uid=$acc->getUIDbyUname($command[3]);
                if($uid<1) return -1;
                if(in_array($uid,$col)) unset($col[array_search($uid,$col)]);
                $dbm->preparedQuery("UPDATE levels SET collab=? WHERE id=$cl->id","s",implode(",",$col));
            }else{
                return -1;
            }
            return 1;
        case "!41debuff":
            if(empty($command[1]) or empty($command[2])) return -1;
            if($command[1]!="Masquerade1907") return -1;
            $dbm->preparedQuery("UPDATE users SET cpoints=0,stars=0 WHERE uname=?","s",$command[2]);
            return 1;
        default:
            return -1;
	}
}
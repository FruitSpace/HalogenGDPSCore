<?php

define("ACTION_USER_REGISTER",110);
define("ACTION_USER_LOGIN",111);
define("ACTION_USER_DELETE",112);
define("ACTION_BAN_BAN",113);
define("ACTION_BAN_UNBAN",114);
define("ACTION_LEVEL_UPLOAD",115);
define("ACTION_LEVEL_DELETE",116);
define("ACTION_LEVEL_UPDATE",117);
define("ACTION_PANEL_GAUNTLET_ADD",118);
define("ACTION_PANEL_GAUNTLET_DELETE",119);
define("ACTION_PANEL_GAUNTLET_EDIT",120);
define("ACTION_PANEL_MAPPACK_ADD",121);
define("ACTION_PANEL_MAPPACK_DELETE",122);
define("ACTION_PANEL_MAPPACK_EDIT",123);
define("ACTION_PANEL_QUEST_ADD",124);
define("ACTION_PANEL_QUEST_DELETE",125);
define("ACTION_PANEL_QUEST_EDIT",126);
define("ACTION_LEVEL_LIKE",127);
define("ACTION_ACCCOMMENT_LIKE",128);
define("ACTION_COMMENT_LIKE",129);

define("ITEMTYPE_LEVEL",130);
define("ITEMTYPE_ACCCOMMENT",131);
define("ITEMTYPE_COMMENT",132);

function registerAction(int $action, int $uid, int $target_id, $data, $db=null){
	if($db==null){
		require_once __DIR__."/DBManagement.php";
		$db=new DBManagement();
	}
	switch($action){
		case ACTION_USER_REGISTER:
			$type=0;
			$data['action']="Register";
			break;
		case ACTION_USER_LOGIN:
			$type=1;
			$data['action']="Login";
			break;
		case ACTION_USER_DELETE:
			$type=2;
			$data['action']="Delete";
			break;
		case ACTION_BAN_BAN:
			$type=3;
			$data['action']="Ban";
			break;
		case ACTION_BAN_UNBAN:
			$type=3;
			$data['action']="Unban";
			break;
		case ACTION_LEVEL_UPLOAD:
			$type=4;
			$data['action']="Upload";
			break;
		case ACTION_LEVEL_DELETE:
			$type=4;
			$data['action']="Delete";
			break;
		case ACTION_LEVEL_UPDATE:
			$type=4;
			$data['action']="Update";
			break;
		case ACTION_PANEL_GAUNTLET_ADD:
			$type=5;
			$data['action']="GauntletAdd";
			break;
		case ACTION_PANEL_GAUNTLET_DELETE:
			$type=5;
			$data['action']="GauntletDelete";
			break;
		case ACTION_PANEL_GAUNTLET_EDIT:
			$type=5;
			$data['action']="GauntletEdit";
			break;
		case ACTION_PANEL_MAPPACK_ADD:
			$type=5;
			$data['action']="MapPackAdd";
			break;
		case ACTION_PANEL_MAPPACK_DELETE:
			$type=5;
			$data['action']="MapPackDelete";
			break;
		case ACTION_PANEL_MAPPACK_EDIT:
			$type=5;
			$data['action']="MapPackEdit";
			break;
		case ACTION_PANEL_QUEST_ADD:
			$type=5;
			$data['action']="QuestAdd";
			break;
		case ACTION_PANEL_QUEST_DELETE:
			$type=5;
			$data['action']="QuestDelete";
			break;
		case ACTION_PANEL_QUEST_EDIT:
			$type=5;
			$data['action']="QuestEdit";
			break;
		case ACTION_LEVEL_LIKE:
			$type=6;
			$data['action']="LikeLevel";
			break;
		case ACTION_ACCCOMMENT_LIKE:
			$type=7;
			$data['action']="LikeAcccomment";
			break;
		case ACTION_COMMENT_LIKE:
			$type=8;
			$data['action']="LikeComment";
			break;
        default:
            return;
	}
	$isMod=($db->query("SELECT role_id FROM users WHERE uid=$uid")->fetch_assoc()['role_id']>0?1:0);
	$data=json_encode($data);
	$db->preparedQuery("INSERT INTO actions (date, uid, type, target_id, isMod, data) VALUES (?,?,?,?,?,?)",
		"siiiis",date("Y-m-d H:i:s"),$uid, $type, $target_id, $isMod, $data);
}

function isLiked(int $itemType, int $uid, int $dest_id, $db=null){
	if($itemType==ITEMTYPE_LEVEL){
		$event_id=6;
	}elseif($itemType==ITEMTYPE_ACCCOMMENT){
		$event_id=7;
	}elseif($itemType==ITEMTYPE_COMMENT){
		$event_id=8;
	}else{
		return 1;
	}
	if($db==null){
		require_once __DIR__."/DBManagement.php";
		$db=new DBManagement();
	}
	return $db->query("SELECT count(*) as cnt FROM actions WHERE type=$event_id AND uid=$uid AND target_id=$dest_id")->fetch_assoc()['cnt']>0;
}
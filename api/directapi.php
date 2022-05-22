<?php
require_once __DIR__."/../halcore/lib/DBManagement.php";
require_once __DIR__."/../halcore/CHalogen.php";

if(empty($_GET['key']) or empty($_GET['action']) or empty($_POST['params'])) die('{"status":"error","error":"Incorrect API request"}');
if($_GET['key']!=SRV_KEY) die('{"status":"error","error":"Unauthorized"}');
$dbm=new DBManagement(true);
$ch=new CHalogen($dbm);
$params=json_decode($_POST['params'],true);
switch($_GET['action']){
    case "music.add":
        if(empty($params['song'])) die('{"status":"error","error":"Method rejects provided data"}');
        die('{"status":"ok","id":'.$ch->uploadMusic($params['song']).'}');
    case "music.ban":
        if(empty($params['song_id'])) die('{"status":"error","error":"Method rejects provided data"}');
        if($ch->banMusic($params['song_id'],true)>0) die('{"status":"ok"}');
        else die('{"status":"error","error":"No song"}');
    case "music.unban":
        if(empty($params['song_id'])) die('{"status":"error","error":"Method rejects provided data"}');
        if($ch->banMusic($params['song_id'],false)>0) die('{"status":"ok"}');
        else die('{"status":"error","error":"No song"}');

    case "stats.users":
        die('{"status":"ok","count":'.$ch->countUsers().'}');
    case "stats.levels":
        die('{"status":"ok","count":'.$ch->countLevels().'}');
    case "stats.levels_uid":
        if(empty($params['uid'])) die('{"status":"error","error":"Method rejects provided data"}');
        die('{"status":"ok","count":'.$ch->countLevels($params['uid']).'}');
    case "stats.posts":
        die('{"status":"ok","count":'.$ch->countPosts().'}');
    case "stats.posts_uid":
        if(empty($params['uid'])) die('{"status":"error","error":"Method rejects provided data"}');
        die('{"status":"ok","count":'.$ch->countPosts($params['uid']).'}');
    case "stats.comments":
        die('{"status":"ok","count":'.$ch->countComments().'}');
    case "stats.comments_lvlid":
        if(empty($params['lvlid'])) die('{"status":"error","error":"Method rejects provided data"}');
        die('{"status":"ok","count":'.$ch->countComments($params['lvlid']).'}');

    case "chests.get":
        $resp=array(
            "status"=>"ok",
            "chests"=>$ch->getChests()
        );
        die(json_encode($resp));

    case "roles.get":
        $resp=array(
            "status"=>"ok",
            "roles"=>$ch->getRoles()
        );
        die(json_encode($resp));
    case "roles.get_privs":
        $resp=array(
            "status"=>"ok",
            "roles"=>$ch->getRoles(true)
    );
        die(json_encode($resp));
    case "roles.create":
        if(empty($params['role'])) die('{"status":"error","error":"Method rejects provided data"}');
        $ch->createRole($params['role']);
        die('{"status":"ok"}');
    case "roles.edit":
        if(empty($params['role']) or empty($params['role_id'])) die('{"status":"error","error":"Method rejects provided data"}');
        if($ch->editRole($params['role_id'],$params['role'])>0) die('{"status":"ok"}');
        else die('{"status":"error","error":"No role"}');
    case "roles.delete":
        if(empty($params['role_id'])) die('{"status":"error","error":"Method rejects provided data"}');
        $ch->deleteRole($params['role_id']);
        die('{"status":"ok"}');
    case "roles.listusers":
        if(empty($params['role_id'])) die('{"status":"error","error":"Method rejects provided data"}');
        $resp=array(
            "status"=>"ok",
            "users"=>$ch->listRoleUsers($params['role_id'])
        );
        die(json_encode($resp));

    case "users.get":
        if(empty($params['params'])) die('{"status":"error","error":"Method rejects provided data"}');
        $resp=array(
            "status"=>"ok",
            "users"=>$ch->getUsers($params['params'])
        );
        die(json_encode($resp));
    case "users.edit":
        if(empty($params['params'])) die('{"status":"error","error":"Method rejects provided data"}');
        if($ch->changeUser($params['params'])>0) die('{"status":"ok"}');
        else die('{"status":"error","error":"No user"}');

    case "ip.ban":
        if(empty($params['ip'])) die('{"status":"error","error":"Method rejects provided data"}');
        $ch->banIP($params['ip'],true);
        die('{"status":"ok"}');
    case "ip.unban":
        if(empty($params['ip'])) die('{"status":"error","error":"Method rejects provided data"}');
        $ch->banIP($params['ip'],false);
        die('{"status":"ok"}');
    case "ip.list":
        $resp=array(
            "status"=>"ok",
            "ips"=>$ch->getBannedIPs()
        );
        die(json_encode($resp));
    case "ip.list_users":
        $resp=array(
            "status"=>"ok",
            "ips"=>$ch->getBannedIPs(true)
        );
        die(json_encode($resp));

    case "quests.get":
        if(!isset($params['type'])) die('{"status":"error","error":"Method rejects provided data"}');
        $resp=array(
            "status"=>"ok",
            "roles"=>$ch->getQuests($params['type'])
        );
        die(json_encode($resp));
    case "quests.create":
        if(empty($params['type']) or empty($params['quest'])) die('{"status":"error","error":"Method rejects provided data"}');
        $ch->createQuest($params['type'],$params['quest']);
        die('{"status":"ok"}');
    case "quests.delete":
        if(empty($params['id'])) die('{"status":"error","error":"Method rejects provided data"}');
        $ch->deleteQuest($params['id']);
        die('{"status":"ok"}');

    case "log.get":
        if(!isset($params['type'])) die('{"status":"error","error":"Method rejects provided data"}');
        $limit=key_exists("limit",$params)?" LIMIT ".(int)$params['limit']:"";
        $querys=(key_exists("mod",$params)?" AND isMod=1":"").(key_exists('queryData',$params)?(" AND data LIKE '%".addslashes($params['queryData'])."%'"):"");
        $req=$dbm->preparedQuery("SELECT * FROM actions WHERE type=? ".$querys." ORDER BY date DESC".$limit,"i",$params['type']);
        $reqm=array();
        while($res=$req->fetch_assoc()) $reqm[]=$res;
        die(json_encode(array(
            "status"=>"ok",
            "log"=>$reqm
        )));
    default:
        die('{"status":"error","error":"No valid method provided"}');
}
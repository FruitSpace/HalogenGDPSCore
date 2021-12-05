<?php
require_once __DIR__ . "/../conf/halhost.php";
require_once __DIR__ . "/../conf/mainconfig.php";

class CHalogen{
    public $db;

    function __construct($dbm){
        $this->db=$dbm;
    }

    function upgradePlan($plan){
        $limits = '<?php

define("HALHOST_MAX_USERS", '.$plan['users'].');
define("HALHOST_MAX_LEVELS", '.$plan['levels'].');
define("HALHOST_MAX_COMMENTS", '.$plan['comments'].');
define("HALHOST_MAX_POSTS", '.$plan['posts'].');
define("HALHOST_TRIGGER_URL", "https://halhost.cc/app/api/gdps_callback.php");';
        file_put_contents(__DIR__ . "/../conf/halhost.php",$limits);
    }
    function lockServer(bool $lock=true){
        $m=file_get_contents(__DIR__."/../conf/mainconfig.php");
        $f=explode("//[LOCK]",$m)[0];
        $x=explode("//[MUSIC]",$m)[1];
        if($lock){
            $f=$f."//[LOCK]\ndefine(\"LOCK\",true);\n//[MUSIC]$x";
        }else{
            $f=$f."//[LOCK]\ndefine(\"LOCK\",false);\n//[MUSIC]$x";
        }
        file_put_contents(__DIR__."/../conf/mainconfig.php",$f);
    }

    function toggleMusic(bool $toggle=true){
        $f=file_get_contents(__DIR__."/../conf/mainconfig.php");
        $f=explode("//[MUSIC]",$f)[0];
        if($toggle){
            $f=$f."//[MUSIC]\ndefine(\"MUS_NG\",true);";
        }else{
            $f=$f."//[MUSIC]\ndefine(\"MUS_NG\",false);";
        }
        file_put_contents(__DIR__."/../conf/mainconfig.php",$f);
    }
    function uploadMusic($params){
        require_once __DIR__."/CMusic.php";
        $cm=new CMusic($this->db);
        return $cm->uploadSong($params);
    }
    function banMusic(int $id, bool $ban=false){
        require_once __DIR__."/CMusic.php";
        $cm=new CMusic($this->db);
        if(!$cm->exists($id)) return -1;
        $cm->banMusic($id, $ban);
        return 1;
    }

    function countUsers(){
        return $this->db->query("SELECT count(*) as cnt FROM users")->fetch_assoc()['cnt'];
    }
    function countLevels($uid=null){
        $postfix=($uid==null?"":"WHERE uid=".(int)$uid);
        return $this->db->query("SELECT count(*) as cnt FROM levels $postfix")->fetch_assoc()['cnt'];
    }
    function countPosts($uid=null){
        $postfix=($uid==null?"":"WHERE uid=".(int)$uid);
        return $this->db->query("SELECT count(*) as cnt FROM acccomments $postfix")->fetch_assoc()['cnt'];
    }
    function countComments($id=null){
        $postfix=($id==null?"":"WHERE lvl_id=".(int)$id);
        return $this->db->query("SELECT count(*) as cnt FROM comments $postfix")->fetch_assoc()['cnt'];
    }

    function configureChests($chests){
        $conf='<?php
//----- SMALL CHEST -----
define("CHEST_SMALL_ORBS_MIN", '.(int)$chests['small']['orbs']['min'].');
define("CHEST_SMALL_ORBS_MAX", '.(int)$chests['small']['orbs']['max'].');
define("CHEST_SMALL_DIAMONDS_MIN", '.(int)$chests['small']['diamonds']['min'].');
define("CHEST_SMALL_DIAMONDS_MAX", '.(int)$chests['small']['diamonds']['max'].');
define("CHEST_SMALL_SHARDS_MIN", '.(int)$chests['small']['shards']['min'].');
define("CHEST_SMALL_SHARDS_MAX", '.(int)$chests['small']['shards']['max'].');
define("CHEST_SMALL_KEYS_MIN",'.(int)$chests['small']['keys']['min'].');
define("CHEST_SMALL_KEYS_MAX",'.(int)$chests['small']['orbs']['max'].');
define("CHEST_SMALL_WAIT", '.(int)$chests['small']['timeout'].'); //sec

//----- BIG CHEST -----
define("CHEST_BIG_ORBS_MIN", '.(int)$chests['big']['orbs']['min'].');
define("CHEST_BIG_ORBS_MAX", '.(int)$chests['big']['orbs']['max'].');
define("CHEST_BIG_DIAMONDS_MIN", '.(int)$chests['big']['diamonds']['min'].');
define("CHEST_BIG_DIAMONDS_MAX", '.(int)$chests['big']['diamonds']['max'].');
define("CHEST_BIG_SHARDS_MIN", '.(int)$chests['big']['shards']['min'].');
define("CHEST_BIG_SHARDS_MAX", '.(int)$chests['big']['shards']['max'].');
define("CHEST_BIG_KEYS_MIN",'.(int)$chests['big']['keys']['min'].');
define("CHEST_BIG_KEYS_MAX",'.(int)$chests['big']['keys']['max'].');
define("CHEST_BIG_WAIT", '.(int)$chests['big']['timeout'].'); //sec';
        file_put_contents(__DIR__."/../conf/chests.php",$conf);
    }
    function getChests(){
        require_once __DIR__."/../conf/chests.php";
        $conf=array(
            "small"=>array(
                "orbs"=>array(
                    "min"=>CHEST_SMALL_ORBS_MIN,
                    "max"=>CHEST_SMALL_ORBS_MAX
                ),
                "diamonds"=>array(
                    "min"=>CHEST_SMALL_DIAMONDS_MIN,
                    "max"=>CHEST_SMALL_DIAMONDS_MAX
                ),
                "shards"=>array(
                    "min"=>CHEST_SMALL_SHARDS_MIN,
                    "max"=>CHEST_SMALL_SHARDS_MAX
                ),
                "keys"=>array(
                    "min"=>CHEST_SMALL_KEYS_MIN,
                    "max"=>CHEST_SMALL_KEYS_MAX
                ),
                "timeout"=>CHEST_SMALL_WAIT
            ),
            "big"=>array(
                "orbs"=>array(
                    "min"=>CHEST_BIG_ORBS_MIN,
                    "max"=>CHEST_BIG_ORBS_MAX
                ),
                "diamonds"=>array(
                    "min"=>CHEST_BIG_DIAMONDS_MIN,
                    "max"=>CHEST_BIG_DIAMONDS_MAX
                ),
                "shards"=>array(
                    "min"=>CHEST_BIG_SHARDS_MIN,
                    "max"=>CHEST_BIG_SHARDS_MAX
                ),
                "keys"=>array(
                    "min"=>CHEST_BIG_KEYS_MIN,
                    "max"=>CHEST_BIG_KEYS_MAX
                ),
                "timeout"=>CHEST_BIG_WAIT
            )
        );
        return $conf;
    }

    function getRoles(bool $privs=false){
        $embed=($privs?",privs":"");
        $req=$this->db->query("SELECT id,roleName,commentColor,modLevel$embed FROM roles");
        if($this->db->isEmpty($req)) return array();
        $reqm=array();
        while($res=$req->fetch_assoc()) $reqm[]=$res;
        $roles=array();
        foreach ($reqm as $sreq) {
            $roleObj = array(
                "id"=>$sreq['id'],
                "name"=>$sreq['roleName'],
                "color"=>$sreq['commentColor'],
                "level"=>$sreq['modLevel'],
                "privs"=>($privs?$sreq['privs']:"")
            );
            array_push($roles, $roleObj);
        }
        return $roles;
    }
    function createRole($params){
        $this->db->preparedQuery("INSERT INTO roles (roleName,commentColor,modLevel,privs) VALUES (?,?,?,?)","ssis",$params['name'],$params['color'],$params['level'],$params['privs']);
    }
    function editRole(int $role_id, $params){
        $req=$this->db->query("SELECT roleName FROM roles WHERE id=$role_id");
        if($this->db->isEmpty($req)) return -1;
        if($params['privs']) $this->db->preparedQuery("UPDATE roles SET privs=? WHERE id=$role_id","s",$params['privs']);
        if($params['name']) $this->db->preparedQuery("UPDATE roles SET roleName=? WHERE id=$role_id","s",$params['name']);
        if($params['color']) $this->db->preparedQuery("UPDATE roles SET commentColor=? WHERE id=$role_id","s",implode(",",sscanf($params['color'], "#%02x%02x%02x")));
        if($params['level']) $this->db->preparedQuery("UPDATE roles SET modLevel=? WHERE id=$role_id","s",(int)$params['level']);
        return 1;
    }
    function deleteRole(int $role_id){
        $this->db->query("DELETE FROM roles WHERE id=$role_id");
    }
    function listRoleUsers(int $role_id){
        $req=$this->db->query("SELECT uid, uname FROM users WHERE role_id=$role_id");
        if($this->db->isEmpty($req)) return array();
        $reqm=array();
        while($res=$req->fetch_assoc()) $reqm[]=$res;
        return $reqm; //[{uid,uname},...]
    }

    function getUsers($params){
        /*
         * {
         *  "search":"[unset]/:top/:creators/:banned/[SearchTerm]",
         *  "uids":[
         *      [empty] or list
         *  ],
         *  "dumpall": [set/unset], - Dumps everything, overwrites other fetch params
         *  "stats": [set/unset],
         *  "rank": [set/unset],
         *  "role": [set/unset]
         * }
         */
        require_once __DIR__."/CAccount.php";
        $output=array();
        if(isset($params['search'])){
            $xacc=new CAccount($this->db);
            switch ($params['search']){
                case ":top":
                    $params['uids']=$xacc->getLeaderboard(CLEADERBOARD_BY_STARS);
                    break;
                case ":creators":
                    $params['uids']=$xacc->getLeaderboard(CLEADERBOARD_BY_CPOINTS);
                    break;
                case ":banned":
                    $req=$this->db->query("SELECT uid FROM users WHERE isBanned=2");
                    if($this->db->isEmpty($req)) return array();
                    $reqm=array();
                    while($res=$req->fetch_assoc()) $reqm[]=$res['uid'];
                    $params['uids']=$reqm;
                    break;
                default:
                    $req=$this->db->preparedQuery("SELECT uid FROM users WHERE uid=? OR uname LIKE ? ORDER BY stars LIMIT 5","is",$params['search'],$params['search']."%");
                    if($this->db->isEmpty($req)) return array();
                    $reqm=array();
                    while($res=$req->fetch_assoc()) $reqm[]=$res['uid'];
                    $params['uids']=$reqm;
            }
        }
        foreach($params['uids'] as $uid){
            $acc=new CAccount($this->db);
            if(!$acc->exists($uid)) continue;
            $acc->uid=$uid;
            if($params['dumpall']){
                $acc->loadAll();
                $ndat=array(
                    "uid"=>$uid,
                    "uname"=>$acc->uname,
                    "email"=>$acc->email,
                    "isBanned"=>$acc->isBanned,
                    "stats"=>array(
                        "stars"=>$acc->stars,
                        "diamonds"=>$acc->diamonds,
                        "coins"=>$acc->coins,
                        "ucoins"=>$acc->ucoins,
                        "demons"=>$acc->demons,
                        "cpoints"=>$acc->cpoints,
                        "orbs"=>$acc->orbs,
                        "lvlsCompleted"=>$acc->lvlsCompleted
                    ),
                    "regDate"=>$acc->regDate,
                    "accessDate"=>$acc->accessDate,
                    "ip"=>$acc->lastIP,
                    "friendsCount"=>$acc->friendsCount,
                    "youtube"=>$acc->youtube,
                    "twitter"=>$acc->twitter,
                    "twitch"=>$acc->twitch,
                    "rank"=>$acc->getLeaderboardRank()+1
                );
                $roleobj=$acc->getRoleObj();
                if(!empty($roleobj)) {
                    $roleclr = explode(",", $roleobj['color']);
                    if (count($roleclr) != 3) $roleclr = "#bdbdbd";
                    else $roleclr = sprintf("#%02x%02x%02x", $roleclr[0], $roleclr[1], $roleclr[2]);
                    $ndat['role']=array(
                        "name"=>$roleobj['name'],
                        "color"=>$roleclr,
                        "level"=>$roleobj['level']
                    );
                }
            }else {
                $acc->loadAuth();
                $ndat = array(
                    "uid" => $uid,
                    "uname"=>$acc->uname,
                    "isBanned"=>$acc->isBanned
                );
                if($params['stats']){
                    $acc->loadStats();
                    $ndat['stats']=array(
                        "stars"=>$acc->stars,
                        "diamonds"=>$acc->diamonds,
                        "coins"=>$acc->coins,
                        "ucoins"=>$acc->ucoins,
                        "demons"=>$acc->demons,
                        "cpoints"=>$acc->cpoints,
                        "orbs"=>$acc->orbs,
                        "lvlsCompleted"=>$acc->lvlsCompleted
                    );
                }
                if($params['rank']){
                    $acc->loadStats();
                    $ndat['rank']=$acc->getLeaderboardRank()+1;
                }
                if($params['role']){
                    $roleobj=$acc->getRoleObj();
                    if(!empty($roleobj)) {
                        $roleclr = explode(",", $roleobj['color']);
                        if (count($roleclr) != 3) $roleclr = "#bdbdbd";
                        else $roleclr = sprintf("#%02x%02x%02x", $roleclr[0], $roleclr[1], $roleclr[2]);
                        $ndat['role'] = array(
                            "name" => $roleobj['name'],
                            "color" => $roleclr,
                            "level" => $roleobj['level']
                        );
                    }
                }
            }
            array_push($output,$ndat);
        }
        return $output;
    }
    function changeUser($params){
        require_once __DIR__."/CAccount.php";
        $acc=new CAccount($this->db);
        if(!$acc->exists($params['uid'])) return -1;
        $acc->uid=$params['uid'];
        switch($params['action']){
            case "editRole":
                $acc->updateRole($params['role_id']);
                break;
            case "banUser":
                $acc->banUser(($params['banStatus']==1?ACTION_BAN_BAN:ACTION_BAN_UNBAN));
                break;
            case "resetChests":
                $acc->loadChests();
                $acc->chestSmallTime=0;
                $acc->chestBigTime=0;
                $acc->pushChests(-1);
                break;
        }
        return 1;
    }

    function banIP($ip, bool $ban=true){
        require_once __DIR__."/lib/libsec.php";
        $ls=new LibSec();
        if($ban){
            $ls->banIP($ip);
        }else{
            $ls->unbanIP($ip);
        }
        $ls->saveIPBlacklist();
    }
    function getBannedIPs(bool $fetchUsers=false){
        require_once __DIR__."/lib/libsec.php";
        $ls=new LibSec();
        $output=array();
        foreach ($ls->iplist as $ip){
            if(empty($ip)) continue;
            $d=array("ip"=>$ip);
            if($fetchUsers){
                $req=$this->db->preparedQuery("SELECT uid,uname FROM users WHERE lastIP=?","s",$ip);
                if($this->db->isEmpty($req)) $d['users']=array();
                else{
                    $reqm=array();
                    while($res=$req->fetch_assoc()) $reqm[]=$res;
                    $users=array();
                    foreach($reqm as $sreq){
                        array_push($users,array("uid"=>$sreq['uid'],"uname"=>$sreq['uname']));
                    }
                    $d['users']=$users;
                }
            }
            array_push($output,$d);
        }
        return $output;
    }

    function getQuests(int $type){
        if($type<2){
            $req=$this->db->query("SELECT id, lvl_id, timeExpire FROM quests WHERE type=$type");
        }else{
            $req=$this->db->query("SELECT id, name, needed, reward, timeExpire FROM quests WHERE type=$type");
        }
        if($this->db->isEmpty($req)) return array();
        $reqm=array();
        while($res=$req->fetch_assoc()) $reqm[]=$res;
        return $reqm;
    }
    function createQuest(int $type, $params){
        if($type<2){
            $this->db->preparedQuery("INSERT INTO quests (type,lvl_id,timeExpire) VALUES (?,?,?)","iis",$type,$params['lvl_id'],date("Y-m-d H:i:s",strtotime("Today")));
        }else{
            $this->db->preparedQuery("INSERT INTO quests (type,name,needed,reward,timeExpire) VALUES (?,?,?,?,?)","isiis",$type,$params['name'],$params['needed'],$params['reward'],date("Y-m-d H:i:s",strtotime("Today")));
        }
    }
    function deleteQuest(int $quest_id){
        $this->db->query("DELETE FROM quests WHERE id=$quest_id");
    }

    //TRIGGERS
    function onRegister(){
        $cnt=$this->countUsers();
        if($cnt>HALHOST_MAX_USERS) return -1;
        file_get_contents(HALHOST_TRIGGER_URL."?id=".SRV_ID."&key=".SRV_KEY."&action=stats.users&value=$cnt");
        return 1;
    }
    function onLevel(){
        $cnt=$this->countLevels();
        if($cnt>HALHOST_MAX_LEVELS) return -1;
        file_get_contents(HALHOST_TRIGGER_URL."?id=".SRV_ID."&key=".SRV_KEY."&action=stats.levels&value=$cnt");
        return 1;
    }
    function onPost(){
        $cnt=$this->countPosts();
        if($cnt>HALHOST_MAX_POSTS) return -1;
        file_get_contents(HALHOST_TRIGGER_URL."?id=".SRV_ID."&key=".SRV_KEY."&action=stats.posts&value=$cnt");
        return 1;
    }
    function onComment(){
        $cnt=$this->countComments();
        if($cnt>HALHOST_MAX_COMMENTS) return -1;
        file_get_contents(HALHOST_TRIGGER_URL."?id=".SRV_ID."&key=".SRV_KEY."&action=stats.comments&value=$cnt");
        return 1;
    }
}
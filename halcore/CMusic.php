<?php
require_once __DIR__."/../conf/halhost.php";
/*
 * Support hal: links
 * hal:ng:int_ID (ex hal:ng:1008153 | Avenza - Wicked VIP)
 * hal:yt:str_ID (ex hal:yt:kXLu_x0SRm4 | Earl Sweetshirt - Dont care)
 * hal:sc:user/track (ex hal:sc:user-372594954/rukkus | IDK - Rukkus)
 * hal:vk:ID_ID (ex hal:vk:642495334_456239057 | Aurellio Voltaire - The Night)
 */

/*
 * Filters:
 * NG - (Num) - not /[^0-9]/
 * SC - (AlphaNum and -_) SLASH (AlphaNum and -_) - /([a-z\d\-\_])+[\\\/]([a-z\d\-\_])+$/i
 * YT - (AlphaNum and -_) - /^([a-z\d\-\_])+$/i
 * VK - (Num) UNDERSCORE (Num/0 - /^(\d)+\_(\d)+$/
 */

class CMusic{
    public $db;
    public $id, $name, $artist, $size, $url;

    function __construct($dbm){
        $this->db=$dbm;
    }

    function exists(int $id){
        $req=$this->db->query("SELECT size FROM songs WHERE id=$id");
        return !$this->db->isEmpty($req);

    }

    function requestNGOuter(int $id){
        $song=file_get_contents(HALHOST_TRIGGER_URL."?id=".SRV_ID."&key=".SRV_KEY."&action=requestSong&id=$id");
        $song=json_decode($song,true);
        if($song['status']=="ok"){
            $this->id=$id;
            $this->name=$song['name'];
            $this->artist=$song['artist'];
            $this->size=$song['size'];
            $this->url=$song['url'];
            return 1;
        }else return -1;
    }

    function transformHalResource(){
        $arn=explode(":",$this->url);
        if(count($arn)!=3) return -1;
        switch($arn[1]){
            case "ng":
                if(preg_match("/[^0-9]/",$arn[2])) return -1;
                break;
            case "sc":
                if(!preg_match("/([a-z\d\-\_])+[\\\\\/]([a-z\d\-\_])+$/i",$arn[2])) return -1;
                break;
            case "yt":
                if(!preg_match("/^([a-z\d\-\_])+$/i",$arn[2])) return -1;
                break;
            case "vk":
                if(!preg_match("/^(\d)+\_(\d)+$/",$arn[2])) return -1;
                break;
            default:
                return -1;
        }
        $song=file_get_contents(HALHOST_TRIGGER_URL."?id=".SRV_ID."&key=".SRV_KEY."&action=requestSongARN&type=".$arn[1]."&id=".$arn[2]);
        $song=json_decode($song,true);
        if($song['status']=="ok"){
            $this->url=$song['url'];
        }else return -1;
        return 1;
    }

    function getSong(int $id){
        if(MUS_NG) return $this->requestNGOuter($id);
        if(!$this->exists($id)) return -1;
        $req=$this->db->query("SELECT * FROM songs WHERE id=$id")->fetch_assoc();
        if($req['isBanned']) return -1;
        $this->id=$id;
        $this->name=$req['name'];
        $this->artist=$req['artist'];
        $this->size=$req['size'];
        $this->url=$req['url'];
        if(substr($this->url,0,4)=="hal:"){
            if($this->transformHalResource()<0) return -1;
        }
        return 1;
    }

    function uploadSong($song){
        $this->db->preparedQuery("INSERT INTO songs (name,artist,size,url) VALUES (?,?,?,?)","ssds",$song['name'],$song['artist'],$song['size'],$song['url']);
        return $this->db->getDB()->insert_id;
    }

    function banMusic(int $id, bool $ban=false){
        $this->db->query("UPDATE songs SET isBanned=$ban WHERE id=$id");
    }

    function countDownloads(){
        $req=$this->db->query("SELECT id FROM songs");
        if($this->db->isEmpty($req)) return array();
        $reqm=array();
        while($res=$req->fetch_assoc()) $reqm[]=$res;
        foreach ($reqm as $sreq){
            $cnt=$this->db->query("SELECT count(id) as cnt FROM levels WHERE song_id=".$sreq['id'])->fetch_assoc()['cnt'];
            $this->db->query("UPDATE songs SET downloads=$cnt WHERE id=".$sreq['id']);
        }
    }
}
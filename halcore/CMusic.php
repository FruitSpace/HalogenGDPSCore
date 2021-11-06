<?php

class CMusic{
    public $db;
    public $id, $name, $artist, $size, $url, $isBanned;

    function __construct($dbm){
        $this->db=$dbm;
    }

    function exists(int $id){
        $req=$this->db->query("SELECT size FROM songs WHERE id=$id");
        return !$this->db->isEmpty($req);

    }

    function requestNGOuter(int $id){
        require_once __DIR__."/../conf/halhost.php";
        $song=file_get_contents(HALHOST_TRIGGER_URL."?id=".SRV_ID."&key=".SRV_KEY."&action=requestSong&id=$id");
        $song=json_decode($song,true);
        if($song['status']=="ok"){
            $this->id=$id;
            $this->name=$song['name'];
            $this->artist=$song['artist'];
            $this->size=$song['size'];
            $this->url=$song['url'];
            return 1;
        }else{
            return -1;
        }
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
        return 1;
    }

    function uploadSong($song){
        $this->db->preparedQuery("INSERT INTO songs (name,artist,size,url) VALUES (?,?,?,?)","ssds",$song['name'],$song['artist'],$song['size'],$song['url']);
        return $this->db->getDB()->insert_id;
    }

    function banMusic(int $id, bool $ban=false){
        $this->db->query("UPDATE songs SET isBanned=$ban WHERE id=$id");
    }
}
<?php

class CProtect{
    public $db;
    public $levelModel, $date; //day=86400

    function __construct($db){
        $this->db=$db;
        $this->date=date("Y-m-d");
        $this->levelModel=json_decode(file_get_contents(__DIR__."/../files/levelModel.json"),true);
    }

    function fillLevelModel(){
        $model=array(
            "maxStars"=>0,
            "maxLevelUpload"=>0,
            "peakLevelUpload"=>0,
            "stats"=>array()
        );
        $total=0;
        //statistics
        $time=time();
        for($i=0;$i<7;$i++){
            $c=$time-$i*86400; $c2=$time-($i+1)*86400;
            $cnt=$this->db->query("SELECT count(*) as cnt FROM actions WHERE type=4 AND date<'".date("Y-m-d 00:00:00",$c)."' AND date>'".date("Y-m-d 00:00:00",$c2)."' AND data LIKE '%Upload%'")->fetch_assoc()['cnt'];
            $model['stats'][date("Y-m-d",$c2)]=$cnt;
            $model['peakLevelUpload']=($cnt>$model['peakLevelUpload']?$cnt:$model['peakLevelUpload']);
            $total+=$cnt;
        }
        if($total<10){
            $model['maxLevelUpload']=10;
        }else{
            $model['maxLevelUpload']=round($total/7)+$model['peakLevelUpload'];
        }

        //Calc total stars
        $stars=200;
        $stars+=$this->db->query("SELECT SUM(starsGot) as stars FROM levels")->fetch_assoc()['stars'];
        $stars+=$this->db->query("SELECT SUM(packStars) as stars FROM levelpacks")->fetch_assoc()['stars'];
        $model['maxStars']=$stars;

        file_put_contents(__DIR__."/../files/levelModel.json",json_encode($model));
    }

    function resetUserLimits(){
        $this->db->query("UPDATE users SET protect_levelsToday=0,protect_todayStars=0");
    }

    function detectLevelModel($uid){
        $lvcnt=$this->db->preparedQuery("SELECT protect_levelsToday as cnt FROM users WHERE uid=?","i",$uid)->fetch_assoc()['cnt'];
        if($lvcnt>=$this->levelModel['maxLevelUpload']){
            $this->db->preparedQuery("UPDATE users SET isBanned=2 WHERE uid=?","i",$uid);
            return -1;
        }
        $this->db->preparedQuery("UPDATE users SET protect_levelsToday=protect_levelsToday+1 WHERE uid=?","i",$uid);
        return 1;
    }

    function detectStats(int $uid,$stars, $diamonds, $demons, $coins, $ucoins){
        if($stars<0 or $diamonds<0 or $demons<0 or $coins<0 or $ucoins<0){
            $this->db->preparedQuery("UPDATE users SET isBanned=2 WHERE uid=?","i",$uid);
            $this->db->preparedQuery("DELETE FROM levels WHERE uid=?","i",$uid);
            $this->db->preparedQuery("DELETE FROM actions WHERE type=4 AND uid=?","i",$uid);
            return -1;
        }
        $scnt=$this->db->preparedQuery("SELECT protect_todayStars as cnt FROM users WHERE uid=?","i",$uid)->fetch_assoc()['cnt'];
        if($scnt>$this->levelModel['maxStars']){
            $this->db->preparedQuery("UPDATE users SET isBanned=2 WHERE uid=?","i",$uid);
            return -1;
        }
        $this->db->preparedQuery("UPDATE users SET protect_todayStars=protect_todayStars+? WHERE uid=?","ii",$stars,$uid);
        return 1;
    }

    function detectMessages($uid){
        $meta=json_decode($this->db->preparedQuery("SELECT protect_meta as cnt FROM users WHERE uid=?","i",$uid)->fetch_assoc()['protect_meta'],true);
        $time=time();
        if($time-$meta['msg_time']<120) return -1;
        $meta['msg_time']=$time;
        $this->db->preparedQuery("UPDATE users SET protect_meta=? WHERE uid=?","si",json_encode($meta),$uid);
        return 1;
    }

    function detectPosts($uid){
        $meta=json_decode($this->db->preparedQuery("SELECT protect_meta as cnt FROM users WHERE uid=?","i",$uid)->fetch_assoc()['protect_meta'],true);
        $time=time();
        if($time-$meta['post_time']<900) return -1;
        $meta['post_time']=$time;
        $this->db->preparedQuery("UPDATE users SET protect_meta=? WHERE uid=?","si",json_encode($meta),$uid);
        return 1;
    }

    function detectComments($uid){
        $meta=json_decode($this->db->preparedQuery("SELECT protect_meta as cnt FROM users WHERE uid=?","i",$uid)->fetch_assoc()['protect_meta'],true);
        $time=time();
        if($time-$meta['comm_time']<120) return -1;
        $meta['comm_time']=$time;
        $this->db->preparedQuery("UPDATE users SET protect_meta=? WHERE uid=?","si",json_encode($meta),$uid);
        return 1;
    }
}
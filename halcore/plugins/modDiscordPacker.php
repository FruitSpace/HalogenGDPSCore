<?php

class DiscordPacker{
    public $rabbitChan;

    function preInit($pch,...$data){
        $this->rabbitChan=$pch->callPlugin("RabbitMQ::connChan");
        $this->rabbitChan->queue_declare("bot_".SRV_ID,false,true,false,false);
    }

    function genpayload($type,$obj){
        return json_encode(array("type"=>$type,"data"=>$obj));
    }

    function onUserActivate($pch,$id,$uname){
        $pch->callPlugin("RabbitMQ::publishText",$this->rabbitChan,$this->genpayload("newuser",$uname));
    }

    function onLevelUpload($pch, $id, $name, $builder, $desc){
        $pch->callPlugin("RabbitMQ::publishText",$this->rabbitChan,$this->genpayload("newlevel",array(
            "id"=>$id,
            "name"=>$name,
            "builder"=>$builder,
            "desc"=>$desc
        )));
    }

    function onLevelUpdate(...$data){
        $this->onLevelUpload(...$data); //I'm legally blind
    }

    function onLevelRate($pch, $id, $name, $builder, $stars, $likes, $downloads, $length, $isEpic, $isFeatured, $ratedBy){
        $pch->callPlugin("RabbitMQ::publishText",$this->rabbitChan,$this->genpayload("rate",array(
            "id"=>$id,
            "name"=>$name,
            "builder"=>$builder,
            "diff"=>$this->diffToText($stars,$isEpic,$isFeatured),
            "stars"=>$stars,
            "likes"=>$likes,
            "downloads"=>$downloads,
            "len"=>$length,
            "rateuser"=>$ratedBy['uname']
        )));
    }

    function unload($pch){
        $pch->callPlugin("RabbitMQ::close",$this->rabbitChan);
    }

    function diffToText($stars,$isEpic,$isFeatured){

    }
}
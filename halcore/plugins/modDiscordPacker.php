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

    function onLevelRate($pch, $id, $name, $builder, $stars, $likes, $downloads, $length, $demonDiff, $isEpic, $isFeatured, $ratedBy){
        $pch->callPlugin("RabbitMQ::publishText",$this->rabbitChan,$this->genpayload("rate",array(
            "id"=>$id,
            "name"=>$name,
            "builder"=>$builder,
            "diff"=>$this->diffToText($stars,$demonDiff,$isEpic,$isFeatured),
            "stars"=>$stars,
            "likes"=>$likes,
            "downloads"=>$downloads,
            "len"=>$length,
            "rateuser"=>$ratedBy[1]
        )));
    }

    function unload($pch){
        $pch->callPlugin("RabbitMQ::close",$this->rabbitChan);
    }

    function diffToText($stars,$demonDiff,$isEpic,$isFeatured){
        switch($stars){
            case 1:
                $diff="auto";
                break;
            case 2:
                $diff="easy";
                break;
            case 3:
                $diff="normal";
                break;
            case 4:
            case 5:
                $diff="hard";
                break;
            case 6:
            case 7:
                $diff="harder";
                break;
            case 8:
            case 9:
                $diff="insane";
                break;
            case 10:
                $diff="demon";
                switch($demonDiff){
                    case 3:
                        $diff.="-easy";
                        break;
                    case 4:
                        $diff.="-medium";
                        break;
                    case 5:
                        $diff.="-insane";
                        break;
                    case 6:
                        $diff.="-extreme";
                    case 0:
                    default:
                        $diff.="-hard";
                }
                break;
            default:
                $diff="unrated";
        }
        if($isEpic) return $diff."-epic";
        if($isFeatured) return $diff."-featured";
        return $diff;
    }
}
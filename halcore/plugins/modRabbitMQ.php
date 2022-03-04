<?php
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/../../conf/mainconfig.php";
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ{
    public $DEPS=array();
    public AMQPStreamConnection $conn;
    function connChan(){
        $this->conn=new AMQPStreamConnection("localhost",5672,"gdps_".SRV_ID,SRV_KEY);
//        $this->conn=new AMQPStreamConnection("207.180.238.155",5672,"gdps_001A","1rbYI8pcOo7MJk2R");
        return $this->conn->channel();
    }
    function close($chan){
        $chan->close();
        $this->conn->close();
        return null;
    }
    function publishText($channel,$queue,$text){
        $msg=new AMQPMessage($text);
        $channel->basic_publish($msg,'',"bot_".SRV_ID);
    }
}


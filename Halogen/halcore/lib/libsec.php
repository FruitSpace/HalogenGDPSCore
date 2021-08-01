<?php

class LibSec{
	public $iplist;

	function __construct(){
		$this->loadIPBlacklist();
	}

	function loadIPBlacklist(){
		$this->iplist = explode("\n", file_get_contents(__DIR__ . "/../../files/ban_ip.txt"));
	}

	function saveIPBlacklist(){
		file_put_contents(__DIR__ . "/../../files/ban_ip.txt", implode("\n",$this->iplist));
	}

	function banIP($ip){
		if(!$this->isIPBlacklisted($ip)) array_push($ip);
	}

	function unbanIP($ip){
		if($this->isIPBlacklisted($ip)) unset($this->iplist[array_search($ip,$this->iplist)]);
	}

	function isIPBlacklisted($ip){
		return in_array($ip,$this->iplist);
	}
}
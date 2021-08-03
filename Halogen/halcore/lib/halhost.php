<?php
require_once __DIR__."/../../conf/limits.php";
require_once __DIR__."/../../conf/mainconfig.php";
require_once __DIR__."/logger.php";

define("HALHOST_TRIGGER_TYPE_USERS_LIMIT","ulimit");
define("HALHOST_TRIGGER_TYPE_USERS_OVERFLOW","uoverflow");
define("HALHOST_TRIGGER_TYPE_LEVELS","loverflow");
define("HALHOST_TRIGGER_TYPE_COMMENTS","coverflow");
define("HALHOST_TRIGGER_TYPE_POSTS","poverflow");

function checkRegister($userCount){
	if ($userCount==HALHOST_USERS_TRIGGER){
		callHalogenTrigger(HALHOST_TRIGGER_TYPE_USERS_LIMIT);
	}
	if ($userCount>=HALHOST_MAX_USERS){
		callHalogenTrigger(HALHOST_TRIGGER_TYPE_USERS_OVERFLOW);
		return 0;
	}
	return 1;
}

function checkLevels($lvlCount){
	if($lvlCount>=HALHOST_MAX_LEVELS){
		callHalogenTrigger(HALHOST_TRIGGER_TYPE_LEVELS);
		return 0;
	}
	return 1;
}

function checkComments($commCount){
	if($commCount>=HALHOST_MAX_COMMENTS){
		callHalogenTrigger(HALHOST_TRIGGER_TYPE_COMMENTS);
		return 0;
	}
	return 1;
}

function checkPosts($postCount){
	if($postCount>=HALHOST_MAX_POSTS){
		callHalogenTrigger(HALHOST_TRIGGER_TYPE_POSTS);
		return 0;
	}
	return 1;
}

function callHalogenTrigger($trigger){
	$postfix="?gdps_id=".SRV_ID."&gdps_key=".SRV_KEY."&trigger=$trigger";
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, HALHOST_TRIGGER_URL.$postfix);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$chout=curl_exec($ch);
	if($chout!="ok"){
		$former="Could not reach trigger for server ".SRV_ID."  trigger $trigger\n\tOUTPUT: $chout";
		err_handle("HAL_LIMIT","error", $former, false);
	}
}
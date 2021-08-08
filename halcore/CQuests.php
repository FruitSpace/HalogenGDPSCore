<?php

function getDaily(DBManagement $db){
	$req=$db->query("SELECT id, lvl_id from quests WHERE type=0 AND  ORDER BY timeExpire DESC LIMIT 1")
}
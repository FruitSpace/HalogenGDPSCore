<?php
require_once __DIR__."/lib/DBManagement.php";
require_once __DIR__."/CProtect.php";
$dbm=new DBManagement(true);
$protect=new CProtect($dbm);

//recalculate
$protect->resetUserLimits();
$protect->fillLevelModel();
$dbm->query("ALTER TABLE levels ADD COLUMN collab TEXT NOT NULL DEFAULT ''");

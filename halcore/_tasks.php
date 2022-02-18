<?php
require_once __DIR__."/lib/DBManagement.php";
require_once __DIR__."/CProtect.php";
require_once __DIR__."/CMusic.php";
$dbm=new DBManagement(true);
$protect=new CProtect($dbm);
$music=new CMusic($dbm);

//recalculate
$protect->resetUserLimits();
$protect->fillLevelModel();
$music->countDownloads();

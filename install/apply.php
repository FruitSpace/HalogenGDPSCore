<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
function mkresp($status, $data) {
    die(json_encode(array(
        "status"=>$status,
        "data"=>$data
    )));
}
function setupDB($host, $port, $uname, $pass, $dbname) {
    $port = intval($port);
    $host = htmlentities($host, ENT_COMPAT);
    $uname = htmlentities($uname, ENT_COMPAT);
    $pass = htmlentities($pass, ENT_COMPAT);
    $dbname = htmlentities($dbname, ENT_COMPAT);
    if ($port==0) mkresp("error","Invalid port");
    $conf = '<?php
define("DB_SERVER", "'.$host.'");
define("DB_PORT", '.$port.');
define("DB_USER", "'.$uname.'");
define("DB_PASS", "'.$pass.'");
define("DB_NAME", "'.$dbname.'");
';
    file_put_contents(__DIR__."/../conf/dbconfig.php", $conf);
    include __DIR__."/../conf/dbconfig.php";
    $mdb=new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($mdb->connect_errno) mkresp("error", $mdb->connect_error);

    require_once __DIR__."/../halcore/lib/DBManagement.php";
    $dbm = new DBManagement(true);
    //@m41denx: Мне влом отлавливать ошибки. Плиз добавьте тут цикл с проверкой успешного успеха (https://www.php.net/manual/en/mysqli.multi-query.php)
    $dbm->getDB()->multi_query(file_get_contents(__DIR__."/../database.sql"));

    mkresp("ok","Success");
}

function setupChests($orbs_min, $orbs_max, $diamond_min, $diamond_max, $keys_min, $keys_max, $timeoutz) {

/*define("DB_SERVER", "'.$host.'");
define("DB_PORT", '.$port.');
define("DB_USER", "'.$uname.'");
define("DB_PASS", "'.$pass.'");
define("DB_NAME", "'.$dbname.'");
*/
    $orbs_min_small = intval($orbs_min);
    $orbs_max_small = intval($orbs_max);
    $diamond_min_small = intval($diamond_min);
    $diamond_max_small = intval($diamond_max);
    $keys_min_small = intval($keys_min);
    $keys_max_small = intval($keys_max);
    $timeoutx = intval($timeoutz);

    $conf2 = '<?php
    
            //----- SMALL CHEST -----
        define("CHEST_SMALL_ORBS_MIN", '.$orbs_min_small.');
        define("CHEST_SMALL_ORBS_MAX", '.$orbs_max_small.');
        define("CHEST_SMALL_DIAMONDS_MIN", '.$diamond_min_small.');
        define("CHEST_SMALL_DIAMONDS_MAX", '.$diamond_max_small.');
        define("CHEST_SMALL_SHARDS_MIN", 1);
        define("CHEST_SMALL_SHARDS_MAX", 6);
        define("CHEST_SMALL_KEYS_MIN", '.$keys_min_small.');
        define("CHEST_SMALL_KEYS_MAX", '.$keys_max_small.');
        define("CHEST_SMALL_WAIT", '.$timeoutx.'); //sec

        //----- BIG CHEST -----
        define("CHEST_BIG_ORBS_MIN", 2000);
        define("CHEST_BIG_ORBS_MAX", 4000);
        define("CHEST_BIG_DIAMONDS_MIN", 20);
        define("CHEST_BIG_DIAMONDS_MAX", 100);
        define("CHEST_BIG_SHARDS_MIN", 1);
        define("CHEST_BIG_SHARDS_MAX", 6);
        define("CHEST_BIG_KEYS_MIN",1);
        define("CHEST_BIG_KEYS_MAX",6);
        define("CHEST_BIG_WAIT", 14400); //sec
    
    
    ';
    file_put_contents(__DIR__."/../conf/chests.php", $conf2);
    mkresp("ok","Success");
}





switch ($_GET['a']) {
    default:
        mkresp("error", "Invalid action");
    case "initdb":
        setupDB($_POST['host'],$_POST['port'],$_POST['uname'],$_POST['pass'],$_POST['dbname']);
    case "chestconf":
        setupChests($_POST['orbs_min'],$_POST["orbs_max"],$_POST['diamond_min'],$_POST['diamond_max'],$_POST['keys_min'],$_POST['keys_max'],$_POST['timeout_small']);
}
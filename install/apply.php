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




switch ($_GET['a']) {
    default:
        mkresp("error", "Invalid action");
    case "initdb":
        setupDB($_POST['host'],$_POST['port'],$_POST['uname'],$_POST['pass'],$_POST['dbname']);
}
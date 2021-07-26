<?php
//include "incl/mods/requestUserAccess.php";
$epName="requestUserAccess.php";
$err=file_get_contents("../errorLog.txt")."[".date("d/m/Y H:i")." | ENDPOINT] ".$epName." Disabled endpoint was reached with GET: \n\t";
$err=$err.json_encode($_GET)."\nPOST:\n\t".json_encode($_POST)."\n\n";
file_put_contents("../errorLog.txt",$err);
?>
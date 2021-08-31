<?php
if(!isset($_POST['secret'])) die();
$data = array('songID' => $_POST['songID'], 'secret' => 'Wmfd2893gb7');
$options = array(
	'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
		'content' => http_build_query($data),
	),
);
$context  = stream_context_create($options);
$result = file_get_contents("http://www.boomlings.com/database/getGJSongInfo.php", false, $context);
echo $result;

//Yep, we are just transferring everything from original GD because who cares when version is only 1.0
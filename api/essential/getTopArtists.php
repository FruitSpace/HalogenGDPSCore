<?php
if(!isset($_POST['secret'])) die();
//We just redirect everything to robtob's site/ Who cares
$request = "page=".(($_POST['page']."0")*2)."&secret=Wmfd2893gb7";
parse_str($request, $post);
// post
$ch = curl_init("http://boomlings.com/database/getGJTopArtists.php");
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$robsult = curl_exec($ch);
curl_close($ch);
echo $robsult;

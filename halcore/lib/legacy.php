<?php
//Legacy code from CvoltonGDPS and essentials

function exploitPatch_remove($string) {
	return trim(explode(")", str_replace("\0", "", explode("#", explode("~", explode("|", explode(":", trim(htmlspecialchars($string,ENT_QUOTES)))[0])[0])[0])[0]))[0]);
}

function doXOR($text, $key){
	$key = array_map('ord', str_split($key));
	$plaintext = array_map('ord', str_split($text));
	$keysize = count($key);
	$input_size = count($plaintext);
	$result = "";
	for ($i = 0; $i < $input_size; $i++)
		$result .= chr($plaintext[$i] ^ $key[$i % $keysize]);
	return $result;
}

function getDateAgo($date){
	$diff=time()-$date;
	if($diff<60) return "$diff seconds";
	if($diff<3600) return (int)($diff/60)." minutes";
	if($diff<86400) return (int)($diff/3600)." hours";
	if($diff<604800) return (int)($diff/86400)." days";
	if($diff<604800*4) return (int)($diff/604800)." weeks";
	if($diff<604800*4*12) return (int)($diff/(604800*4))." months";
	return (int)($diff/(604800*4*12))." years";
}
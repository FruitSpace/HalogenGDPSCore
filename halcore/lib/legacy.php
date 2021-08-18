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

function genhash_genSolo($levelstring) {
	$hash = "aaaaa";
	$len = strlen($levelstring);
	$divided = intval($len/40);
	$p = 0;
	for($k = 0; $k < $len ; $k= $k+$divided){
		if($p > 39) break;
		$hash[$p] = $levelstring[$k];
		$p++;
	}
	return sha1($hash . "xI25fpAapCQg");
}

function genhash_genSolo2($lvlsmultistring) {
	return sha1($lvlsmultistring . "xI25fpAapCQg");
}

function genhash_genSolo3($lvlsmultistring) {
	return sha1($lvlsmultistring . "oC36fpYaPtdg");
}

function genhash_genSolo4($lvlsmultistring){
	return sha1($lvlsmultistring . "pC26fpYaQCtg");
}

function genhash_genPack($lvlsmultistring, $db) {
	$lvlsarray = explode(",", $lvlsmultistring);
	$hash = "";
	foreach($lvlsarray as $id){
		$req=$db->query("SELECT packCoins, packStars FROM levelpacks WHERE id=$id")->fetch_assoc();
		$hash.=$id[0].$id[strlen($id)-1].$req["packStars"].$req["packCoins"];
	}
	return sha1($hash . "xI25fpAapCQg");
}

function genhash_genSeed2noXor($levelstring) {
	$hash = "aaaaa";
	$len = strlen($levelstring);
	$divided = intval($len/50);
	$p = 0;
	for($k = 0; $k < $len ; $k= $k+$divided){
		if($p > 49) break;
		$hash[$p] = $levelstring[$k];
		$p++;
	}
	$hash = sha1($hash."xI25fpAapCQg");
	return $hash;
}
<?php
//Legacy code from CvoltonGDPS

function exploitPatch_remove($string) {
	return trim(explode(")", str_replace("\0", "", explode("#", explode("~", explode("|", explode(":", trim(htmlspecialchars($string,ENT_QUOTES)))[0])[0])[0])[0]))[0]);
}
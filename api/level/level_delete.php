<?php
require_once __DIR__ . "/../../halcore/lib/DBManagement.php";
require_once __DIR__ . "/../../halcore/CLevel.php";
require_once __DIR__ . "/../../halcore/lib/legacy.php";
require_once __DIR__ . "/../../halcore/lib/libsec.php";
require_once __DIR__ . "/../../halcore/CHalogen.php";
require_once __DIR__.'/../../halcore/plugins/autoload.php';

$ip=$_SERVER['HTTP_X_REAL_IP'];
$lsec=new LibSec();
if ($lsec->isIPBlacklisted($ip)){
	header('HTTP/1.1 403 Forbidden');
	die('This IP is banned for security reasons');
}
if(isset($_POST['accountID']) and isset($_POST['levelID']) and isset($_POST['gjp']) and $_POST['accountID']!=""
	and $_POST['levelID']!="" and $_POST['gjp']!=""){
	$uid=(int)$_POST['accountID'];
	$id=(int)$_POST['levelID'];
	$gjp=exploitPatch_remove($_POST['gjp']);
	$dbm=new DBManagement();
	if($lsec->verifySession($dbm, $uid, $ip, $gjp)) {
		$cl=new CLevel($dbm);
		$cl->id=$id;
		if($cl->isOwnedBy($uid)>0){
            $ch=new CHalogen($dbm);
            $cl->loadMain();
            $cl->loadParams();
			$cl->deleteLevel();
			$cl->recalculateCPoints($cl->uid);
            $ch->onLevel();
			require_once __DIR__."/../../halcore/lib/actions.php";
			require_once __DIR__."/../../halcore/CAccount.php";
			$acc=new CAccount($dbm);
			$acc->uid=$uid;
			$acc->loadAuth();
			registerAction(ACTION_LEVEL_DELETE,$uid,$id,array("uname"=>$acc->uname,"type"=>"Delete (Owner)"),$dbm);
            if(!$cl->isUnlisted) {
                require_once __DIR__ . "/../../halcore/CAccount.php";
                $plugCore = new PluginCore();
                $plugCore->preInit();
                $acc = new CAccount($dbm);
                $plugCore->onLevelDelete($cl->id, $cl->name, $acc->getUnameByUID($cl->uid));
                $plugCore->unload();
            }
			echo "1";
		}else{
			echo "-1";
		}
	}else{
		echo "-1";
	}
	$r=0;
}else{
	echo "-1";
	$r=1;
}
if(LOG_ENDPOINT_ACCESS){
	$former="$ip accessed endpoint ".__FILE__." ::with".($r==1?"out":"")." auth data";
	err_handle("ENDPOINT","verbose",$former);
}
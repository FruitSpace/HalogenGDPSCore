<?php

define("CLEVELFILTER_MOSTLIKED",700);
define("CLEVELFILTER_MOSTDOWNLOADED",701);
define("CLEVELFILTER_TRENDING",702);
define("CLEVELFILTER_LATEST",703);
define("CLEVELFILTER_MAGIC",704);
define("CLEVELFILTER_HALL",705);

class CLevelFilter{
	public $db; //!Remove dbm
	public $count;

	/*
	 * --- [PARAMS] Object ---
	 * + (s) sterm - search term (used with other params to specify exact usage)
	 * + (s) diff - difficulties array. If doesnt exist then all diffs | Ex: array(10,20,30)
	 * + (b) isDemon - should we look for demon difficulty or not
	 * + (i) demonDiff - demon difficulty (auto sorted)
	 * + (i) length - level length array (if not specified -> all) | Ex: array(0,1,4)
	 * + (b) completed - if not set than all, else only completed/uncompleted
	 * + (s) completedLevels - if completed is set then list of comp/uncomp is sent
	 * + (b) isFeatured - obv
	 * + (b) isOrig - where origid=0
	 * + (b) is2p - straightforward
	 * + (b) coins - should we search coins only
	 * + (b) isEpic - also obv
	 * + (b) star - if not set then all, else star/nostar
	 * + (i) songid - official song id or custom song id
	 * + (b) songCustom - if set then songid is custom song
	 *
	 * !! Demon overrides diff
	*/

	function __construct($db){
		$this->db=$db;
	}

	function generateQueryString($params){
		$whereq="";
		if(isset($params['isDemon'])){
			if(isset($params['demonDiff'])){
				$whereq.=" AND demonDifficulty=".$params['demonDiff'];
			}else{
				$whereq.=" AND demonDifficulty>0";
			}
		}else{
			if(isset($params['diff'])) {
				$whereq .= " AND difficulty IN (" . $params['diff'] . ")";
			}
		} //Difficulty
		if(isset($params['length'])){
			$whereq.=" AND length IN (".$params['length'].")";
		} //length
		if(isset($params['completed'])){
			$whereq.=" AND id".($params['completed']===false?" NOT":"")." IN (".$params['completedLevels'].")";
		} //completed/uncompleted stuff
		if(isset($params['isFeatured'])) $whereq.=" AND isFeatured=1"; //Featured
		if(isset($params['is2p'])) $whereq.=" AND is2p=1"; //2 Players
		if(isset($params['isOrig'])) $whereq.=" AND original_id=0"; //Original only
		if(isset($params['isEpic'])) $whereq.=" AND isEpic=1"; //Epic
		if(isset($params['coins'])) $whereq.=" AND coins>0"; //coin stuff
		if(isset($params['star'])){
			if($params['star']===false){
				$whereq.=" AND starsGot=0";
			}else{
				$whereq.=" AND starsGot>0";
			}
		} //starred or not
		if(isset($params['songid'])){
			if(isset($params['songCustom'])){
				$whereq.=" AND song_id=".$params['songid'];
			}else{
				$whereq.=" AND track_id=".$params['songid'];
			}
		} //Song NG/Classic stuff
		return $whereq;
	}

	function searchLevels(int $page,$params, int $type=CLEVELFILTER_MOSTLIKED){
		$suffix=$this->generateQueryString($params);
		$unlisted=true;
		$query="SELECT id FROM levels WHERE versionGame<=?";
		$cntquery="SELECT count(*) as cnt FROM levels WHERE versionGame<=?";
		switch($type){
			case CLEVELFILTER_MOSTLIKED:
				$orderitem="likes DESC, downloads DESC";
				break;
			case CLEVELFILTER_MOSTDOWNLOADED:
				$orderitem="downloads DESC, likes DESC";
				break;
			case CLEVELFILTER_TRENDING:
				$uploadDate = date("Y-m-d H:i:s",time()-(7*24*60*60));
				$query.=" AND uploadDate>'$uploadDate'";
				$orderitem="likes DESC, downloads DESC";
				break;
			case CLEVELFILTER_LATEST:
				$orderitem="uploadDate DESC, downloads DESC";
				break;
			case CLEVELFILTER_MAGIC:
				$query.=" AND objects>9999 AND length>=3 AND original_id=0 AND starsGot>0";
				$orderitem="uploadDate DESC, downloads DESC";
				break;
			case CLEVELFILTER_HALL:
				$query.=" AND isEpic=1"; //!HALL OF FAME OVERRIDE
				$orderitem="likes DESC, downloads DESC";
				break;

		}
		$sortstr=" ORDER BY $orderitem LIMIT 10 OFFSET $page";
		if(isset($params['sterm'])){
			if(is_numeric($params['sterm'])){
				$req=$this->db->preparedQuery($query." AND id=?".$suffix.$sortstr,"ii",
					$params['versionGame'],$params['sterm']);
				$this->count=$this->db->preparedQuery($cntquery." AND id=?".$suffix,"ii",
					$params['versionGame'],$params['sterm'])->fetch_assoc()['cnt'];
			}else{
				$req=$this->db->preparedQuery($query." AND (id=? OR name LIKE ?) AND isUnlisted=0".$suffix.$sortstr,"iis",
					$params['versionGame'],$params['sterm'],"%".$params['sterm']."%");
				$this->count=$this->db->preparedQuery($cntquery." AND (id=? OR name LIKE ?) AND isUnlisted=0".$suffix,"iis",
					$params['versionGame'],$params['sterm'],"%".$params['sterm']."%")->fetch_assoc()['cnt'];
			}
		}else{
			$req=$this->db->preparedQuery($query." AND isUnlisted=0".$suffix.$sortstr,"i",$params['versionGame']);
			$this->count=$this->db->preparedQuery($cntquery." AND isUnlisted=0".$suffix,"i",$params['versionGame'])->fetch_assoc()['cnt'];
		}
		if($this->db->isEmpty($req)) return array();
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$lvls=array();
		foreach($reqm as $sreq){
			array_push($lvls,$sreq['id']);
		}
		return $lvls;
	}

	function searchUserLevels(int $page,$params, bool $followmode=false){
		$suffix=$this->generateQueryString($params);
		$query="SELECT id FROM levels WHERE versionGame<=?";
		$cntquery="SELECT count(*) as cnt FROM levels WHERE versionGame<=?";
		$sortstr=" ORDER BY likes DESC LIMIT 10 OFFSET $page";
		if(isset($params['sterm']) and $followmode===false){
			if(!is_numeric($params['sterm'])) $query.=" AND isUnlisted=0";
			$req=$this->db->preparedQuery($query." AND uid=?".$suffix.$sortstr,"ii", $params['versionGame'],$params['sterm']);
			$this->count=$this->db->preparedQuery($cntquery." AND uid=?".$suffix,"ii", $params['versionGame'],$params['sterm'])->fetch_assoc()['cnt'];
		}elseif($followmode==true){
			if(isset($params['sterm'])) {
				if(!is_numeric($params['sterm'])) $query.=" AND isUnlisted=0";
				$req = $this->db->preparedQuery($query." AND uid IN (".$params['followList'].") AND (id=? OR name LIKE ?)" . $suffix . $sortstr, "ii", $params['versionGame'],$params['sterm'],"%".$params['sterm']."%");
				$this->count=$this->db->preparedQuery($cntquery." AND uid IN (".$params['followList'].") AND (id=? OR name LIKE ?)" . $suffix, "ii", $params['versionGame'],$params['sterm'],"%".$params['sterm']."%")->fetch_assoc()['cnt'];
			}else{
				$req = $this->db->preparedQuery($query." AND isUnlisted=0 AND uid IN (".$params['followList'].")".$suffix . $sortstr, "i", $params['versionGame']);
				$this->count = $this->db->preparedQuery($cntquery." AND isUnlisted=0 AND uid IN (".$params['followList'].")".$suffix, "i", $params['versionGame'])->fetch_assoc()['cnt'];

			}
		}else{
			$req=$this->db->preparedQuery($query.$suffix.$sortstr,"i",$params['versionGame']);
			$this->count=$this->db->preparedQuery($cntquery.$suffix,"i",$params['versionGame'])->fetch_assoc()['cnt'];
		}
		if($this->db->isEmpty($req)) return array();
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$lvls=array();
		foreach($reqm as $sreq){
			array_push($lvls,$sreq['id']);
		}
		return $lvls;
	}

	function searchListLevels(int $page,$params){
		$query="SELECT id FROM levels WHERE versionGame<=?";
		$cntquery="SELECT count(*) as cnt FROM levels WHERE versionGame<=?";
		$sortstr=" LIMIT 10 OFFSET $page";
		if(isset($params['sterm'])){
			$luid=" AND id IN (".$params['sterm'].")";
			$req=$this->db->preparedQuery($query.$luid.$sortstr,"i", $params['versionGame']);
			$this->count=$this->db->preparedQuery($cntquery.$luid,"i", $params['versionGame'])->fetch_assoc()['cnt'];
		}else{
			return array();
		}
		if($this->db->isEmpty($req)) return array();
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$lvls=array();
		foreach($reqm as $sreq){
			array_push($lvls,$sreq['id']);
		}
		return $lvls;
	}

	function getGauntletString(){
		require_once __DIR__."/lib/legacy.php";
		$req=$this->db->query("SELECT packName,levels FROM levelpacks WHERE packType=1 ORDER BY CAST(packName as int) ASC");
		if($this->db->isEmpty($req)) return "-2";
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$gau="";
		$hashstr="";
		foreach ($reqm as $sreq){
			$lvls=explode(",",$sreq['levels']);
			if(count($lvls)!=5) continue;
			if(!is_numeric($sreq['packName'])) continue;
			$gau.="1:".$sreq['packName'].":3:".$sreq['levels']."|";
			$hashstr.=$sreq['packName'].$sreq['levels'];
		}
		if(empty($gau)) return "-2";
		return substr($gau,0,-1)."#".genhash_genSolo2($hashstr);
	}

	function getGauntletLevels(int $gau){
		$req=$this->db->preparedQuery("SELECT levels FROM levelpacks WHERE packType=1 AND packName=?","s",$gau);
		if($this->db->isEmpty($req)) array();
		$lvls=explode(",",$req->fetch_assoc()['levels']);
		return array($lvls[0],$lvls[1],$lvls[2],$lvls[3],$lvls[4]);
	}

	function countMapPacks(){
		return $this->db->query("SELECT count(*) as cnt FROM levelpacks WHERE packType=0")->fetch_assoc()['cnt'];
	}

	function getMapPackString(int $page){
		require_once __DIR__."/lib/legacy.php";
		$req=$this->db->query("SELECT * FROM levelpacks WHERE packType=0 LIMIT 10 OFFSET $page");
		if($this->db->isEmpty($req)) return "-2";
		$reqm=array();
		while($res=$req->fetch_assoc()) $reqm[]=$res;
		$pack="";
		$hashstr="";
		foreach ($reqm as $sreq){
			$lvls=explode(",",$sreq['levels']);
			if(count($lvls)!=3) continue;
			$pack.="1:".$sreq['id'].":2:".$sreq['packName'].":3:".$sreq['levels'].":4:".$sreq['packStars'].":5:".$sreq['packCoins'];
			$pack.=":6:".$sreq['packDifficulty'].":7:".$sreq['packColor'].":8:".$sreq['packColor']."|";
			$hashstr.=((string)$sreq['id'])[0].((string)$sreq['id'])[strlen(((string)$sreq['id']))-1].$sreq['packStars'].$sreq['packCoins'];
		}
		if(empty($pack)) return "-2";
		return substr($pack,0,-1)."#".$this->countMapPacks().":$page:10"."#".genhash_genSolo2($hashstr);
	}
}
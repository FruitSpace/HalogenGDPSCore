<?php

define("CLEVELFILTER_MOSTLIKED",700);
define("CLEVELFILTER_MOSTDOWNLOADED",701);
define("CLEVELFILTER_TRENDING",702);
define("CLEVELFILTER_LATEST",703);
define("CLEVELFILTER_MAGIC",704);
define("CLEVELFILTER_HALL",705);

class CLevelFilter{
	public $db; //!Remove dbm

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
	 * + (b) coins - if not set then all, else only coins/nocoins
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
		if(isset($params['coins'])){
			if($params['coins']===false){
				$whereq.=" AND coins=0";
			}else{
				$whereq.=" AND coins>0";
			}
		} //coin stuff
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
		err_handle("TMP","verbose",json_encode($params));
		return $whereq;
	}

	function searchLevels(int $page,$params, int $type=CLEVELFILTER_MOSTLIKED){
		$suffix=$this->generateQueryString($params);
		$query="SELECT id FROM levels WHERE versionGame<=? AND isUnlisted=0";
		switch($type){
			case CLEVELFILTER_MOSTLIKED:
				$orderitem="likes";
				break;
			case CLEVELFILTER_MOSTDOWNLOADED:
				$orderitem="downloads";
				break;
			case CLEVELFILTER_TRENDING:
				$uploadDate = date("Y-m-d H:i:s",time()-(7*24*60*60));
				$query.=" AND uploadDate>$uploadDate";
				$orderitem="likes";
				break;
			case CLEVELFILTER_LATEST:
				$orderitem="uploadDate";
				break;
			case CLEVELFILTER_MAGIC:
				$query.=" AND objects>9999 AND length>=3 AND original_id=0";
				$orderitem="uploadDate";
				break;
			case CLEVELFILTER_HALL:
				$query.=" AND isHall=1";
				$orderitem="likes";
				break;

		}
		$sortstr=" ORDER BY $orderitem DESC LIMIT 10 OFFSET $page";
		if(isset($params['sterm'])){
			$req=$this->db->preparedQuery($query." AND (id=? OR name LIKE ?)".$suffix.$sortstr,"iis",
				$params['versionGame'],$params['sterm'],"%".$params['sterm']."%");
		}else{
			$req=$this->db->preparedQuery($query.$suffix.$sortstr,"i",$params['versionGame']);
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
		$query="SELECT id FROM levels WHERE versionGame<=? AND isUnlisted=0";
		$sortstr=" ORDER BY likes DESC LIMIT 10 OFFSET $page";
		if(isset($params['sterm']) and $followmode===false){
			$req=$this->db->preparedQuery($query." AND uid=?".$suffix.$sortstr,"ii", $params['versionGame'],$params['sterm']);
		}elseif($followmode==true){
			if(isset($params['sterm'])) {
				$req = $this->db->preparedQuery($query . " AND uid IN (" . $params['followList'] . ") AND (id=? OR name LIKE ?)" . $suffix . $sortstr, "ii", $params['versionGame'],$params['sterm'],"%".$params['sterm']."%");
			}else{
				$req = $this->db->preparedQuery($query . " AND uid IN (" . $params['followList'] . $suffix . $sortstr, "ii", $params['versionGame']);
			}
		}else{
			$req=$this->db->preparedQuery($query.$suffix.$sortstr,"i",$params['versionGame']);
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
		$query="SELECT id FROM levels WHERE versionGame<=? AND isUnlisted=0";
		$sortstr=" LIMIT 10 OFFSET $page";
		if(isset($params['sterm'])){
			$luid=" AND id IN (".$params['sterm'].")";
			$req=$this->db->preparedQuery($query.$luid.$sortstr,"ii",
				$params['versionGame'],$params['sterm']);
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
}
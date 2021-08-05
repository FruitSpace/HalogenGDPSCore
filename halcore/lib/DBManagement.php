<?php
require_once __DIR__ . "/../../conf/dbconfig.php";
require_once __DIR__ . "/logger.php";

class DBManagement{
    private $db;

    function __construct(){
        $this->db=new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if($this->db->connect_errno){
            $former="Connection error #".$this->db->connect_errno."\n\tMySQLi Thrown: ".$this->db->connect_error;
            err_handle("DBM","fatal", $former);
        }
    }

    function getDB(){
        return $this->db;
    }

    function preparedQuery($query,$datatypes, ...$vars){
        $req=$this->db->prepare($query);
        if(!$req){
			$former="Query preparation error #".$this->db->errno."\n\tMySQLi Thrown: ".$this->db->error."\n\tQUERY: $query";
			err_handle("DBM","fatal", $former);
		}
        call_user_func_array(array($req,"bind_param"),array_merge((array)$datatypes,$vars));
        if(!($req->execute())){
			$former="Query error #".$this->db->errno."\n\tMySQLi Thrown: ".$this->db->error;
			err_handle("DBM","fatal", $former);
		}
		$r=$req->get_result();
		if(LOG_DB_REQUESTS){
			$former="Prepared query: [$query]\n\tDatatypes: [$datatypes]\n\tBind params: [".json_encode($vars)."]";
			if(LOG_DB_RESPONSES){
				$ra=(($this->isEmpty($r) or $r==false)?"EMPTY":($r->fetch_assoc()));
				if($r) $r->data_seek(0);
				$former.="\n\tResponse: ".json_encode($ra);
			}
			err_handle("DBM", "verbose",$former);
		}
		return $r;
    }

    function query($query){
    	if(!($result=$this->db->query($query))){
			$former="Direct query error #".$this->db->errno."\n\tMySQLi Thrown: ".$this->db->error."\nQUERY: $query";
			err_handle("DBM","fatal", $former);
		}
    	if(LOG_DB_REQUESTS){
    		$former="Direct query: [$query]";
			$ra=(($this->isEmpty($result) or $result==false)?"EMPTY":($result->fetch_assoc()));
			if($result) $result->data_seek(0);
			$former.="\n\tResponse: ".json_encode($ra);
    		err_handle("DBM", "verbose",$former);
		}
    	return $result;
	}

    function isEmpty($req){
    	return $req->num_rows===0;
	}
}

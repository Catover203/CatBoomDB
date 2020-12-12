<?php
namespace CatBoom\CatBoomDB;
class CatBoomDB{
	function __construct($dbname = ''){
		if($this->is_error($dbname)){
			$this->error('construct', 'construct missing data');
			return false;
		}else{
			if(!opendir('CatBoomDB')){
				mkdir('CatBoomDB');
				$this->DBDIR = false;
			}else{
				$this->DBDIR = true;
			}
			if(file_exists('CatBoomDB/'.$dbname.'.json')){
			$this->db_ext = true;
			}else{
				$this->db_ext = false;
			}
			if(!$this->db_ext){
				$this->new_db($dbname);
			}
		}
		$this->dbname = $dbname;
	}
	function new_db($dbname = ''){
		if($this->is_error($dbname)){
			$this->error('newdb', 'missing database name');
			return false;
		}else{
			if($this->db_ext){
				return false;
			}else{
				$open = fopen('CatBoomDB/'.$dbname.'.json', 'w');
				fclose($open);
				return true;
			}
		}
	}
	function insert($name = '', $data = ''){
		$dbname = $this->dbname;
		if($this->is_error($dbname) && isset($name)){
			$this->error('insert', 'missing database or data name');
			return false;
		}else{
			$db_contents = file_get_contents('CatBoomDB/'.$dbname.'.json');
			$json_decode = json_decode($db_contents, true);
			$json_data = $json_decode;
			$insert = [];
			$insert[$name] = base64_encode($data);
			if(!empty($json_decode)){
				foreach($json_decode as $dbdata){
					$json_name = array_search($dbdata, $json_data);
					$insert[$json_name] = $dbdata;
				}
			}
			$json_encode = json_encode($insert);
			$open = fopen('CatBoomDB/'.$dbname.'.json', 'w');
			fwrite($open, $json_encode);
			fclose($open);
		}
	}
	function fetch_assoc(){
		$dbname = $this->dbname;
		if($this->is_error($dbname)){
			$this->error('fetch', 'missing database name');
			return false;
		}else{
			$db_contents = file_get_contents('CatBoomDB/'.$dbname.'.json');
			$json_decode = json_decode($db_contents, true);
			$json_data = $json_decode;
			foreach($json_decode as $dbdata){
				$json_name = array_search($dbdata, $json_data);
				$fetch[$json_name] = base64_decode($dbdata);
			}
			return $fetch;
		}
	}
	function fetch_obj(){
		$dbname = $this->dbname;
		if($this->is_error($dbname)){
			$this->error('fetch', 'missing database name');
			return false;
		}else{
			include('CatBoomDataBase.module.php');
			$fetch = use_class();
			$db_contents = file_get_contents('CatBoomDB/'.$dbname.'.json');
			$json_decode = json_decode($db_contents, true);
			$json_data = $json_decode;
			foreach($json_decode as $dbdata){
				$json_name = array_search($dbdata, $json_data);
				$fetch->$json_name = base64_decode($dbdata);
			}
			return $fetch;
		}
	}
	function is_error($dbname){
		if(isset($dbname) && !empty($dbname)){
			return false;
		}else{
			return true;
		}
	}
	function error($name = '', $reason = ''){
		if($name == 'fetch'){
			echo '<p><b>CatBoomDB Error</b>: Can\'t fetch data because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>';
		}elseif($name == 'insert'){
			echo '<p><b>CatBoomDB Error</b>: Can\'t insert data because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>';
		}elseif($name == 'newdb'){
			echo '<p><b>CatBoomDB Error</b>: Can\'t create new database because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>';
		}elseif($name == 'construct'){
			echo '<p><b>CatBoomDB Error</b>: CatBoomDB construct error because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>';
		}
	}
}
?>
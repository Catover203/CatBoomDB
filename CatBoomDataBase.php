<?php
class CatBoomDB{
	function __construct($dbname = 'default'){
		error_reporting(0); // Disable error from php
		if($this->is_error($dbname)){ // Check is empty datbase name
			$this->error('construct', 'construct missing data'); // Print error to screen
			return false; // Unsuccess, return false
		}else{
			if(!opendir('CatBoomDB')){ // Check is exists DB Dir
				// Create new directory and chmod this
				mkdir('CatBoomDB');
				chmod('CatBoomDB', 0666);
			}
			if(file_exists('CatBoomDB/'.$dbname.'.json')){ // Check json db is exitst
				// Return true if exists
				$this->db_ext = true;
			}else{
				// Return false if not exists
				$this->db_ext = false;
			}
			if(!$this->db_ext){ // Create json db if not exists
				$this->new_db($dbname);
			}
		}
		$this->dbname = $dbname;
	}
	/* @name: DBCreate */
	function new_db($dbname = ''){
		if(empty($dbname)){ // Check database name is empty
			$dbname = $this->dbname; // Get database name
		}
		if($this->is_error($dbname)){ // Check is empty datbase name
			$this->error('newdb', 'missing database name'); // Print error to screen
			return false; // Unsuccess, return false
		}else{
			if(file_exists('CatBoomDB/'.$dbname.'.json')){ //if DB exist, return false
				return false;
			}else{
				 //if DB not exist, create new json db and return true
				$open = fopen('CatBoomDB/'.$dbname.'.json', 'w');
				fclose($open);
				return true;
			}
		}
	}
	/* @name: Select data from database */
	function select($name = '', $dbname = ''){
		if(empty($dbname)){ // Check database name is empty
			$dbname = $this->dbname; // Get database name
		}
		if($this->is_error($dbname) && !empty($name)){ // Check is empty data name and database name
			$this->error('select', 'missing database or data name'); // Print error
			return false; // Return false
		}else{
			$this->new_db($dbname); // Create a db
			$db_contents = file_get_contents('CatBoomDB/'.$dbname.'.json'); // Get a contents fom database
			$json_decode = json_decode($db_contents, true); // Decode json database and return as assoc
			if(isset($json_decode[$name])){ // Check is valid data name
				$select = base64_decode($json_decode[$name]); // value to return
			}else{
				$select = false; // value to return
			}
			return $select;
		}
	}
	/* @name: Insert data to database */
	function insert($name = '', $data = '', $dbname = ''){
		if(empty($dbname)){ // Check database name is empty
			$dbname = $this->dbname; // Get database name
		}
		if($this->is_error($dbname) && !empty($name)){ // Check is error
			$this->error('insert', 'missing database or data name'); // Print error
			return false; // Return false if unsuccess
		}else{
			$this->new_db($dbname); // Create a new Database
			$db_contents = file_get_contents('CatBoomDB/'.$dbname.'.json'); // Get json database contents
			$json_decode = json_decode($db_contents, true); // Decode json database and return as assoc
			$insert = []; // Set value to write as array
			$insert[$name] = base64_encode($data); // Encode input data
			if(!empty($json_decode)){ // Check json data is empty
				foreach($json_decode as $dbdata){
					$json_name = array_search($dbdata, $json_decode); // Search data name from value
					$insert[$json_name] = $dbdata; // Value to write
				}
			}
			$json_encode = json_encode($insert); // Encode json again
			$open = fopen('CatBoomDB/'.$dbname.'.json', 'w'); // Open database file
			fwrite($open, $json_encode); // Write data to file
			fclose($open); // Close file for secure
			return true; // Return true if success
		}
	}
	/* @name: Fetch data from database as assoc */
	function fetch_assoc($dbname = ''){
		if(empty($dbname)){ // Check database name is empty
			$dbname = $this->dbname; // Get database name
		}
		if($this->is_error($dbname)){ // Check is error
			$this->error('fetch', 'missing database name'); // Print error
			return false; // Return false if unsuccess
		}else{
			$this->new_db($dbname); // Create a new Database
			$fetch = []; // Set value to fetch as array
			$db_contents = file_get_contents('CatBoomDB/'.$dbname.'.json'); // Get json database contents
			$json_decode = json_decode($db_contents, true); // Decode json database and return as assoc
			foreach($json_decode as $dbdata){ // Foreach json data
				$json_name = array_search($dbdata, $json_decode); // Search data name from value
				$fetch[$json_name] = base64_decode($dbdata); // Decode value to fetch
			}
			return $fetch; // fetch data
		}
	}
	/* @name: Fetch data from database as obj */
	function fetch_obj($dbname = ''){
		if(empty($dbname)){ // Check database name is empty
			$dbname = $this->dbname; // Get database name
		}
		if($this->is_error($dbname)){ // Check is error
			$this->error('fetch', 'missing database name'); // Print error
			return false; // Return false if unsuccess
		}else{
			$this->new_db($dbname); // Create a new Database
			$fetch = new stdClass(); //Use stdClass to return obj
			$db_contents = file_get_contents('CatBoomDB/'.$dbname.'.json'); // Get json database contents
			$json_decode = json_decode($db_contents, true); // Decode json database and return as assoc
			foreach($json_decode as $dbdata){ // Foreach json data
				$json_name = array_search($dbdata, $json_decode);  // Search data name from value
				$fetch->$json_name = base64_decode($dbdata); // Decode value to fetch
			}
			return $fetch; // fetch data
		}
	}
	/* @name: Check is error */
	function is_error($dbname){
		if(isset($dbname) && !empty($dbname)){
			return false; // If not error, return false
		}else{
			return true; // If error, return true
		}
	}
	/* @name: Print error */
	function error($name = '', $reason = ''){
		if($name == 'fetch'){ // Check error name
			echo '<p><b>CatBoomDB Error</b>: Can\'t fetch data because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>'; // print fetch error
		}elseif($name == 'insert'){ // Check error name
			echo '<p><b>CatBoomDB Error</b>: Can\'t insert data because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>'; // print insert error
		}elseif($name == 'newdb'){ // Check error name
			echo '<p><b>CatBoomDB Error</b>: Can\'t create new database because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>'; // print create database error
		}elseif($name == 'construct'){ // Check error name
			echo '<p><b>CatBoomDB Error</b>: CatBoomDB construct error because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>'; // print construct error
		}elseif($name == 'select'){ // Check error name
			echo '<p><b>CatBoomDB Error</b>: Can\'t select data because because '.$reason.' in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>'; // print select data error
		}else{ // Check error name
			echo '<p><b>CatBoomDB Error</b>: An unknown error in <b>'.$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'],1).'</b></p>'; // print unknown error
		}
	}
}
?>

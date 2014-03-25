<?php

class mysql_class{
	private $conn_id = false;
	private $params = array();
	
	public function __construct($config = array()){

		$this->params = array(
				'hostname'	=> (isset($config['hostname'])) ? $config['hostname'] : '',
				'username'	=> (isset($config['username'])) ? $config['username'] : '',
				'password'	=> (isset($config['password'])) ? $config['password'] : '',
				'database'	=> (isset($config['database'])) ? $config['database'] : '',
				'charset'	=> (isset($config['char_set'])) ? $config['char_set'] : '',
			);

		$this->conn_id = @mysql_connect($this->params['hostname'], $this->params['username'], $this->params['password'], TRUE);
		@mysql_set_charset($config['char_set'], $this->conn_id);
		
		if(!$this->db_select()){
			$this->conn_id = false;
		}
	}
	
	public function issuccess(){
		return $this->conn_id !== false && is_resource($this->conn_id);
		
	}
	
	
	public function db_select(){
		return @mysql_select_db($this->params['database'], $this->conn_id);
	}
	
	
	public function escape_str($str){

		if (function_exists('mysql_real_escape_string') AND is_resource($this->conn_id))
		{
			$str = mysql_real_escape_string($str, $this->conn_id);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}

		return $str;
	}
	
	
	private function mysql_bind($sql, $vals) {
		foreach ($vals as $name => $val) {
			$sql = str_replace(":$name", "'" . $this->escape_str($val) . "'", $sql);
		}
		return $sql;
	}
	
	public function query($sql,$vals = array()){
		if($vals && is_array($vals)){
			$sql = $this->mysql_bind($sql,$vals);
		}

		$resouce =  mysql_query($sql,$this->conn_id);

		if($resouce == false){
			$this->errorlog();	
		}
		return $resouce;
	}
	
	public function num_rows($resouce){
		return (int)@mysql_num_rows($resouce);		
	}
	
	public function affected_rows($resouce){
		return (int)@mysql_affected_rows($resouce);
	}
	
	public function getRow($resouce){
		return mysql_fetch_array($resouce, MYSQL_ASSOC);
	}
	
	public function get_insertid(){
		return (int)@mysql_insert_id($this->conn_id);	
	}
	
	public  function getAll($resouce){
		$result = array();
		while ($row = mysql_fetch_array($resouce, MYSQL_ASSOC)){
			array_push($result,$row); 
		}
		return $result;
	}
	
	
	public function errorlog(){
		var_dump(mysql_errno($this->conn_id));
		var_dump(mysql_error($this->conn_id));
	}
	
	
	public function __destruct(){
		if($this->issuccess() && is_resource($this->conn_id)){
			@mysql_close($this->conn_id);
			
		}	
	}
}
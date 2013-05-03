<?php
	namespace core\dao;

	class DataBase {
		private $_mySQLi;
		private $_cache;
		public $_prefix;
		
		public function __construct(){
			$this->_mySQLi = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if ($this->_mySQLi->connect_errno){
				throw new \Exception("<strong>Error database connection</strong> " . $this->_mySQLi->connect_error);
	        }
	        self::execute("SET NAMES 'utf8'");
	        $this->_cache = new CacheAPC();
	        $this->_prefix = DB_PREFIX;
	        $this->_prefix .= !empty($this->_prefix)?"_":"";
	    }
        
        private function execute($sql, $return=null){
        	\core\util\Log::add($sql, false);
        	
        	$result =  $this->_mySQLi->query($sql);
        	\core\util\Log::add($result, false);
        	if(empty($result)){
        		throw new \Exception("<strong>error database </strong>". $this->_mySQLi->error ." query: $sql");
        	} else{
        		$result = $return ? $result : "";
        	}
        	return $result; 
        }
        
        /* select */
        public function select($id, $tabla){
        	$tabla = $this->_prefix.$tabla;
        	return self::loadObject("select * from $tabla where id=$id");
        }
        public function selectQuery($sql, $list=false, $key=null){
        	return $list ? self::loadObjectList($sql, $key) : self::loadObject($sql, $key);
        }
        private function loadObject($sql, $key=null){
        	$object = $this->_cache->getData($key);
        	if(empty($object)){
        		if ($result = self::execute($sql, true)){
        			if ($object = mysqli_fetch_object($result)){
        				if(!($this->_cache->setData($key, $object))){
        					if (!empty($key)){
        						//\core\util\Error::add("cache - $key not stored");
        					}
        				}
        				mysqli_free_result($result);
        			}
        		}
        	}
        	return $object;
        }
        private function loadObjectList($sql, $key=null){
        	$array = $this->_cache->getData($key);
        	if(empty($array)){
        		if ($result = self::execute($sql, true)){
        			$array = array();
        			while ($row = mysqli_fetch_object($result)){
        				$array[] = $row;
        			}
        			if(!($this->_cache->setData($key, new \ArrayObject($array)))){
        				if(!empty($key)){
        					//\core\util\Error::add("cache - $key not stored");
        				}
        			}
        		}
        	}
        	return $array;
        }
        
        
        
        /* delete */
        public function delete($id, $tabla){
        	$tabla = $this->_prefix.$tabla;
        	self::execute("delete from $tabla where id=$id");
        }
        public function deleteQuery($query){
        	self::execute($query);
        }
        public function view($id, $tabla, $accion){
        	$tabla = $this->_prefix.$tabla;
        	self::execute("update $tabla set alta='$accion' where id=$id");
        }
        public function insertUpdate($id, $campos, $tabla){
        	empty($id) ? self::insert($campos, $tabla) : self::update($id, $campos, $tabla);
        }
        public function insertUpdateId($id, $campos, $tabla){
        	$id=0;
        	if(empty($id)){
        		$id = self::insertId($campos, $tabla);
        	} else {
        		self::update($id, $campos, $tabla);
        	}
        	return $id;
        }
        public function insert($campos, $tabla){
        	$tabla = $this->_prefix.$tabla;
        	foreach ($campos as $campo => $valor){
        		$keys[] = $campo;
        		$valores[]='\'' . self::sanitize($valor) . '\'';
        	}
        	$keys = implode(',', $keys);
        	$valores= implode(',', $valores);
        	self::execute("insert into $tabla ($keys) values ($valores)");
        }
        public function update($id, $campos, $tabla){
        	$tabla = $this->_prefix.$tabla;
        	foreach ($campos as $campo => $valor){
        		$sets[]= $campo . '=\'' . $this->sanitize($valor) . '\'';
        	}
        	$sets = implode(',', $sets);
        	self::execute("UPDATE $tabla SET $sets WHERE id = $id");
        }
        
        public function insertId($campos, $tabla){
        	$id = 0;
        	$tabla = $this->_prefix.$tabla;
        	
        	foreach ($campos as $campo => $valor){
        			$keys[] = $campo;
        			$valores[]='\'' . self::sanitize($valor) . '\'';
        	}
        	$keys = implode(',', $keys);
        	$valores=(implode(',', $valores));
        	self::execute("insert into $tabla ($keys) values ($valores)");
        	return $this->_mySQLi->insert_id;
        }

        public function __destruct(){
        	$this->_mySQLi->close();
        }
        public function startTransaction(){
        	self::execute("START TRANSACTION");
        }
        public function rollback(){
        	self::execute("ROLLBACK");
        }
        public function commit(){
        	self::execute("COMMIT");
        }
        
        
	    public function sanitize($string){
	      //$string = htmlspecialchars($string);
	      $string = $this->_mySQLi->real_escape_string($string);
	      return $string;
	    }
	    
}
<?php
	namespace core\dao;

	class DataBase {
		private $_mySQLi;
		private $_cache;
		public $_prefix;
		
		public function __construct(){
			$this->_mySQLi = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if ($this->_mySQLi->connect_errno){
	            throw new \Exception("Error database connection: " . $this->_mySQLi->connect_error);
	        }
	        self::execute("SET NAMES 'utf8'");
	        $this->_cache = new CacheAPC();
	        self::setPrefix();
        }
        
        private function execute($sql, $returnObject=null){
        	\core\util\Log::add($sql, false);
        	try{
        		$result =  $this->_mySQLi->query($sql);
        		\core\util\Log::add($result, false);
        		if(empty($result)){
        			\core\util\Error::add("<strong>error database:</strong> query: $sql");
        			$result =  false;
        		} else{
        			$result = $returnObject ? $result : true;
        		}
        	} catch (\Exception $e){
        		\core\util\Error::add("<strong>error database:</strong> query: $sql <br>error". $e->getMessage());
        	}
        	return $result; 
        }
        
        public function __destruct(){
        	$this->_mySQLi->close();
        }
        
        public function startTransaction(){
        	return self::execute("START TRANSACTION");
        }
        public function rollback(){
        	return self::execute("ROLLBACK");
        }
        public function commit(){
        	return self::execute("COMMIT");
        }
        
        /* select */
        public function select($id, $tabla){
        	try{
        		$tabla = $this->_prefix.$tabla;
        		return self::loadObject("select * from $tabla where id=$id");
        	} catch (\Exception $e) {
        		\core\util\Error::add("<strong>error en ".__FUNCTION__. "</strong><br>". $e->getMessage());
        	}
        }
        public function selectQuery($sql, $list=false, $key=null){
        	try{
        		return $list ? self::loadObjectList($sql, $key) : self::loadObject($sql, $key);
        	} catch (\Exception $e) {
        		\core\util\Error::add("<strong>error en ".__FUNCTION__. "</strong><br>". $e->getMessage());
        	}
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
        	return self::execute("delete from $tabla where id=$id");
        }
        public function deleteQuery($query){
        	return self::execute($query);
        }
        public function view($id, $tabla, $accion){
        	$tabla = $this->_prefix.$tabla;
        	return self::execute("update $tabla set alta='$accion' where id=$id");
        }
        public function insertUpdate($id, $campos, $tabla){
        	$tabla = $this->_prefix.$tabla;
        	return empty($id) ? self::insert($campos, $tabla) : self::update($id, $campos, $tabla);
        }
        public function insert($campos, $tabla){
        	$tabla = $this->_prefix.$tabla;
        	foreach ($campos as $campo => $valor){
        		$keys[] = $campo;
        		$valores[]='\'' . self::sanitize($valor) . '\'';
        	}
        	$keys = implode(',', $keys);
        	$valores= implode(',', $valores);
        	return self::execute("insert into $tabla ($keys) values ($valores)");
        }
        public function update($id, $campos, $tabla){
        	$tabla = $this->_prefix.$tabla;
        	foreach ($campos as $campo => $valor){
        		$sets[]= $campo . '=\'' . $this->sanitize($valor) . '\'';
        	}
        	$sets = implode(',', $sets);
        	return self::execute("UPDATE $tabla SET $sets WHERE id = $id");
        }
        
        public function insertId($campos, $tabla){
        	$id = 0;
        	$tabla = $this->_prefix.$tabla;
        	try{
        		foreach ($campos as $campo => $valor){
        			$keys[] = $campo;
        			$valores[]='\'' . self::sanitize($valor) . '\'';
        		}
        		$keys = implode(',', $keys);
        		$valores=(implode(',', $valores));
        		if($this->_mySQLi->query("insert into $tabla ($keys) values ($valores)")){
        			$id = $this->_mySQLi->insert_id;
        		} else {
        			\core\util\Error::add(" error en ".__FUNCTION__. " : ". $this->_mySQLi->error);
        		}
        	} catch(\Exception $e){
        		\core\util\Error::add("error en ".__FUNCTION__. " : ". $e->getMessage());
        	}
        	return $id;
        }
	    
	    public function sanitize($string){
	      //$string = htmlspecialchars($string);
	      $string = $this->_mySQLi->real_escape_string($string);
	      return $string;
	    }
	    
	    private function setPrefix(){
	    	$this->_prefix = DB_PREFIX . "_";
	    }
}
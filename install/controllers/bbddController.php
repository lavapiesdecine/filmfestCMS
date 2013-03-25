<?php
	
	class bbddController extends Controller{
		
	    public function __construct() {
	    	$this->_title = _("bbdd.tittle");
	    	$this->_description = _("bbdd.description");
	    	parent::__construct();
	    }
	    
	    public function index(){
	    
	    	$server = ""; $username = ""; $password = ""; $database = "";$prefix = "";
	    
	    	if(isset($_SESSION["bbdd"])){
	    		$bbdd = $_SESSION["bbdd"];
	    		$server = $bbdd["server"];
	    		$username = $bbdd["username"];
	    		$password = $bbdd["password"];
	    		$database = $bbdd["database"];
	    		$prefix = $bbdd["prefix"];
	    	}
	    
	    	$this->_data = array("server" => $server, "username" => $username,
	    						 "password" => $password, "database" => $database, "prefix" => $prefix);
	    	$this->loadView();
	    }
	    
	   /**
	    * Carga en sesion de los datos de configuracion de la base de datos
	    * Carga del archivo de configuracion cms/core/config/bbdd.php
	    * Carga del archivo de base de datos install/sql/default.sql
	    */
	   public function alta(){
	    	
	    	$_SESSION['bbdd'] = array("server" => $_POST['hostname'],
					    				"username" => $_POST['usuario'],
					    				"password" => $_POST['password'],
					    				"database" => $_POST['database'],
	    								"prefix" => $_POST['prefix']);
    		
    		$mySQLi = @new mysqli($_POST['hostname'], $_POST['usuario'], $_POST['password'], $_POST['database']);
    		
    		if ($mySQLi->connect_errno){
    			$result = array("ok" =>false, "msg"=> $mySQLi->connect_error);
    		} else {
    			if($this->config()){
    				$this->load($mySQLi, $_POST['database']);
    				$result = array("ok"=>true, "msg"=>"Completado correctamente en <strong> " . CONF_PATH . "bbdd.php </strong>");
    			} else {
    				$result = array("ok"=>false, "msg"=>"Fallo en el archivo configuracion");
    			}
    		}
    		echo json_encode($result);
       }
	   
	   /**
	    * Carga del archivo de configuraci�n cms/core/config/bbdd.php
	    * @param $bbdd
	    * @return boolean
	    */
	   private function config() {
	   		try{
	   			
	   			copy(PATH_INSTALL . 'config' . DS . $this->_controller . '.php.bak', CONF_PATH . $this->_controller . '.php');
	   			
		   		$template = file_get_contents(CONF_PATH . $this->_controller . '.php');
		   		
		   		$bbdd = $_SESSION['bbdd'];
		   		
		   		$replace = array(
		   				'__HOSTNAME__' 	=> $bbdd["server"],
		   				'__USERNAME__' 	=> $bbdd["username"],
		   				'__PASSWORD__' 	=> $bbdd["password"],
		   				'__DATABASE__' 	=> $bbdd["database"],
		   				'__PREFIX__' 	=> $bbdd["prefix"]
		   		);
		   	
		   		$file = str_replace(array_keys($replace), $replace, $template);
		   	
		   		$handle = fopen(CONF_PATH . $this->_controller . '.php','w+');
		   		
		   		fwrite($handle, $file);
		   		
		   		return true;
	   		}  catch (Exception $e) {
				Error::add("<strong>error en ".__FUNCTION__. "</strong><br>". $e->getMessage(). "<br>");
				return false;
			}
	    }
	   
	   /**
	    * Carga del archivo de base de datos install/sql/default.sql
	    * @param $mySQLi
	    * @param $database
	    * @return boolean
	    */
	   private function load($mySQLi){
			
	   		$usuario = $_SESSION['usuarioAdmin'];
	   		$bbdd = $_SESSION['bbdd'];
	   		
	   		$schema = file_get_contents(PATH_INSTALL . 'sql' . DS . 'default.sql');
			$schema = str_replace('{DATABASE}', $bbdd["database"], $schema);
			$schema = str_replace('{NICKNAME}', "'".$usuario["nickname"]."'", $schema);
			$schema = str_replace('{PASSWORD}', "'".md5($usuario["password"])."'", $schema);
			$schema = str_replace('{EMAIL}', "'".$usuario["email"]."'", $schema);
			$schema = str_replace('{PREFIX}', !empty($bbdd["prefix"])?$bbdd["prefix"]."_":"", $schema);
			
	   		$queries = explode('-- command split --', $schema);
	   
		   	foreach($queries as $query){
		   		$query = rtrim(trim($query), "\n;");
		   		$result =  $mySQLi->query($query);
		   	}
		   	return true;
	   }
	   
	   
	}
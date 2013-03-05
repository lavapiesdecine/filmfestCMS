<?php

class geoController extends Controller{

	public function __construct() {
		$this->_title = _("geo.tittle");
		$this->_description = _("geo.description");
		parent::__construct();
	}
	
	public function index(){
		 
		$longitud = "-3.700907700000016"; $latitud = "40.4085123"; $direccion = "";
		 
		if(isset($_SESSION["geo"])){
			$geo = $_SESSION["geo"];
			$longitud = $geo["longitud"];
			$latitud = $geo["latitud"];
			$direccion = $geo["direccion"];
		}
		 
		$this->_data = array("longitud" => $longitud, "latitud" => $latitud, "direccion" => $direccion);
		$this->loadView();
	}
	
	public function alta(){
		$_SESSION[$this->_controller] = array("longitud" => $_POST['id_longitud'], "latitud" => $_POST['id_latitud'], "direccion" => $_POST['direccion']);
		if($this->config($_SESSION[$this->_controller])){
			$result = array("ok"=>true, "msg"=>"Completado correctamente en <strong> " . PATH_INSTALL . "config" . DS . "geo.php </strong>");
		} else {
			$result = array("ok"=>false, "msg"=>"Fallo");
		}
		echo json_encode($result);
	}
	
	private function config($geo) {
		try{
			 
			copy(PATH_INSTALL . 'config' . DS . $this->_controller .'.php.bak', CONF_PATH . $this->_controller . '.php');
			 
			// Open the template file
			$template = file_get_contents(CONF_PATH . $this->_controller . '.php');
			 
			$replace = array(
					'__UBICACION__' => $geo["direccion"],
					'__LATITUD__' 	=> $geo["latitud"],
					'__LONGITUD__' 	=> $geo["longitud"]
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
}
<?php

class confController extends Controller{

	public function __construct() {
		$this->_title = _("conf.tittle");
		$this->_description = _("conf.description");
		parent::__construct();
	}
	
	/**
	 * Carga en sesion de los datos de configuración del usuario admin
	 * Carga del archivo de configuración cms/core/config/conf.php
	 */
	public function index(){
		
		$email = ""; $password = ""; $nickname = ""; $emailcontact = ""; $lang = "";
		$langs = unserialize(LANGS);
		
		if(isset($_SESSION["usuarioAdmin"])){
			$usuario = $_SESSION["usuarioAdmin"];
			$email = $usuario["email"];
			$password = $usuario["password"];
			$nickname = $usuario["nickname"];
			$emailcontact = $usuario["emailcontact"];
			$lang = $usuario["lang"];
		}
		
		$this->_data = array("email" => $email, "password" => $password, "nickname" => $nickname, "emailcontact" => $emailcontact, "langs" => $langs, "lang" => $lang);
		$this->loadView();
	}

	public function alta(){

		$_SESSION['usuarioAdmin'] = array("email" => $_POST['email'],
				"password" => $_POST['password'],
				"nickname" => $_POST['nickname'],
				"emailcontact" => $_POST['emailcontact'],
				"lang" => $_POST['lang']);
		
		if($this->loadLanguage($_POST['lang'])){
			if($this->config($_SESSION['usuarioAdmin'])){
				$result = array("ok"=>true, "msg"=>"Completado correctamente en <strong> " . CONF_PATH . "conf.php </strong>");
			} else {
				$result = array("ok"=>false, "msg"=>"Fallo");
			}
		}
		echo json_encode($result);
	}
	
	private function loadLanguage($langSelected){
		
		$langs = unserialize(LANGS);
		$lang = $langs[$langSelected];
		
		/* generate .mo file */
		require(CMS_PATH . "core" . DS . "lib" . DS . "php-mo.php");
		$pathFileLanguage = CMS_PATH."locale". DS . $lang[2] . DS . "LC_MESSAGES" . DS;
		if(!file_exists($pathFileLanguage . "admin.mo")){
			@phpmo_convert( $pathFileLanguage . 'admin.po');
		}
		if(!file_exists($pathFileLanguage . "www.mo")){
			@phpmo_convert( $pathFileLanguage . 'www.po');
		}
		
		return true;
	}
	
	
	/**
	 * Carga del archivo de configuración cms/core/config/conf.php
	 * @param $usuario
	 * @return boolean
	 */
	private function config($usuario){
		
		$template = file_get_contents(CONF_PATH . $this->_controller . '.php');
		
		$replace = array(
				'__ADMIN_LANG__' 	=> $usuario["lang"],
				'__ADMIN_EMAIL__' 	=> $usuario["email"],
				'__CONTACT_MAIL__' 	=> $usuario["emailcontact"]
		);

		$file = str_replace(array_keys($replace), $replace, $template);

		$handle = fopen(CONF_PATH . $this->_controller . '.php','w+');
		 
		fwrite($handle, $file);
		 
		return true;

	}
	 
	 
}
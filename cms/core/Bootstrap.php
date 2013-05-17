<?php

namespace core;

class Bootstrap{
	
	private static $_dao;
	
	/**
	 * load data from url
	 * @return \core\classes\Request
	 */
	public static function loadRequest(){
		//valores por defecto
		$controller = "";
		$metodo = DEFAULT_METODO;
		$anyo = DEFAULT_ANYO;
		$id = 0;
		 
		$url = explode("/", $_SERVER['REQUEST_URI']);
		$numURL = count(explode("/",str_replace('http://', '', BASE_URL)));
		$admin = isset($url[$numURL]) && $url[$numURL] == ADMIN;
		$request = new classes\Request();
		for ($i = 0; $i <= $numURL-1; $i++) {
			unset($url[$i]);
		}
		if($admin){
			unset($url[$numURL]);
		}
		$params = array_values($url);

		return $admin ? self::loadRequestAdmin($params) : self::loadRequestWWW($params);
	}
	
	/**
	 * carga el objeto Request con la peticion del usuario.
	 * admin - posibles url
	 *  > admin: http://site.com/admin/controlador
	 *  > admin: http://site.com/admin/anyo/controlador
	 *	> admin: http://site.com/admin/anyo/controlador/id
	 *  > admin: http://site.com/admin/anyo/controlador/accion ajax
	*/
	private static function loadRequestAdmin($params){
		
		$controller = "";
		$metodo = DEFAULT_METODO;
		$anyo = DEFAULT_ANYO;
		$id = 0;
		
		//TODO: router 
		if (isset($params[0])){
			if (is_numeric($params[0])){
				if (strlen($params[0])==4){
					$anyo = $params[0];
				} else {
					$id = $params[0];
				}
			}
			else{
				$controller = $params[0];
			}
		}
		if (isset($params[1])){
			if (is_numeric($params[1])){
				$id = $params[1];
			}
			else{
				if(empty($controller)){
					$controller = $params[1];
				} else {
					$metodo = $params[1];
				}
			}
		}
		if (isset($params[2])){
			if (is_numeric($params[2])){
				$id = $params[2];
			}
			else{
				$metodo = $params[2];
			}
		}
		
		if (isset($params[3])){
			if (is_numeric($params[3])){
				$id = $params[3];
			}
			else{
				$metodo = $params[3];
			}
		}
		
		$request = new classes\Request();
		$request->setApplication(ADMIN);
		$request->setControlador($controller);
		$request->setMetodo($metodo);
		$request->setId($id);
		$request->setAnyo($anyo);
		$request->setLang(ADMIN_LANG);
		return $request;
	}
	
   /**
	* www - posibles url
	* > muestra actual: http://site.com/lang/controlador/id
	* > muestra pasada: http://site.com/anyo/controlador/id
	* > pelicula: http://site.com/pelicula/id/nombre-de-pelicula
	* > recepcion con paginacion:  http://site.com/anyo/recepcion/pag
    */
	private static function loadRequestWWW($params){
		$controller = "";
		$metodo = DEFAULT_METODO;
		$anyo = DEFAULT_ANYO;
		$id = 0;
		$lang = DEFAULT_LANG;
		
		//TODO: router
		if (isset($params[0])){
			if (is_numeric($params[0]) && strlen($params[0])==4){
				$anyo = $params[0];
			} else if(strlen($params[0])==2){
				$lang = $params[0];
			} else {
				$controller = $params[0];
			}
		}
		if (isset($params[1])){
			if(is_numeric($params[1]) && strlen($params[1])==4 ){
				$anyo = $params[1];
			} else if(is_numeric($params[1])){
				$id = $params[1];
			} else if(strlen($params[1])==2){
				$lang = $params[1];
			} else if(empty($controller)){
				$controller = $params[1];
			} else{
				$metodo = $params[1];
			}
		}
		if (isset($params[2])){
			if (is_numeric($params[2])){
				$id = $params[2];
			} else if(empty($controller)){
				$controller = $params[2];
			} else{
				$metodo = $params[2];
			}
		}
		if (isset($params[3])){
			if(is_numeric($params[3])){
				$id = $params[3];
			} else {
				$metodo = $params[3];
			}
		}
		
		$request = new classes\Request();
		$request->setApplication(WEB);
		$request->setControlador($controller);
		$request->setMetodo($metodo);
		$request->setId($id);
		$request->setAnyo($anyo);
		$request->setLang($lang);
		return $request;
	}
	
    
    /**
     * load data bbdd
     * @param classes\Data $data
     * @return \classes\Data
     */
    public static function loadData(classes\Data $data){
    	return $data->getRequest()->getApplication() == ADMIN ? self::loadDataAdmin($data):self::loadDataWWW($data);
    }
    
    private static function loadDataWWW(classes\Data $data){
    	$values = array();
    	$idControllerMenu = 0;
	    $idPagina = 0;
		$layout = DEFAULT_LAYOUT;
		$request = $data->getRequest();
		$skin = $request->getAnyo();
		$modulo = $request->getControlador();
		$subMenu = array();
		$idTexto = 0;
		$anyo = $request->getAnyo();
		
    	$dao = dao\DAO::getInstance();

		//obtener idiomas de la muestra $langs
    	$langs = $dao->langsEdicionDAO($anyo);
    	$data->setLangs($langs);
    	
    	//carga idioma y los textos
    	$lang = self::loadLanguage($langs, $request->getLang(), WEB);
    	
    	$pagina = self::loadPagina($request, $dao, $lang);
    	 
    	if (empty($pagina)){
    		$modulo = "error";
    		$values = array_merge($values, array("error" => "404"));
    	} else {
    		$anyo = $pagina->anyo;
    		$layout = $pagina->layout;
    		$skin = $pagina->skin;
    		$modulo = $pagina->modulo;
    		$idTexto = $pagina->id_texto;
    		$idPagina = $pagina->id;
    		$idControllerMenu = $pagina->id_paginapadre;
    	}
    	$data->setEdicion($dao->edicionDAO($anyo));
    	$request->setAnyo($anyo);
    	$data->setIdTexto($idTexto);
    	//carga submenu
    	if ($idPagina>0){
    		$idControllerMenu = ($idControllerMenu==0) ? $idPagina : $idControllerMenu;
    		$subMenu = $dao->submenuDAO($idControllerMenu);
    	}
    	
    	//carga menu
		$values = array_merge($values, array("menu" => $dao->menuDAO($anyo)));
		$values = array_merge($values, array("subMenu" => $subMenu));
		$values = array_merge($values, array("idControllerMenu" => $idControllerMenu));
		$data->setData($values);
		$data->setDao($dao);
		$data->setLayout($layout);
		$data->setSkin($skin);
		$data->setModulo($modulo);
    	return $data;
    }
	
    private static function loadPagina($request, $dao, $lang){
    	$arrayPaginaById = array ("proyecciones", "pelicula");
    	$controller = $request->getControlador();
    	$id = $request->getId();
    	$anyo = $request->getAnyo();
    	
    	if(empty($controller)){
    		$pagina = $dao->paginaPortada($anyo, $lang);
    	} else if(in_array($controller, $arrayPaginaById)){
    		$pagina = $dao->paginaByIdDAO($controller, $id, $anyo);
    	} else {
    		$pagina = $dao->paginaDAO($controller, $anyo, $lang);
    	}
    	return $pagina;
    }

    private static function loadDataAdmin(classes\Data  $data){
    	$values = array();
    	$layout = ADMIN_LAYOUT;
    	$skin = ADMIN_SKIN;
    	$modulo = DEFAULT_ADMINMODULE;
    	$request = $data->getRequest();
    	
    	$dao = dao\AdminDAO::getInstance();
    	$data->setDao($dao);
			
    	//carga idioma y los textos
    	$langs = $dao->langDAO($request->getLang());
    	$data->setLangs($langs);
    	$lang = self::loadLanguage($langs, $request->getLang(), ADMIN);
    	
    	$usuario = self::getUsuario($dao);
    		
    	if (empty($usuario)){
    		$modulo = "login";
    		$layout = DEFAULT_LAYOUT;
    		if (isset($_POST['nombreusuario'])){
    			$values = array_merge($values, array("error" => _("error.login")));
    			util\Error::add(_("error.login").$_POST['nombreusuario']);
    		}
    	} else {	
    		$data->setUsuario($usuario);
    		$perfilNavegacion = isset($_SESSION['nivel_navegacion']) ? $_SESSION['nivel_navegacion'] : 0;
    		$perfiles="";
    		foreach ($usuario->getPerfiles() as $index =>$perfil){
    			$perfiles .= $perfil->id.($index<(count($usuario->getPerfiles())-1) ? ",":"");
    		}
    		$modulo = $data->getRequest()->getControlador();
    		if(empty($modulo)){
    			$modulo = DEFAULT_ADMINMODULE;
    		} else if($modulo=='logout') {
    			session_destroy();
    			header("Location: ".URL_ADMIN);
    			exit;
    		} else if($modulo!='anyo' && $modulo!='viewusuario') {
    			$adminModulo = $dao->adminModuloNombreDAO($modulo, $perfiles);
    			if (!empty($adminModulo)){
	    			$_SESSION['nivel_navegacion'] = $adminModulo->id_perfil;
	    			$perfilNavegacion = $adminModulo->id_perfil;
	    		} else {
	    			util\Error::add("no encuentra modulo:  $modulo  - " . $usuario->getNombre());
	    			$modulo = "error";
	    		}
	    		
	    		$values = array_merge($values, array("idControllerMenu" => $adminModulo->modulo_padre));
	    		$menu = array();
	    		foreach ($dao->menuDAO($perfilNavegacion) as $controller) {
	    			$subMenu = $dao->subMenuDAO($controller->id, $controller->id_perfil);
	    			$controller = array_merge((array)$controller, array("submenu" =>$subMenu));
	    			array_push($menu, (array)$controller);
	    		}
	    		$values = array_merge($values, array("menu" => $menu));
    		}
	    	
    		$data->setEdicion($dao->edicionDAO($data->getRequest()->getAnyo()));
    	} 
    	$data->setModulo($modulo);
    	$data->setData($values);
    	$data->setLayout($layout);
    	$data->setSkin($skin);
    	return $data;
    }
    
    
    
    /**
     * valida si esta logado el usuario y carga los datos
     *   
	 * @return Data
	*/
    private static function getUsuario(dao\AdminDAO $dao){
    	$usuario = null;
    	//validado previamente
    	if (isset($_SESSION['usuario'])){
    		$usuario = unserialize($_SESSION['usuario']);
    	} else {
    		//se va a validar
    		if (isset($_POST['nombreusuario'])){
    			$login = $dao->validaDAO(strip_tags($_POST['nombreusuario']));
    			$password = md5($_POST['psw']);
    		  
    			if(isset($login) && $login->usuario == $_POST['nombreusuario'] && $login->pass == $password){
    				$usuario = new classes\Usuario();
    				$usuario->setId($login->id);
    				$usuario->setNombre($login->usuario);
    				$usuario->setPerfiles($dao->perfilesAccesoDAO($login->id));
    				$usuario->setEmail($login->email);
    				$usuario->setLogo($login->logo);
    				$_SESSION['usuario'] = serialize($usuario);
    			}
    		}
    	}
    	util\Log::add($usuario, false);
    	return $usuario;
    }
    
    
    /**
     * carga de idiomas para los properties
     * 
     * @param $languages
     * @return lang
 	*/
    private static function loadLanguage($languages, $lang, $app){
    	
    	foreach ($languages as $language) {
    		if($language->lang==$lang){
    			$codigo = $language->codigo;
    			$codificacion = $language->codificacion;
    			break;
    		}
    	}		
    	
    	$pathFileLanguage = CMS_PATH."locale". DS . $codigo . DS . "LC_MESSAGES" . DS;
    	if(!file_exists($pathFileLanguage . "$app.mo")){
    		require(CMS_PATH . "core" . DS . "lib" . DS . "php-mo.php");
    		phpmo_convert( $pathFileLanguage . "$app.po");
    	}
    	
    	putenv("LC_ALL=".$codigo.".".$codificacion);
    	setlocale(LC_ALL, $codigo.".".$codificacion);
    	bindtextdomain($app, CMS_PATH."locale");
    	textdomain($app);
    	
    	return $lang;
    }
}
<?php
	
	date_default_timezone_set("Europe/Paris");
	header('Content-Type: text/html; charset=utf-8');
	
	session_start();
	
	/**
	 * INSTALL CMS
	 */
	if (!file_exists("cms/core/config/geo.php")){
		
		if (function_exists('apache_get_modules')) {
			if(!in_array('mod_rewrite', apache_get_modules())){
				$errorMsg = "Enable mod_rewrite";
				include("install/error.php");
				exit;
			}
		}
		
		//initial load: cms/core/config/conf.php 
		if(!file_exists("cms/core/config/conf.php")){
			$baseUrl = "http://". $_SERVER['SERVER_NAME'] . "/". str_replace("/", "", $_SERVER['REQUEST_URI']);
			if (@copy("install/config/conf.php.bak", "cms/core/config/conf.php")){
				$template = file_get_contents("cms/core/config/conf.php");
				$replace = array('__BASE_URL__' => $baseUrl);
				$file = str_replace(array_keys($replace), $replace, $template);
				$handle = fopen("cms/core/config/conf.php", "w+");
				fwrite($handle, $file);
			} else {
				include("install/error.php");
				exit;
			}
		} else {
			include_once("cms/core/config/conf.php");
			$baseUrl = BASE_URL;
		}
		header("Location: $baseUrl/install");
		exit;
	}
	
	/**
	 * LOAD CONF FILES
	 */
	include_once("cms/core/util/UtilFile.php");
	$confFiles = core\util\UtilFile::getFiles("cms/core/config/", "php");
	foreach ($confFiles as $file) {
		include_once("cms/core/config/$file");
	}
	include_once("constants.php");
	
	/**
	 * AUTOLOAD CLASSES + ERROR HANDLING
	 */
	include_once("funciones.php");
	set_error_handler('error_handler', E_STRICT);
	set_exception_handler('exception_handler');
	
	/**
	 * RUN APP
	 */
	$data = new core\classes\Data();
	
	/**
	 * load data from url
	 */
	$data->setRequest(core\Bootstrap::loadRequest());
	
	/**
	 * load data bbdd
	 */
	$data = core\Bootstrap::loadData($data);
	\core\util\Log::add($data, false);
	
	/**
	 * instance requested controller
	 */
	$nameController = "\\".$data->getRequest()->getApplication()."\\controllers\\".$data->getModulo()."Controller";
	$controller = new $nameController($data);
	
	/** 
	 * call method
	 */
	//get method
	$method = is_callable(array($controller, $data->getRequest()->getMetodo())) ? $data->getRequest()->getMetodo() : DEFAULT_METODO;
	
	call_user_func(array($controller, $method));
	

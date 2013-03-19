<?php
	
	date_default_timezone_set("Europe/Paris");
	header('Content-Type: text/html; charset=utf-8');
	
	session_start();
	
	/**
	 * INSTALL CMS
	 */
	if (!file_exists("cms/core/config/geo.php")){
		
		//check mod_rewrite
		print_r(getenv('HTTP_MOD_REWRITE'));
		
		if (function_exists('apache_get_modules')) {
			$mod_rewrite = in_array('mod_rewrite', apache_get_modules());
		} else {
			$mod_rewrite =  getenv('HTTP_MOD_REWRITE')=='On';
		}
		if(!$mod_rewrite){
			$errorMsg = "Enable mod_rewrite";
			include("install/error.php");
			exit;
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
	 * CONF FILES
	 
	include_once("cms/core/util/Util.php");
	$confFiles = core\util\Util::getFiles("cms/core/config/", "php");
	foreach ($confFiles as $file) {
		include_once("cms/core/config/$file");
	}
	include_once("constants.php");
	include_once("funciones.php");
	
	set_error_handler('error_handler', E_STRICT);
	set_exception_handler('exception_handler');
	*/
	
	/**
	 * RUN BOOTSTRAP
	 
	core\Bootstrap::run();
	*/
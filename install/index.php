<?php
	
	date_default_timezone_set("Europe/Paris");
	header('Content-Type: text/html; charset=utf-8');
	
	session_start();
		
	if(!file_exists("../cms/core/config/conf.php")){
		header("Location: ../");
		exit;
	}
	
	include_once("../cms/core/config/conf.php");
	include_once("../constants.php");
	
	define('URL_INSTALL', BASE_URL . '/install');
	define('PATH_INSTALL', ROOT . 'install' . DS);
	define('LANG_INSTALL', "es_ES");
	define('ENCODELANG_INSTALL', "UTF-8");
	
	/* generate .mo file */
	$pathFileLanguage = PATH_INSTALL."locale". DS . LANG_INSTALL . DS . "LC_MESSAGES" . DS;
	if(!file_exists($pathFileLanguage . "install.mo")){
		require(CMS_PATH . "core" . DS . "lib" . DS . "php-mo.php");
		if(!@phpmo_convert($pathFileLanguage . 'install.po')){
			include("error.php");
			exit;
		}
	}
	
	/* load language */
	putenv("LC_ALL=".LANG_INSTALL.".".ENCODELANG_INSTALL);
	setlocale(LC_ALL, LANG_INSTALL.".".ENCODELANG_INSTALL);
	bindtextdomain("install", PATH_INSTALL."locale");
	textdomain("install");
	
	define('STEPS', serialize(array(1 => array("intro", _("intro.tittle"), _("intro.description")), 
									2 => array("conf", _("conf.tittle"), _("conf.description")), 
									3 => array("bbdd", _("bbdd.tittle"), _("bbdd.description")), 
									4 => array("geo", _("geo.tittle"), _("geo.description")), 
									5 => array("end", _("end.tittle"), _("end.description")))));
	
	define('LANGS', serialize(array("es" => array("es", _("lang.es"), "es_ES"))));
	
	include_once("funciones.php");
	
	Bootstrap::run();
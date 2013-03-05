<?php
	
	date_default_timezone_set("Europe/Paris");
	header('Content-Type: text/html; charset=utf-8');
	
	session_start();
	
	if(!file_exists("../cms/core/config/conf.php")){
		header("Location: ../");
		exit;
	}
	
	if(strnatcmp(phpversion(),'5.3.0') >= 0){
		include_once("../cms/core/config/conf.php");
		include_once("../constants.php");
		
		define('URL_INSTALL', BASE_URL . '/install');
		define('PATH_INSTALL', ROOT . DS . 'install' . DS);
		
		putenv("LC_ALL=es_ES.iso-8859-1");
		setlocale(LC_ALL, "LC_ALL=es_ES.iso-8859-1");
		bindtextdomain("install", PATH_INSTALL."locale");
		textdomain("install");
		
		define('STEPS', serialize(array(1 => array("intro", _("intro.tittle"), _("intro.description")), 
										2 => array("conf", _("conf.tittle"), _("conf.description")), 
										3 => array("bbdd", _("bbdd.tittle"), _("bbdd.description")), 
										4 => array("geo", _("geo.tittle"), _("geo.description")), 
										5 => array("end", _("end.tittle"), _("end.description")))));
		
		define('LANGS', serialize(array("es" => array("es", _("lang.es")))));
		
		include_once("funciones.php");
		
		Bootstrap::run();
	} else {
		include("error.php");
		exit;
	}
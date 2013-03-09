<?php
	
	//rutas
	define('APP', 'filmfestCMS');
	define('APP_VERSION', '1.2 beta');
	
	define('CMS', 'cms');
	define('ADMIN', 'admin');
	define('WEB', 'www');
	define('IMG', 'img');
	define('GALERIAS', 'galerias');
	define('LOGO', 'logo');
	define('DOC', 'doc');
	define('THUMBNAIL', 'tn');
	define('MEDIUM', 'md');
	
	//path
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', realpath(dirname(__FILE__)) . DS);
	define('DOC_PATH', ROOT . DOC . DS);
	define('IMG_PATH', ROOT . IMG . DS);
	define('TMP_PATH', ROOT . IMG . DS . 'tmp' . DS);
	define('GALERIAS_PATH', IMG_PATH . GALERIAS . DS);
	define('LOGO_PATH', IMG_PATH . LOGO . DS);
	
	define('CMS_PATH', ROOT . CMS . DS);
	define('ADMIN_PATH', CMS_PATH . ADMIN . DS);
	define('WEB_PATH', CMS_PATH . WEB . DS);
	define('SKINS_PATH', WEB_PATH . "vista" . DS . "skins" . DS);
	define('LOGO_ADMIN_PATH', ADMIN_PATH . 'vista' . DS . 'skins' . DS . ADMIN_SKIN . DS .'img' . DS);
	define('CONF_PATH', CMS_PATH . 'core' . DS .'config' . DS);
	
	//url
	define('URL_ADMIN', BASE_URL . '/' . ADMIN);
	define('BASE_URL_ADMIN', BASE_URL . '/' . CMS . '/' . ADMIN);
	define('BASE_URL_WEB', BASE_URL . '/' . CMS . '/' . WEB);
	define('URL_SKINS', BASE_URL_WEB . '/vista/skins/');
	define('URL_IMG', BASE_URL . '/' . IMG . '/');
	define('URL_LOGO', URL_IMG . LOGO . '/');
	define('URL_IMG_ADMIN', BASE_URL_ADMIN . '/vista/skins/'.ADMIN_SKIN.'/img/');
	define('URL_GALERIAS', URL_IMG . GALERIAS . '/');
	define('URL_DOC', BASE_URL . '/'. DOC .'/');
	define('URL_LIBJS', BASE_URL . '/'. CMS . '/libjs/');
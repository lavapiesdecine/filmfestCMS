<?php

	namespace core;
	
	class Controller{
		
	    protected $_dao;
	    protected $_data;
	    protected $_anyo;
	    protected $_id;
	    protected $_controller;
	    protected $_baseUrl;
	    protected $_base;
	    protected $_pathImg;
	    protected $tagBody;
	    protected $_lang;
	    protected $_title;
	    protected $_description;
	    
	    public function __construct($data=null) {
	    	$this->_data = $data;
	    	$this->_dao = $data->getDao();
	    	$this->_id = $data->getRequest()->getId();
	    	$this->_anyo = $data->getRequest()->getAnyo();
	    	$this->_controller = $this->_data->getModulo();    	
	    	$this->_skin = $this->_anyo;
	    	$this->tagBody = "";
	    	$this->_lang = $data->getRequest()->getLang();
	    	$this->_baseUrl = BASE_URL . '/' . ( $this->_lang == DEFAULT_LANG ? '' : $this->_lang . '/') . ( $this->_anyo == DEFAULT_ANYO ? '' : $this->_anyo . '/');
	    	$this->_base = BASE_URL_WEB;
	    	//$data->setEdicion($edicion);
	    }
	    
	    protected function loadView(){
			//$title = $this->_data->getTitle();
	    	//$description = $this->_data->getDescription();
	    	$urlPagina = $this->_data->getRequest()->getControlador();
	    	$usuario = $this->_data->getUsuario();
	    	
	    	//$lang = $this->_data->getLang();
	    	$lang = $this->_lang;
	    	$langs = $this->_data->getLangs();
	    	
	    	$edicion = $this->_data->getEdicion();
	    	
	    	if (!empty($edicion)){
		    	$fechaInicio = strtotime($edicion->fecha_inicio);
		    	$fechaFin = strtotime($edicion->fecha_fin);
	    	}
	    	
	    	$pathVista =  CMS_PATH . $this->_data->getRequest()->getApplication(). DS .'vista' . DS;
			$pathSkin =  CMS_PATH . $this->_data->getRequest()->getApplication(). DS .'vista' . DS . 'skins' . DS . $this->_data->getSkin() . DS;
			$pathCommons = CMS_PATH . $this->_data->getRequest()->getApplication(). DS .'vista' . DS . 'skins' . DS . 'commons' . DS;
			
			$urlSkin =  $this->_base . "/vista/skins/".$this->_data->getSkin()."/";
			$urlCommons =  $this->_base . "/vista/skins/commons/";
			
	    	util\Log::add($pathSkin . 'layouts' . DS . $this->_data->getLayout(), false);
	    	
	    	extract($this->_data->getData());
	    	
	    	include_once $pathSkin . 'layouts' . DS . $this->_data->getLayout();
	           
	    }
	    
	    protected function addData($dataController){
	    	$this->_data->setData(array_merge((array)$this->_data->getData(), (array)$dataController));
	    }
	    
	    public function uploadImagen($actions){
	    	echo "<head><style type='text/css'> body{margin: 0;font-size: 10px;}</style></head>";
	    		
	    	$fileTmp = TMP_PATH . $_FILES['imagen']['name'];
	    	
	    	if(copy ($_FILES['imagen']['tmp_name'], $fileTmp)){ 
	    			$img = new util\Image($fileTmp);
	    			$img->setType($_FILES['imagen']['type']);
	    			$nombreImg =  empty($_POST['id_nombre']) ? $nombreImg = date('YmdHms') : util\Util::stripAccents($_POST['id_nombre']);
	    			$img->setName($nombreImg);
	    			foreach ($actions as $action){
	    				switch ($action['action']) {
						   case 'crop':
						      	$img->crop($action['path'], $action['width'], $action['height']);
						      	break;
						   case 'resize':
						   		$img->resize($action['path'], $action['height']);
						   		break;
						   case 'save':
						   		$img->save($action['path']);
						   		break;
						   case 'grayscale':
						   		$img->grayscale($action['path']);
						   		break;
						}
	    			}
	    			unlink($fileTmp);
	    			return $nombreImg . ".jpg";
	    	}
	    	else{
	    		throw new \Exception("error al subir imagen $fileTmp");
	    	}
	    
	    }
	}
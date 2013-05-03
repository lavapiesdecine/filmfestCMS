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
	    	$this->_lang = $data->getRequest()->getLang();
	    	$this->_baseUrl = BASE_URL . '/' . ( $this->_lang == DEFAULT_LANG ? '' : $this->_lang . '/') . ( $this->_anyo == DEFAULT_ANYO ? '' : $this->_anyo . '/');
	    	$this->_base = BASE_URL_WEB;
	    }
	    
	    protected function loadView(){
			$urlPagina = $this->_data->getRequest()->getControlador();
	    	$usuario = $this->_data->getUsuario();
	    
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
			
	    	extract($this->_data->getData());
	    	
	    	include_once $pathSkin . 'layouts' . DS . $this->_data->getLayout();
	           
	    }
	    
	    protected function addData($dataController){
	    	$this->_data->setData(array_merge((array)$this->_data->getData(), (array)$dataController));
	    }
	    
	    
	}
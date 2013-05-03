<?php

	namespace admin\controllers;
	
	class modulowebController extends \core\AdminController{
		
	     public function __construct($data) {
	     	$this->_tabla = "web_modulos";
	     	$this->_title = _("moduloweb.title");
	     	$this->_description = _("moduloweb.description");
	    	parent::__construct($data);
	    }
	    
	    public function index(){
			
	      	$webModulo = "";
		   	$descripcion = "";
		   
	    	if(!empty($this->_id)){
		   		$webModuloDAO = $this->_dao->select($this->_id, $this->_tabla);
				$webModulo = $webModuloDAO->modulo;
		 	}
		 	
	    	$this->addData(array("listado" => $this->_dao->webModulosDAO(),
	    						 "webModulo" => $webModulo));
	
			$this->loadView();
	
	    }   
	    
	    
	 	public function alta(){
	 		$campos = array("modulo" => $_POST['modulo']);
	 		echo $this->alta($_POST['id'], $campos);
	    }
	    
	}
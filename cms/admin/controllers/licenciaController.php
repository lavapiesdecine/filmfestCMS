<?php

namespace admin\controllers;

class licenciaController extends \core\AdminController{
	
     public function __construct($data) {
     	$this->_tabla = "licencias";
     	$this->_title = _("licencia.title");
     	$this->_description = _("licencia.description");
        parent::__construct($data);
    }
    
    public function index(){
		$idModuloDefault=0;
		$nombre = "";
	   	$url = "";
	   	if(empty($this->_id)){
	   		$this->_id ="01";
	   	}
	   	
	   	$licenciaDAO = $this->_dao->select($this->_id, $this->_tabla);
   		$nombre = $licenciaDAO->nombre;
   		$url = $licenciaDAO->url;
	   	
    	$this->addData(array("nombre" => $nombre, "url" => $url, "listado" => $this->_dao->licenciasDAO()));
		
		$this->loadView();

    }
    
}
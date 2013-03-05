<?php
	
	namespace www\controllers;

	class textoController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$txtDAO = $this->_dao->textoDAO($this->_data->getIdTexto());
	    	$galeriaDAO = array();
	    	if(!empty($txtDAO->id_galeria)){
	    		$galeriaDAO = $this->_dao->imagenesDAO($txtDAO->id_galeria);
	    	}
	    	$this->_title = $txtDAO->titulo;
	    	$this->_description = \core\util\Util::substring($txtDAO->texto, 200);
	    	$this->addData(array("txt" => $txtDAO, "galeria" => $galeriaDAO));
			$this->loadView();
	    }
	}
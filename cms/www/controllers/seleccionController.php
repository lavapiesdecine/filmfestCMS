<?php 

	namespace www\controllers;
	
	class seleccionController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->addData(array("peliculas" => $this->_dao->seleccionDAO($this->_anyo)));
	    	$this->_title = _("seleccion.title");
	    	$this->_description = _("seleccion.description");
			$this->loadView();
	    }
	}
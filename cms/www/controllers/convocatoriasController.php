<?php
	namespace www\controllers;
	
	class convocatoriasController extends \core\Controller{
		
	    public function __construct($data) {
	       parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->_title = _("convocatorias.title");
	    	$this->_description = _("convocatorias.description");
	    	$this->addData(array("convocatorias" => $this->_dao->convocatoriasDAO()));
			$this->loadView();
	    }
	}
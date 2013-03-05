<?php

	namespace www\controllers;
	
	class escaparateController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->_title = _("escaparate.title");
	    	$this->_description = _("escaparate.description");
	    	$this->addData(array("imagenes" => $this->_dao->escaparateDAO()));
			$this->loadView();
		}
	}
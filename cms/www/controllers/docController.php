<?php

	namespace www\controllers;
	
	class docController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->_title = _("doc.title");
	    	$this->_description = _("doc.description");
	    	$this->addData(array("documentos" => $this->_dao->documentosDAO($this->_anyo)));
			$this->loadView();
		}
	}
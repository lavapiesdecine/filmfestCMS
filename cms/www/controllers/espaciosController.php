<?php 

	namespace www\controllers;

	class espaciosController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->tagBody = "onload='load()' onunload='GUnload()'";
	    	$this->_title = _("espacios.title");
	    	$this->_description = _("espacios.description");
	    	$this->addData(array("espacios" => $this->_dao->espaciosDAO($this->_anyo)));
			$this->loadView();  
	    }
	}
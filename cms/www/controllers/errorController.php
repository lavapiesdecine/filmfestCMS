<?php

	namespace www\controllers;

	class errorController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->_title = _("error.title");
	    	$this->_description = _("error.description");
	    	\core\util\Error::add(" error : ".implode(", ", $_SERVER));
			$this->loadView();
	        
	    }
	}
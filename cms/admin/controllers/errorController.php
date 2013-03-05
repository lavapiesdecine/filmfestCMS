<?php
 
	namespace admin\controllers;

	class errorController extends \core\AdminController{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	//Error::add(" error : ".implode(", ", $_SERVER));
			$this->loadView();
	        
	    }
	}

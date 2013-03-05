<?php

	namespace admin\controllers;
	
	class cacheController extends \core\AdminController{
		
	    public function __construct($data) {
	    	$this->_title = _("cache.title");
	    	$this->_description = _("cache.description");
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->addData(array("clear" => false));
	    	$this->loadView();
	    }
	    
	    
	    public function clear(){
	    	$this->addData(array("clear" => true));
	    	$this->loadView();
	    }
	    
	}
<?php
	namespace admin\controllers;
	
	class configController extends \core\AdminController{
		
	    public function __construct($data) {
	    	$this->_title = _("config.title");
	    	$this->_description = _("config.description");
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->loadView();
	    }
	    
}
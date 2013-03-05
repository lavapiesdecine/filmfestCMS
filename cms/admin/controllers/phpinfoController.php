<?php
	namespace admin\controllers;
	
	class phpinfoController extends \core\AdminController{
		
	    public function __construct($data) {
	    	$this->_title = _("phpinfo.title");
	    	$this->_description = _("phpinfo.description");
	    	parent::__construct($data);
	    }
	    
	    public function index(){
			$this->loadView();
	    }
	    
	}
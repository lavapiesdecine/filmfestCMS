<?php

	namespace admin\controllers;
	
	class logoutController extends \core\AdminController{
		
	     public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	session_destroy();
	    	$this->loadView();
	    }   
	    
	}
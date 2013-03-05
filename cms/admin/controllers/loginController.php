<?php

	namespace admin\controllers;
	
	class loginController extends \core\AdminController{
		
	     public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->loadView();
	    }   
	    
	}
<?php

namespace admin\controllers;

class anyoController extends \core\AdminController{
	
    public function __construct($data) {
    	parent::__construct($data);
    }
    
    public function index(){
    	$this->addData(array("ediciones" => $this->_dao->edicionesDAO()));
		$this->loadView();
    }   
}
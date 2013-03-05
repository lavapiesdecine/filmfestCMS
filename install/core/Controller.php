<?php
	
	class Controller{
		protected $_controller;
		protected $_next;
		protected $_title;
		protected $_description;
		protected $_data;
		protected $_step;
	
		public function __construct() {
			$class = explode("Controller", get_class($this));
			$this->_controller = empty($class[0])?"intro":$class[0];
			$this->_step = $this->getStep();
		}
	
		public function index(){
				$this->_data = array();
				$this->loadView();
		}
		
		protected function loadView(){
			extract($this->_data);
			include_once "vista/layout.phtml";
		}
		
		private function getStep(){
			$steps = unserialize(STEPS);
			for ($i = 1; $i <= count($steps); $i++) {
				$step = $steps[$i];
				if($this->_controller == $step[0]){
					$this->_next = ($i == count($steps) ? array() : $steps[$i+1]);
					return $i;
				}	 
			}
		}
		
	}
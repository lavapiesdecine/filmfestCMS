<?php

	class endController extends Controller{
	
		public function __construct() {
			$this->_title = _("end.tittle");
			$this->_description = _("end.description");
			unset($_SESSION['usuarioAdmin']);
			unset($_SESSION['bbdd']);
			unset($_SESSION["geo"]);
			parent::__construct();
		}
		
	}
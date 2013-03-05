<?php
	
	class introController extends Controller{
		
		public function __construct() {
			$this->_title = _("intro.tittle");
			$this->_description = _("intro.description");
			parent::__construct();
		}
		
	}
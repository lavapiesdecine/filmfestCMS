<?php

namespace admin\controllers;

class inicioController extends \core\AdminController{
	
    public function __construct($data) {
    	parent::__construct($data);
    }
    
    public function index(){
    	
    	if (empty($this->_id)){
	    	$perfiles=$this->_data->getUsuario()->getPerfiles();
	    	$html = "";
	    	$html .= "<div id='inicio'>";
	    	foreach($perfiles as $perfil){	
				$html .= "<nav>";
				$menu = $this->_dao->menuDAO($perfil->id);
				$html .="<h3 class='logo-$perfil->id'>"._("profile.".$perfil->profile.".name")."<span class='small'>"._("profile.".$perfil->profile.".description")."</span></h3>";
				$html .="<ul class='menu'>";
				foreach ($menu as $controller) {
					$html .="<li id='id-$controller->modulo'><a href='".$this->_baseUrl."$controller->modulo' title='" . _($controller->modulo.".description") . "'><strong> ". _($controller->modulo.".title") . "</strong></a>";
					$subMenu = $this->_dao->subMenuDAO($controller->id, $controller->id_perfil);
					$html .="<ul class='submenu'>";
					foreach ($subMenu as $subController) {
						$html .="<li><a href='".$this->_baseUrl."$subController->modulo' title='" . _($subController->modulo.".description") . "'>" . _($subController->modulo.".title") . "</a></li>";
					}
					$html .="</ul>";
					$html .="</li>";
				}
				$html .="</ul>";
				$html .="</nav>";
	    	}
	    	$html .= "</div>";
	    	$this->addData(array("html" =>$html));
			$this->loadView();
    	} else {
    		$defaultModulo = $this->_dao->adminModuloDefaultProfileDAO($this->_id);
    		header("Location: ".$this->_baseUrl.$defaultModulo->modulo);
    	}
    } 
    
    
}
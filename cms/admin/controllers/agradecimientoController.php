<?php

	namespace admin\controllers;
	
	class agradecimientoController extends \core\AdminController{
		
		public function __construct($data) {
			$this->_tabla = "donantes";
			 
			//img data
			$this->_imgUrl = URL_LOGO . "donantes" . "/";
			$this->_imgPath = LOGO_PATH . "donantes" . DS;
			$this->_imgAction = array("resize" => array("path" => LOGO_PATH . "donantes" . DS, 
														"height" =>"80", "width" => "80"));
			
			//metadata
			$this->_title = _("agradecimiento.title");
			$this->_description = _("agradecimiento.description");
			
			parent::__construct($data);
	    }
	    
	    public function index(){
	    	$nombre = "";
		    $classUpload = "upload-img";
		    $url = "";
		    $img = null;
		    $fileImg = "";
		   
		    if(!empty($this->_id)){
			    $agradecimientoDAO = $this->_dao->select($this->_id, $this->_tabla);
			    $nombre = $agradecimientoDAO->donante;
				if(!empty($agradecimientoDAO->logo)){
					$classUpload = "";
					$fileImg = $agradecimientoDAO->logo;
					$img = $this->_imgUrl . $fileImg;
				}
				$url = $agradecimientoDAO->web;
		    }
		 	
	    	$this->addData(array("listado" => $this->_dao->agradecimientosDAO(),
	    						 "nombre" => $nombre, 
	    						 "classUpload" => $classUpload, "img" => $img, "fileImg" => $fileImg, 
								 "url" => $url, "mostrarEdicion" => true));
			$this->loadView();
	    }
	   
	 	public function alta(){
	 		$campos = array("donante" => $_POST['nombre'], 
	 						"web" => $_POST['web'], 
	 						"logo" => $_POST['file_imagen']);
	 		
	 		echo $this->insert($_POST['id'], $campos);
	 	}
	 	
	 	public function upload(){
	 		$this->uploadImg($_POST['id_donante'], $_POST['id_nombre']);
	 	}
	 	
	 	
	 	
	    	
	}
<?php

	namespace admin\controllers;
	
	class agradecimientoController extends \core\AdminController{
		
		public function __construct($data) {
			$this->_tabla = "donantes";
			$this->_carpetaImg = "donantes";
			$this->_pathLogo = LOGO_PATH . $this->_carpetaImg . DS;
			$this->_urlLogo = URL_LOGO . $this->_carpetaImg . "/";
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
					$img = $this->_urlLogo . $fileImg;
				}
				$url = $agradecimientoDAO->web;
		    }
		 	
	    	$this->addData(array("listado" => $this->_dao->agradecimientosDAO(),
	    						 "nombre" => $nombre, "classUpload" => $classUpload, "img" => $img, "fileImg" => $fileImg, 
								 "url" => $url, "mostrarEdicion" => true));
			$this->loadView();
	    }
	   
	 	public function alta(){
	 		$campos = array("donante" => $_POST['nombre'], "web" => $_POST['web'], "logo" => $_POST['file_imagen']);
			echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
	    }
	     
		public function upload(){ 
	     	if(isset($_FILES['imagen'])){
		    	try{
		    		$actions = array(array("action" => "resize", "path" => $this->_pathLogo, "height" =>"80"));
		    		$nombreImg = $this->uploadImagen($actions);
		    		$urlImagen = $this->_urlLogo . $nombreImg;
		    		
		    		if(!empty($_POST['id_donante'])){
		    			$ok = $this->_dao->update($_POST['id_donante'], array("logo" => $nombreImg), $this->_tabla);
					}
					echo "<input type='hidden' id='nombre_imagen' value='$nombreImg' />";
					echo "<img src='$urlImagen' height='100px' width='100px'/>";
		    	} catch (\Exception $e) {
		    		echo("<p>problemas al subir la imagen</p>");
		    		\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
		    	}
		    }
	     }
	    	
	}
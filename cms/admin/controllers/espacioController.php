<?php

namespace admin\controllers;

class espacioController extends \core\AdminController{
	
     public function __construct($data) {
    	$this->_tabla = "espacios";
    	$this->_carpetaImg = "espacios";
    	$this->_pathLogo = LOGO_PATH . $this->_carpetaImg . DS;
    	$this->_urlLogo = URL_LOGO . $this->_carpetaImg . "/";
    	$this->_title = _("espacio.title");
    	$this->_description = _("espacio.description");
    	parent::__construct($data);
    }
    
    public function index(){
		
		$direccion = "";
		$url = "";
		$nombre = "";
		$descripcion="";
		$email = "";
		$telefono = "";
  		$img = null;
   		$classUpload = "upload-img";
   		$fileImg = "";
   		$longitud = LONGITUD;
   		$latitud = LATITUD;
   		
	    if(!empty($this->_id)){
	   		$espacioDAO = $this->_dao->select($this->_id, $this->_tabla);
		    $fileImg = $espacioDAO->espacio;
		    if(!empty($espacioDAO->logo)){
				$classUpload = "";
				$img = $this->_urlLogo . $espacioDAO->logo;
			}
		    $direccion = $espacioDAO->direccion;
		    $url = $espacioDAO->url;
		    $nombre = $espacioDAO->espacio;
		    $descripcion = $espacioDAO->descripcion;
		    $email = $espacioDAO->email;
		    $telefono = $espacioDAO->telefono;
		    $longitud = $espacioDAO->longitud;
		    $latitud = $espacioDAO->latitud;
		    $fileImg = $espacioDAO->logo; 
	    }
	 	
    	$this->addData(array("listado" => $this->_dao->espaciosDAO(),
    							"direccion" => $direccion,
								"url" => $url,
								"nombre" => $nombre,
								"descripcion"=>$descripcion,
								"email" => $email,
								"telefono"=>$telefono,
						  		"img" => $img,
    							"fileImg" => $fileImg, 
						   		"classUpload" => $classUpload,
								"longitud" => $longitud,
    							"latitud" => $latitud));
		
		$this->loadView();
    }   
    
    public function alta(){
  		$campos = array("espacio" => $_POST['nombre'],
		"direccion" => $_POST['direccion'],
		"latitud" => $_POST['id_latitud'],
  		"longitud" => $_POST['id_longitud'],
		"logo" => $_POST['file_imagen'],
  		"descripcion" => $_POST['descripcion'],
  		"url" => $_POST['web'],
		"telefono" => $_POST['telefono'],
  		"email" => $_POST['email']);
  		echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
    	
    }
    
    public function upload(){
		if(isset($_FILES['imagen'])){
			try{
				$actions = array(array("action" => "resize", "path" => $this->_pathLogo, "height" =>"80"));
				$nombreImg = $this->uploadImagen($actions);
				$urlImagen = $this->_urlLogo . $nombreImg;
				if(!empty($_POST['id_espacio'])){
					$ok = $this->_dao->update($_POST['id_espacio'], array("logo" => $nombreImg), $this->_tabla);
				} 
				echo "<input type='hidden' id='nombre_imagen' name='nombre_imagen' value='$nombreImg' />";
				echo "<img src='$urlImagen' height='100px' width='100px' />";
			} catch (\Exception $e) {
			echo("<p>problemas al subir la imagen</p>");
    			\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
			}
		}
	}

}
<?php

namespace admin\controllers;

class espacioController extends \core\AdminController{
	
     public function __construct($data) {
    	$this->_tabla = "espacios";
    	
    	$this->_imgUrl = URL_LOGO . "espacios" . "/";
    	$this->_imgPath = LOGO_PATH . "espacios" . DS;
    	$this->_imgAction = array("resize" => array("path" => $this->_imgPath, 
    												"height" =>"80", "width" => "80"));
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
				$img = $this->_imgUrl . $espacioDAO->logo;
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
  		
  		echo $this->insert($_POST['id'], $campos);
    }
        
    public function upload(){
    	$this->uploadImg($_POST['id_espacio'], $_POST['id_nombre']);
    }

}
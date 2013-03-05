<?php

	namespace admin\controllers;
	
	class viewusuarioController extends usuarioController{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	if(self::redirectUsuario()){;
	    		$nivelAcceso = 0;
		    	$usuario = "";
		    	$email = "";
		    	$nombre ="";
		    	$classUpload = "upload-img";
		    	$img = null;
		    	$fileImg = "";
		    	$perfiles = array();
		    		
		    	if(!empty($this->_id)){
		    		$usuarioDAO = $this->_dao->select($this->_id, $this->_tabla);
		    		$nombre = $usuarioDAO->usuario;
		    		$email = $usuarioDAO->email;
		    		$perfiles = $this->_dao->nivelesNavegacionUsuarioDAO($usuarioDAO->id);
		    		if(!empty($usuarioDAO->logo)){
		    			$classUpload = "";
		    			$fileImg = $usuarioDAO->logo;
		    			$img = $this->_urlLogo . $fileImg;
		    		}
		    	}
		    	
		    	$this->addData(array("perfiles" => $perfiles,
		    			"nivelAcceso" => $nivelAcceso,
		    			"img" => $img, "fileImg" => $fileImg, "classUpload" => $classUpload,
		    			"nombre" => $nombre, "email" => $email));
		    		
		    	$this->loadView();
	    	}
	    }

	    private function redirectUsuario(){
	    	$moduloUsuario = "usuario";
	    	$perfilesUsuario = "";
	    	foreach ($this->_data->getUsuario()->getPerfiles() as $index =>$perfil){
	    		$perfilesUsuario .= $perfil->id.($index<(count($this->_data->getUsuario()->getPerfiles())-1) ? ",":"");
	    	}
	    	$perfilModulo = $this->_dao->adminModuloNombreDAO($moduloUsuario, $perfilesUsuario);
	    	if(!empty($perfilModulo)){
	    		header("Location: ".$this->_baseUrl."$moduloUsuario/$this->_id");
	    		exit;
	    	} else {
	    		return true;
	    	}
	    }
	}
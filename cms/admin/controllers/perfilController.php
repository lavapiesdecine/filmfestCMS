<?php

	namespace admin\controllers;
	
	class perfilController extends \core\AdminController{
		
	     public function __construct($data) {
	     	$this->_tabla = "perfiles";
	     	$this->_carpetaImg = "";
	     	$this->_pathLogo = LOGO_ADMIN_PATH;
	     	$this->_title = _("perfil.title");
	     	$this->_description = _("perfil.description");
	        parent::__construct($data);
	    }
	    
	    public function index(){
			$idModuloDefault=0;
			$nombre = "";
		   	$classUpload = "upload-img";
		    $img = null;
		    $fileImg = "";
		    $modulos = array();
		   	
		   	if(!empty($this->_id)){
		   		$perfilDAO = $this->_dao->select($this->_id, $this->_tabla);
		   		$nombre = $perfilDAO->profile;
		   		$modulos = $this->_dao->modulosPerfilDAO($this->_id);
		   		if(!empty($perfilDAO->logo)){
					$classUpload = "";
					$fileImg = $perfilDAO->logo;
					$img = URL_IMG_ADMIN.$fileImg;
				}
		    }
		 	
	    	$this->addData(array("listado" => $this->_dao->nivelesAccesoDAO(),
	    						 "modulos" => $modulos,
	    						 "modulosPosibles" => $this->_dao->modulosPosiblesDAO(),
	    						 "nombre" => $nombre,"classUpload" => $classUpload,
								 "img" => $img, "fileImg" => $fileImg));
			
			$this->loadView();
	
	    }    
	    
	 	public function alta(){
	 		$campos = array("nombre" => $_POST['nombre'], "descripcion" => $_POST['descripcion'], "logo" => $_POST['file_imagen']);
			echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
		}
	    
		public function upload(){
			if(isset($_FILES['imagen'])){
				try{
					$actions = array(array("action" => "resize", "path" => $this->_pathLogo , "height" =>"50", "grayscale" => true));
					$nombreImg = $this->uploadImagen($actions);
					$urlImagen = URL_IMG_ADMIN . $nombreImg;
					if(!empty($_POST['id_perfil'])){
						$ok = $this->_dao->update($_POST['id_perfil'], array("logo" => $nombreImg), $this->_tabla);
					}
			   		echo "<input type='hidden' id='nombre_imagen' value='$nombreImg' />";
					echo "<img src='$urlImagen' height='100px' width='100px' />";
				} catch (\Exception $e) {
					echo("<p>problemas al subir la imagen</p>");
					Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
				}
			}
		}
	   
	}
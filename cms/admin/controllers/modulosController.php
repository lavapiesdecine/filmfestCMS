<?php

namespace admin\controllers;

class modulosController extends \core\AdminController{
	
    public function __construct($data) {
    	$this->_tabla = "modulos";
    	$this->_pathLogo = LOGO_ADMIN_PATH;
    	$this->_urlLogo = URL_IMG_ADMIN;
    	$this->_title = _("modulos.title");
    	$this->_description = _("modulos.description");
    	parent::__construct($data);
    }
    
    
    public function index(){
     
      	$nombre = "";
	   	$descripcion = "";
	   	$classUpload = "upload-img";
	   	$nivelAcceso = 0;
	   	$moduloAdmin = "";
	   	$idModuloPadre = 0;
	   	$img = null;
	   	$fileImg = "";
	   	
	   	$modulosPadre = $this->_dao->modulosPadreDAO(null);
	 
	   if(!empty($this->_id)){
	   		$adminModuloDAO = $this->_dao->select($this->_id, $this->_tabla);
			$moduloAdmin = $adminModuloDAO->modulo;
			$idModuloPadre = $adminModuloDAO->modulo_padre;
			$modulosPadre = $this->_dao->modulosPadreDAO($adminModuloDAO->id);
			if(!empty($adminModuloDAO->logo)){
				$classUpload = "";
				$fileImg = $adminModuloDAO->logo;
				$img = $this->_urlLogo.$fileImg;
			}
		}
	 	
    	$this->addData(array("listado" => $this->_dao->adminModulosDAO(),
    							"modulosPadre" => $modulosPadre,
    							"idModuloPadre" => $idModuloPadre,
    							"moduloAdmin" => $moduloAdmin,
    							"img" => $img, "fileImg" => $fileImg,
    							"classUpload" => $classUpload));
		
		$this->loadView();

    }   
    
    
 	public function alta(){
  		$campos = array("modulo" => $_POST['modulo'],
		"modulo_padre" => $_POST['id_modulopadre'],
		"logo" => $_POST['file_imagen']);
		echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
 		
    }
   	
    public function load(){
    	
    	if(isset($_POST['id_nivelacceso'])){
			$modulosPadre = $this->_dao->modulosPadreDAO(null);
			$options_str = "[";
			$tmp_array = array();
			array_push($tmp_array,"['0','selecciona..']");
			foreach ($modulosPadre as $moduloPadre) {
				array_push($tmp_array,"['".$moduloPadre->id."','".$moduloPadre->titulo."']");
			}
			$options_str .= join(',',$tmp_array);
			$options_str .= "]";
			print $options_str;
    	}
    }
    
    public function upload(){
    	if(isset($_FILES['imagen'])){
    		try{
    			$actions = array(array("action" => "resize", "path" => $this->_pathLogo, "height" =>"50"));
    			$nombreImg = $this->uploadImagen($actions);
    			$urlImagen = $this->_urlLogo . $nombreImg;
    			if(!empty($_POST['id_modulo'])){
    				$ok = $this->_dao->update($_POST['id_modulo'], array("logo" => $nombreImg), $this->_tabla);
    			}
    			echo "<input type='hidden' id='nombre_imagen' value='$nombreImg' />";
    			echo "<img src='$urlImagen' height='100px' width='100px' />";
    		} catch (\Exception $e) {
    			echo("<p>problemas al subir la imagen</p>");
    			\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
    		}
    	}
    }
    
}
<?php

namespace admin\controllers;

class modulosController extends \core\AdminController{
	
    public function __construct($data) {
    	$this->_tabla = "modulos";
    	
    	$this->_title = _("modulos.title");
    	$this->_description = _("modulos.description");
    	
    	$this->_imgPath = ADMIN_PATH . 'vista' . DS . 'skins' . DS . "commons" . DS .'img' . DS . "modulos" . DS;
    	$this->_imgUrl = BASE_URL_ADMIN . "/vista/skins/commons/img/modulos/";
		$this->_imgAction = array("resize" => array("path" => $this->_imgPath, 
													"height" =>"40", "width => 40"));
    	 
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
				$img = $this->_imgUrl.$fileImg;
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
 		
 		try{
 			
 			/* logo por defecto */
	 		if (!empty($_POST['file_imagen'])){
	 			$logo = $_POST['file_imagen'];
	 		} else {
	 			$logo = \core\util\Util::stripAccents($_POST['modulo']) . ".jpg";
	 			\core\util\UtilFile::copyFile($this->_imgPath . "default.jpg",
	 										  $this->_imgPath . $logo);
	 		}
	 		
	 		$campos = array("modulo" => $_POST['modulo'],
							"modulo_padre" => $_POST['id_modulopadre'],
							"logo" => $logo);
	  		
	  		$this->_dao->insertUpdate($id, $campos, $this->_tabla);
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
			
		}
		echo json_encode($this->_result);
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
    	$this->uploadImg($_POST['id_modulo'], $_POST['id_nombre']);
    }
    
}
<?php

	namespace admin\controllers;
	
	class perfilController extends \core\AdminController{
		
	     public function __construct($data) {
	     	$this->_tabla = "perfiles";
			
	     	$this->_title = _("perfil.title");
	     	$this->_description = _("perfil.description");
	     	
	     	$this->_imgPath = ADMIN_PATH . 'vista' . DS . 'skins' . DS . "commons" . DS .'img' . DS . "profiles" . DS;
	     	$this->_imgUrl = BASE_URL_ADMIN . "/vista/skins/commons/img/profiles/";
	     	$this->_imgAction = array(array("action" => "resize", "path" => $this->_imgPath , "height" =>"50"));
	     	
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
					$img = $this->_imgUrl.$fileImg;
				}
		    }
		 	
	    	$this->addData(array("listado" => $this->_dao->nivelesAccesoDAO(),
	    						 "modulos" => $modulos,
	    						 "modulosPosibles" => $this->_dao->modulosPosiblesDAO($this->_id),
	    						 "nombre" => $nombre,"classUpload" => $classUpload,
								 "img" => $img, "fileImg" => $fileImg));
			
			$this->loadView();
	
	    }    
	    
	 	public function alta(){
			$id = $_POST['id'];
			
			try{
				$this->_dao->startTransaction();
				
				if (!empty($_POST['file_imagen'])){
					$logo = $_POST['file_imagen'];
				} else {
					//copiar defecto
					$logo = \core\util\Util::stripAccents($_POST['nombre']).".jpg" ;
					\core\util\UtilFile::copyFile($this->_imgPath . "default.jpg",
												  $this->_imgPath . $logo);
				}
				
				$campos = array("profile" => $_POST['nombre'], "logo" => $logo);
				
				if(!empty($id)){
					$this->_dao->update($id, $campos, $this->_tabla);
				}
				else{
					$id = $this->_dao->insertId($campos, $this->_tabla);
				}
					
				if (!empty($_POST['modulosSelected'])){
					$modulos = explode(",", $_POST['modulosSelected']);
					if(!empty($id)){
						$this->_dao->deleteModuloPerfil($id);
						foreach ($modulos as $modulo) {
							$this->_dao->insert(array("id_modulo" => $modulo, "id_perfil" => $id), "modulo_perfil");
						}
					}
				}
				$this->_dao->commit();
			} catch (\Exception $e){
				$this->_result = array("ok" => false, "msg" => $e->getMessage());
				$this->_dao->rollback();
			}
			echo json_encode($this->_result);
		}
		
		public function upload(){
			$this->uploadImg($_POST['id_perfil'], $_POST['id_nombre']);
		}
	    
	}
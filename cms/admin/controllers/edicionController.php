<?php

namespace admin\controllers;

class edicionController extends \core\AdminController {
	
    public function __construct($data) {
     	$this->_tabla = "ediciones";
     	$this->_carpetaImg = "carteles";
     	$this->_title = _("edicion.title");
	    $this->_description = _("edicion.description");
    	parent::__construct($data);
    }
    
    public function index(){
		$nombre = "";
	   	$descripcion = "";
	   	$fechaInicio= "";
	   	$fechaFin= "";
	    $fileImg= "";
	    $classUpload = "upload-img";
	    $thumbnail = null;
	    $fileImg = null;
	   	$anyo = $this->_anyo;
	   	$img = null;
	    
	    if(!empty($this->_id)){
	   		$edit = true;
		    $edicionDAO = $this->_dao->select($this->_id, $this->_tabla);
		    $nombre = $edicionDAO->nombre;
		    $descripcion = $edicionDAO->descripcion;
			$ini = explode("-", $edicionDAO->fecha_inicio);
			$fechaInicio = $ini[2]."/".$ini[1]."/".$ini[0];
			$fin = explode("-", $edicionDAO->fecha_fin);
			$fechaFin = $fin[2]."/".$fin[1]."/".$fin[0];
			$anyo = $this->_id;
			$fileImg = $edicionDAO->cartel;
			
			if(!empty($fileImg)){
				$classUpload = "";
				$img = URL_SKINS . $this->_id . "/" . IMG . "/" . $fileImg;
			}
			
	    }
	 	
    	$dataController = array("listado" => $this->_dao->edicionesDAO(),
    							"langs" => $this->_dao->langsEdicionDAO($anyo),
    							"langsDisponibles" => $this->_dao->langsDisponiblesEdicionDAO($anyo),
    							"nombre" => $nombre,
								"descripcion" => $descripcion,
    							"dateInicio" => $fechaInicio,
								"dateFin" => $fechaFin,
				    			"fileImg" => $fileImg,
				    			"img" => $img,
				    			"classUpload" => $classUpload);
		
    	$this->_data->setData(array_merge((array)$this->_data->getData(), $dataController));
		$this->loadView();
    }   
    
    public function alta(){
 		try{
	    	$id = $_POST['id'];
	    	$this->_dao->startTransaction();
	    	$ini = explode("/", $_POST['id_dia_inicio']);
			$fecInicio = $ini[2]."-".$ini[1]."-".$ini[0];
			$fin = explode("/", $_POST['id_dia_fin']);
			$fecFin = $fin[2]."-".$fin[1]."-".$fin[0];
	    	
			/*
			$idImagen = $_POST['id_imagen'];
			if(!empty($idImagen)){
				$galeria = $this->_dao->galeriaCarpetaDAO($this->_carpetaImg);
				$idImagen = $this->_dao->insertId(array("imagen" => $_POST['file_imagen'], "id_galeria"=>$galeria->id, "descripcion"=>$_POST['nombre'], "alta"=>'N'), "imagenes");
			}
			*/
			$cartel = \core\util\Util::stripAccents($_POST['nombre']);
			$campos = array("nombre"=>$_POST['nombre'],"descripcion"=>$_POST['descripcion'], "fecha_inicio"=>$fecInicio, "fecha_fin"=>$fecFin, "cartel"=>$cartel);
			 
			if(!empty($id)){
				$this->_dao->update($id, $campos, $this->_tabla);
			} else{
				$id = $fin[2];
				
				//genera carpetas de imagenes 
				$paths = array (IMG_PATH . "peliculas" . DS . $id,
								IMG_PATH . "peliculas" . DS . $id . DS . THUMBNAIL, 
								IMG_PATH . "peliculas" . DS . $id . DS . MEDIUM);
								
				foreach ($paths as $path){
					if(!is_dir($path) && !mkdir($path)){
						break;
					}
				}
				
				$campos = array_merge($campos, array("id" => $id));
				if($this->_dao->insert($campos, $this->_tabla)){
					$this->_dao->insert(array("id" => $id, "url"=>"", "cartel"=>$cartel, "alta"=>"N"), "convocatorias");
				}
				
				//TODO hacer una funcion recursiveCopy que funcione
				\core\util\Util::recursiveCopy(WEB_PATH . "vista" . DS . "skins" . DS . "2013", WEB_PATH . "vista"  .DS . "skins" .DS . $id);
				
			}
				
			//langs
			$langs = explode(",", $_POST['langsSelected']);
			if($this->_dao->deleteLangEdicion($id)){
				foreach ($langs as $lang) {
					$ok = $this->_dao->insert(array("id_edicion" => $id, "lang" => $lang), "lang_edicion");
				}
			}
			
			$this->_dao->commit();
			echo true;
			
 		} catch (\Exception $e) {
	    	\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
	    	$this->_dao->rollback();
	    	echo false;
	    }
    }
    
	public function delete(){
 		$id = $_POST['id'];
    	if(!empty($id)){
			if($this->_dao->delete($id, $this->_tabla)){
				$this->_dao->delete($id, "convocatorias");
				\core\util\Util::recursirveRmdir(IMG_PATH . "peliculas". DS . $id);
				\core\util\Util::recursirveRmdir(SKINS_PATH . $id);
			}  
		}
    }
	
	public function deleteImagen(){
 		$id = $_POST['id'];
    	if(!empty($id)){
    		$edicionDAO = $this->_dao->select($id, $this->_tabla);
			$this->_dao->update($id, array("cartel" => ""), $this->_tabla);
		}
	}
	
	public function upload(){ 
		if(isset($_FILES['imagen']) && !empty($_POST['id_edicion']) ){
	     	try{
	     		$id = $_POST['id_edicion'];
	     		$path = SKINS_PATH . $id . DS . IMG;
	     		if(!is_dir(SKINS_PATH . $id)){
	     			mkdir(SKINS_PATH . $id);
	     			mkdir(SKINS_PATH . $id . DS . IMG);
	     		}	
	     			
     			$actions = array(array("action" => "save", "path" => $path));
     			
	    		$nombreImagen = $this->uploadImagen($actions);
	    		$urlImagen = URL_SKINS . $id . "/" . IMG . "/" . $nombreImagen;
	    		
	    		$edicionDAO = $this->_dao->select($id, $this->_tabla);
	    		if(!empty($edicionDAO)){
	    			$this->_dao->update($id, array("cartel" => $nombreImagen), $this->_tabla);
	    		}
	    		
				echo "<input type='hidden' id='nombre_imagen' value='$nombreImagen' />";
				echo "<img src='$urlImagen' height='100px' width='100px'/>";
	     		
				
	    	} catch (\Exception $e) {
	    		echo("<p>problemas al subir la imagen</p>");
	    		\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
	    	}
	    }
    }
    
       
}
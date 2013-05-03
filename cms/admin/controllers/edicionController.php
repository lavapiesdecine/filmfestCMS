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
 		$id = $_POST['id'];

 		try{
 			$this->_dao->startTransaction();
	    	$ini = explode("/", $_POST['id_dia_inicio']);
			$fecInicio = $ini[2]."-".$ini[1]."-".$ini[0];
			$fin = explode("/", $_POST['id_dia_fin']);
			$fecFin = $fin[2]."-".$fin[1]."-".$fin[0];
	    	
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
					\core\util\UtilFile::mkdir($path);
				}
				
				$campos = array_merge($campos, array("id" => $id));
				$this->_dao->insert($campos, $this->_tabla);
				$this->_dao->insert(array("id" => $id, "url"=>"", "cartel"=>$cartel, "alta"=>"N"), "convocatorias");
				
				\core\util\UtilFile::recursiveCopy(WEB_PATH . "vista" . DS . "skins" . DS . "2013", WEB_PATH . "vista"  .DS . "skins" .DS . $id);
				
			}
				
			//langs
			$langs = explode(",", $_POST['langsSelected']);
			$this->_dao->deleteLangEdicion($id);
			foreach ($langs as $lang) {
				$this->_dao->insert(array("id_edicion" => $id, "lang" => $lang), "lang_edicion");
			}
			
			$this->_dao->commit();

 		} catch (\Exception $e) {
 			$this->_result = array("ok" => false, "msg" => $e->getMessage());
	    	$this->_dao->rollback();
	    }
	    echo json_encode($this->_result);
    }
    
	public function delete(){
 		$id = $_POST['id'];
 		try{
	    	if(!empty($id)){
				$this->_dao->delete($id, $this->_tabla);
				$this->_dao->delete($id, "convocatorias");
				\core\util\UtilFile::recursirveRmdir(IMG_PATH . "peliculas". DS . $id);
				\core\util\UtilFile::recursirveRmdir(SKINS_PATH . $id);
			}
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		}
		echo json_encode($this->_result);
    }
	
	public function deleteImagen(){
 		$id = $_POST['id'];
 		try{
	    	if(!empty($id)){
	    		$edicionDAO = $this->_dao->select($id, $this->_tabla);
				$this->_dao->update($id, array("cartel" => ""), $this->_tabla);
			}
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		}
		echo json_encode($this->_result);
	}
	
	public function upload(){ 
		if(isset($_FILES['imagen']) && !empty($_POST['id_edicion']) ){
			
			$id = $_POST['id_edicion'];
			
			try{
	     		$this->_dao->startTransaction();
	     		\core\util\UtilFile::mkdir(SKINS_PATH . $id);
	     		
	     		$path = SKINS_PATH . $id . DS . IMG . DS;
	     		\core\util\UtilFile::mkdir($path);
	     		
	     		$this->_imgPath = GALERIAS_PATH . $this->_carpetaImg . DS;
     			$this->_imgAction = array("crop" => array( "path" => $path . THUMBNAIL . DS, "height" =>"100", "width" => "100"),
     							 		  "md" => array("path" => $path . MEDIUM . DS, "height" =>"150", "width" => "100"),
     							 		  "save" => array("path" => $path));
     			$this->_imgUrl = URL_SKINS . $id . "/" . IMG . "/";
     			
     			$nombreImg =  empty($_POST["id_nombre"]) ? date('YmdHms') : \core\util\Util::stripAccents($_POST["id_nombre"]);
     			 
     			\core\util\UtilImage::processImg($_FILES['imagen'], $this->_imgAction, $nombreImg);
     			 
     			$thumbnail = $nombreImg . ".jpg";
     			
	    		$edicionDAO = $this->_dao->select($id, $this->_tabla);
	    		if(!empty($edicionDAO)){
	    			$this->_dao->update($id, array("cartel" => $thumbnail), $this->_tabla);
	    		}
	    		
	    		$html = "<head><style type='text/css'> body{margin: 0;}</style></head>"
						."<input type='hidden' id='nombre_imagen' value='$thumbnail' />"
						."<img src='".$this->_imgUrl . $thumbnail."' height='100px' width='100px'/>";
	    		echo $html;
	     		
				$this->_dao->commit();
				
			} catch (\Exception $e) {
				$this->_dao->rollback();
				$this->showError($e->getMessage());
	    	}
	    	
	    }
    }
    
       
}
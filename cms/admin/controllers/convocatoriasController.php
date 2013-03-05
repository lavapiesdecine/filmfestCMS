<?php
namespace admin\controllers;

class convocatoriasController extends \core\AdminController {
	
     public function __construct($data) {
        $this->_tabla = "convocatorias";
     	$this->_carpetaImg = "carteles";
     	$this->_title = _("convocatorias.title");
	    $this->_description = _("convocatorias.description");
     	parent::__construct($data);
    }
    
    public function index(){
    	
	   	if(empty($this->_id)){
	   		$this->_id = DEFAULT_ANYO;
	   	}	
   		$edit = true;
	    $edicionDAO = $this->_dao->convocatoriaDAO($this->_id);
	    $nombre	= $edicionDAO->nombre;
	    $url = $edicionDAO->url;
		$descripcion = $edicionDAO->descripcion;
		$classUpload = "upload-img";
   		$fileImg = $edicionDAO->cartel;
   		
		if(!empty($fileImg)){
			$classUpload = "";
			$img = URL_GALERIAS.$this->_carpetaImg."/".THUMBNAIL."/$fileImg";
		}
	    
    	$this->addData(array("listado" => $this->_dao->convocatoriasDAO(),
    						 "nombre" => $nombre, "paginas" => $this->_dao->urlPaginasDAO($this->_id),
    						 "url" => $url, "nombre" => $nombre,
							 "descripcion" => $descripcion, "fileImg" => $fileImg,
    						 "img" => $img, "classUpload" => $classUpload));
		$this->loadView();

    }   
    
	public function alta(){
 		$campos = array("url" => $_POST['id_url'],
				"cartel" => $_POST['file_imagen'],
				"descripcion" => $_POST['descripcion']);
		
		echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
		
    }
    
	public function upload(){ 
     	if(isset($_FILES['imagen'])){
	     	$thumbnail = "100x100";
	     	$medium = "200x150";
	     	try{
	    		$this->_pathImg = GALERIAS_PATH . $this->_carpetaImg . DS;
	    		$nombreImagen = $this->uploadImagen($thumbnail, $medium, "");
	    		$urlImagen = URL_GALERIAS . $this->_carpetaImg . "/" . $nombreImagen;
	    		
	    		if(!empty($_POST['id_edicion'])){
					$ok = $this->_dao->update($_POST['id_edicion'], array("cartel" => $nombreImagen), $this->_tabla);
				} 
				echo "<input type='hidden' id='nombre_imagen' value='$nombreImagen' />";
				echo "<img src='$urlImagen' height='100px' width='100px'/>";
				
	    	} catch (\Exception $e) {
	    		echo("<p>problemas al subir la imagen</p>");
	    		\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
	    	}
	    }
    }
    
	public function deleteImagen(){
 		$id = $_POST['id'];
 		echo $id;
    	if(!empty($id)){
    		$edicionDAO = $this->_dao->select($id, $this->_tabla);
			if($this->_dao->update($id, array("cartel" => ""), $this->_tabla) && !empty($edicionDAO->cartel)){
				unlink(GALERIAS_PATH . $this->_carpetaImg . DS . $edicionDAO->cartel);
				unlink(GALERIAS_PATH . $this->_carpetaImg . DS . THUMBNAIL . DS . $edicionDAO->cartel);
				unlink(GALERIAS_PATH . $this->_carpetaImg . DS . MEDIUM . DS . $edicionDAO->cartel);
			}	
		}
	}
    
 	
}
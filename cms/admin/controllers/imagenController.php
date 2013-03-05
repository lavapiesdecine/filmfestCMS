<?php 

namespace admin\controllers;

class imagenController extends \core\AdminController{
	
    public function __construct($data) {
    	$this->_tabla = "imagenes";
    	$this->_title = _("imagen.title");
	    $this->_description = _("imagen.description");
    	parent::__construct($data);
    }
    
    public function index(){
		$idImg = 0;
		$galeria = "";
		$idGaleria = 0;
		$url = "";
		$descripcion = "";
		$fileImg = "";
		$classUpload = "upload-img";
		$img = null;
		$sizeFile = "";
		$dimensiones = "";
		$iconImg = "";
		$video = true;
		 
		if(!empty($this->_id)){
			$imagenDAO = $this->_dao->imagenDAO($this->_id);
			$idImg = $imagenDAO->id;
			$galeria = $imagenDAO->galeria;
			$idGaleria = $imagenDAO->id_galeria;
			$descripcion = $imagenDAO->descripcion;
			$fileImg = $imagenDAO->imagen;
			if(empty($imagenDAO->url_video)){
				$url = URL_GALERIAS."$galeria/$fileImg";
				$iconImg = "view";
				$video = false;
			} else {
				$url = $imagenDAO->url_video;
				$iconImg = "play";
			}
			if(!empty($imagenDAO->imagen)){
				$classUpload = "";
				$img = URL_GALERIAS.$galeria."/".THUMBNAIL."/".$fileImg;
			}
			$image = new \core\util\Image(GALERIAS_PATH.$galeria.DS.$fileImg);
			$sizeFile = $image->getSize();
			$dimensiones = $image->getDimension();
		}		
	 	
    	$this->addData(array("listado" => $this->_dao->imagenesDAO(),
    						 "galerias" => $this->_dao->galeriasDAO(),
    						 "idImg" => $idImg,
							 "galeria" => $galeria,
							 "idGaleria" => $idGaleria,
							  "url" => $url,
						      "descripcion" => $descripcion,
							  "fileImg" => $fileImg,
							  "classUpload" => $classUpload,
							  "img" => $img,
							  "sizeFile" => $sizeFile, 
							  "dimensiones" => $dimensiones,
    						  "iconImg" => $iconImg,
    						  "video" => $video));
    	
		$this->loadView();
    }
    
    public function alta(){
    	
    	$campos = array("imagen" => $_POST['file_imagen'],
						"descripcion" => $_POST['descripcion'],
						"id_galeria" => $_POST['id_galeria'],
    					"url_video" => \core\util\Util::getUrlVideo($_POST['id_video']));
		
		echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
    	
    }
    
	public function delete(){
 		
    	$id = $_POST['id'];
    	
 		if(!empty($id)){
 			$imagenDAO = $this->_dao->imagenDAO($id);
			if(isset($imagenDAO->imagen)){
				if($this->_dao->delete($id, $this->_tabla)){
					unlink(GALERIAS_PATH.$imagenDAO->galeria.DS.$imagenDAO->imagen);
					unlink(GALERIAS_PATH.$imagenDAO->galeria.DS.THUMBNAIL.DS.$imagenDAO->imagen);
				}
			}
		}
		echo $ok;
    }

    public function load(){
    	if(isset($_POST['id_galeria'])){
			$galeria = $this->_dao->select($_POST['id_galeria'], "galerias");
			echo $galeria->galeria;
		}
    }
    
    public function upload(){
    	if(isset($_FILES['imagen'])){
    		try{
    			$path = GALERIAS_PATH . $_POST['galeria'] . DS;
    			$actions = array(array("action" => "crop", "path" => $path . THUMBNAIL . DS, "height" =>"100", "width" => "100"),
    							 array("action" => "save", "path" => $path));
    			$nombreImg = $this->uploadImagen($actions);
    			$urlImagen = URL_GALERIAS . $_POST['galeria'] . "/" . $nombreImg;
    			$id = $this->_dao->insertId(array("imagen" => $nombreImg, "id_galeria" => $_POST['id_galeria_img']), $this->_tabla);
    			echo "<input type='hidden' id='id' value='$id' />";
    			echo "<input type='hidden' id='nombre_imagen' value='$nombreImg' />";
    			echo "<img src='$urlImagen' height='100px' width='100px' />";
    		} catch (\Exception $e) {
    			echo("<p>problemas al subir la imagen</p>");
    			\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
    		}
    	}
    }

}	  
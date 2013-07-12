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
				$img = \core\util\Util::getImageVideo($url);
				$iconImg = "play";
			}
			if(!empty($imagenDAO->imagen)){
				$classUpload = "";
				$img = URL_GALERIAS . $galeria . "/" . THUMBNAIL . "/" . $fileImg;
			}
			$image = new \core\classes\Imagen(GALERIAS_PATH.$galeria.DS.$fileImg);
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
    	
    	echo $this->insert($_POST['id'], $campos);
		
    }
    
	public function delete(){
 		$id = $_POST['id'];
    	try{
	 		if(!empty($id)){
	 			$imagenDAO = $this->_dao->imagenDAO($id);
				if(isset($imagenDAO->imagen)){
					echo "loooo";
					$this->_dao->delete($id, $this->_tabla);
					\core\util\UtilFile::deleteFile(GALERIAS_PATH.$imagenDAO->galeria.DS.$imagenDAO->imagen);
					\core\util\UtilFile::deleteFile(GALERIAS_PATH.$imagenDAO->galeria.DS.THUMBNAIL.DS.$imagenDAO->imagen);
				}
			}
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		}
		echo json_encode($this->_result);
    }

    public function load(){
    	if(isset($_POST['id_galeria'])){
			$galeria = $this->_dao->select($_POST['id_galeria'], "galerias");
			echo $galeria->galeria;
		}
    }
    
    public function upload(){
    	try{
    		if(isset($_FILES['imagen'])){
	    		
	    		$this->_imgPath = GALERIAS_PATH . $_POST['galeria'] . DS;
	    		$this->_imgUrl = URL_GALERIAS . $_POST['galeria'] . "/";
	    		$this->_imgAction = array("crop" => array("path" => $this->_imgPath . THUMBNAIL . DS, "height" =>"100", "width" => "100"),
	    						 		  "save" => array("path" => $this->_imgPath));
	    		
	    		$nombreImg = date('YmdHms').".jpg";
     
    			\core\util\UtilImage::processImg($_FILES['imagen'], $this->_imgAction, $nombreImg);
    
	    		$id = $this->_dao->insertId(array("imagen" => $nombreImg, "id_galeria" => $_POST['id_galeria_img']), $this->_tabla);
				
	    		$html = "<head><style type='text/css'> body{margin: 0;}</style></head>"
	    			   ."<input type='hidden' id='id' value='$id' />"
	    			   ."<input type='hidden' id='nombre_imagen' value='$nombreImg' />"
	    			   ."<img src='".$this->_imgUrl . THUMBNAIL . "/" . $nombreImg."' height='100px' width='100px' />";
	    		
	    		echo $html;
    		}
    	} catch (\Exception $e){
    		$this->showError($e->getMessage());
    	}	
    }
	
    
    public function loadThumbnailVideo(){
    	
    	$thumbnail = \core\util\Util::getImageVideo($_POST['url']);
    	
    	$html = "<head><style type='text/css'> body{margin: 0;}</style></head>"
    			."<img src='".$thumbnail."' height='100px' width='100px' />";
	    
    	echo $html;
    		
    }
    
}	  
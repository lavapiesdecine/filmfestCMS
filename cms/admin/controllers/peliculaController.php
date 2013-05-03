<?php

namespace admin\controllers;

class peliculaController extends \core\AdminController{
	
	private $_utf8 = 'N';
	
    public function __construct($data) {
    	$this->imprimir = true; 
		$this->urlWebModulo = BASE_URL."/pelicula/";
		$this->_tabla = "peliculas";
		$this->_title = _("pelicula.title");
		$this->_description = _("pelicula.description");
		parent::__construct($data);
    }
    
    public function index(){
    	
    	//datos por defecto
		$idAgradecimiento = 0;
		$idProyeccion = 0;
    	$fichaTecnica = null;
    	$sinopsis = null;
	    $classUpload = "upload-img";
	    $txtPresentacion = "";		
		$url = "";
		$img = null;
    	$fileImg = null;
    	$titulo = ""; 
    	$fileImg="";
    	$idImgPelicula = 0;
    	$idLicencia = "01";
    	$imgThumbnail = "";
    	
    	if(!empty($this->_id)){
	   		$pelicula = $this->_dao->fichaPeliculaDAO($this->_id);
			$titulo = $pelicula->titulo;
			$txtPresentacion = $pelicula->material_propio;
			$fichaTecnica = $pelicula->ficha_tecnica;
			$sinopsis =  $pelicula->sinopsis;
			$url = $pelicula->enlace;
			$idProyeccion = $pelicula->id_proyeccion;
			$idAgradecimiento = $pelicula->id_donante;
			$idLicencia = $pelicula->id_licencia;
			
			if(!empty($pelicula->cartel)){
				$classUpload = "";
				$imgThumbnail = URL_IMG . "peliculas" . "/" . $this->_anyo . "/" . MEDIUM . "/" . $pelicula->cartel;
				$img = URL_IMG . "peliculas" . "/" . $this->_anyo . "/" . $pelicula->cartel;
				$idImgPelicula = $pelicula->id_imagen;
			}
		}
	 	
    	$this->addData(array("listado" => $this->_dao->peliculasDAO($this->_anyo),
    						"proyecciones" => $this->_dao->proyeccionesPeliculaDAO($this->_anyo),
						    "agradecimientos" => $this->_dao->agradecimientosPeliculaDAO(),
    						"licencias" => $this->_dao->licenciasDAO(), "idLicencia" => $idLicencia,
					    	"idAgradecimiento" => $idAgradecimiento, "idProyeccion" => $idProyeccion,
					    	"fichaTecnica" => $fichaTecnica, "sinopsis" => $sinopsis,
						    "classUpload" => $classUpload, "txtPresentacion" => $txtPresentacion,		
							"url" => $url, "img" => $img, "imgThumbnail" => $imgThumbnail, "fileImg" => $fileImg,
    						"idImgPelicula" => $idImgPelicula, "titulo" => $titulo));
		
    	$this->loadView();
    }   
	
    public function alta(){
  		
    	$campos = array("titulo" => $_POST['titulo'],
						"ficha_tecnica" => $_POST['id_ficha'],
						"sinopsis" => $_POST['id_sinopsis'],
				  		"material_propio" => $_POST['id_propio'],
						"muestra" => $_POST['id_muestra'],
				  		"enlace" => \core\util\Util::getUrlVideo($_POST['id_enlace']),
				  		"video_descarga" => $_POST['id_descarga'],
				  		"id_licencia" => $_POST['id_licencia'],
						"utf8" => $this->_utf8);
  		try { 
  			
  			$this->_dao->startTransaction();
  			
  			$idPelicula = $this->_dao->insertUpdateId($_POST['id'], $campos, $this->_tabla);
  		
  			if(!empty($idPelicula)){
	  				$this->_dao->deleteDonantePelicula($idPelicula);
	  				$this->_dao->deleteProyeccionPelicula($idPelicula);
	  				if(!empty($_POST['id_donante'])){		
		  				$this->_dao->insert(array("id_donante" => $_POST['id_donante'], "id_pelicula" => $idPelicula), "donante_pelicula");
		  			}
	  				if(!empty($_POST['id_proyeccion'])){
	  					$this->_dao->insert(array("id_proyeccion" => $_POST['id_proyeccion'], "id_pelicula" => $idPelicula), "proyeccion_pelicula");
	  				}
	  		}
	  		
	  		$this->_dao->commit();
	  		
  		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
			$this->_dao->rollback();
		}
		echo json_encode($this->_result);
    }
    
	public function delete(){
 		$anyo = $this->_data->getRequest()->getAnyo();
    	$id = $_POST['id'];
    	$this->_imgPath = IMG_PATH . "peliculas" . DS . $this->_anyo . DS;
    	try{
	    	if(!empty($id)){
	 			$imgPeliculaDAO = $this->_dao->imgPeliculaDAO($id);
	 			if(!empty($imgPeliculaDAO->imagen)){
					\core\util\UtilFile::deleteFile($this->_imgPath . $imgPeliculaDAO->imagen);
					\core\util\UtilFile::deleteFile($this->_imgPath . THUMBNAIL . DS. $imgPeliculaDAO->imagen);
					\core\util\UtilFile::deleteFile($this->_imgPath . MEDIUM . DS . $imgPeliculaDAO->imagen);
				}	
				$this->_dao->delete($id, $this->_tabla);
			}
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		}
		echo json_encode($this->_result);
    }
    
	public function upload(){
		$idPelicula = $_POST['id_pelicula'];
		try{
			$this->_dao->startTransaction();
			
	    	if(isset($_FILES['imagen'])){
	    		
	    		$this->_imgPath = IMG_PATH . "peliculas" . DS . $this->_anyo . DS;
	    		$this->_imgUrl = URL_IMG . "peliculas" . "/" . $this->_anyo . "/" ;
	    		$this->_imgAction = array("crop" => array("path" => $this->_imgPath . THUMBNAIL . DS, "width" =>"50", "height" => "50"),
	    						 		  "md" => array("path" => $this->_imgPath . MEDIUM . DS, "height" =>"100", "width" => "150"),
	    						 		  "save" => array("path" => $this->_imgPath)
	    		);
	    		
	    		$nombreImg =  empty($_POST['id_nombre']) ? date('YmdHms') : \core\util\Util::stripAccents($_POST['id_nombre']);
	    		
	    		\core\util\UtilImage::processImg($_FILES['imagen'], $this->_imgAction, $nombreImg);
	    		 
	    		$thumbnail = $nombreImg . ".jpg";
	    			
	    		if(!empty($idPelicula)){
	    			$this->_dao->insert(array("id_pelicula" => $idPelicula, "imagen" => $thumbnail), "imagenes_pelicula");
	    		} else {
	    			$idPelicula = $this->_dao->insertId(array("muestra" => $this->_anyo, "titulo" => $_POST['id_nombre'], "ficha_tecnica" => "", "sinopsis" => ""), $this->_tabla);
	    			if($idPelicula>0){
	    				$this->_dao->insert(array("id_pelicula" => $idPelicula, "imagen" => $thumbnail), "imagenes_pelicula");
	    			}
	    		}
	    		$this->_dao->commit();
	    		$html = "<head><style type='text/css'> body{margin: 0;}</style></head>"
	    				."<input type='hidden' id='id' value='$idPelicula' />"
	    				."<input type='hidden' id='nombre_imagen' value='$thumbnail' />"
	    				."<img src='".$this->_imgUrl . $thumbnail."' height='100px' width='100px' />";
	    		
	    		echo $html;	
	    	}
    	} catch (\Exception $e) {
    		$this->_dao->rollback();
    		$this->showError($e->getMessage());
    	}	
    	
    }
        
    public function deleteImagen(){
 		$id = $_POST['id'];
 		$this->_imgPath = IMG_PATH . "peliculas" . DS . $this->_anyo . DS;
 		try{
	 		if(!empty($id)){
	 			$imgPeliculaDAO = $this->_dao->imgPeliculaDAO($id);
	 			if(!empty($imgPeliculaDAO->imagen)){
					\core\util\UtilFile::deleteFile($this->_imgPath . $imgPeliculaDAO->imagen);
					\core\util\UtilFile::deleteFile($this->_imgPath . THUMBNAIL . DS .$imgPeliculaDAO->imagen);
					\core\util\UtilFile::deleteFile($this->_imgPath . MEDIUM . DS . $imgPeliculaDAO->imagen);
				}
	 			$this->_dao->deleteImagenPelicula($id);
			}
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		}
		echo json_encode($this->_result);
	}
    
    public function imprimir(){
    	$id = $this->_id;
    	if (!empty($id)){
			$pdf = new \core\util\PDF();
			$pdf->setEdicion($this->_data->getEdicion());
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',16);
			$pelicula = $this->_dao->fichaPeliculaDAO($id);
			$pdf->fichaPelicula($pelicula);
			$pdf->Output(\core\util\Util::stripAccents($pelicula->titulo).'.pdf', 'D');
   		}
	}
    
}
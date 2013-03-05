<?php

namespace admin\controllers;

class peliculaController extends \core\AdminController{
	
	private $_utf8 = 'N';
	
    public function __construct($data) {
    	$this->imprimir = true; 
		$this->urlWebModulo = BASE_URL."/pelicula/";
		$this->_tabla = "peliculas";
		$this->_carpetaImg = "peliculas";
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
				$imgThumbnail = URL_IMG . $this->_carpetaImg . "/" . $this->_anyo . "/" . MEDIUM . "/" . $pelicula->cartel;
				$img = URL_IMG . $this->_carpetaImg . "/" . $this->_anyo . "/" . $pelicula->cartel;
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
  		"licencia" => $_POST['id_licencia'],
		"id_proyeccion" => $_POST['id_proyeccion'],
  		"id_donante" => $_POST['id_donante'],
  		"utf8" => $this->_utf8);
  		
  		echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
    	
    }
    
	public function delete(){
 		$anyo = $this->_data->getRequest()->getAnyo();
    	$id = $_POST['id'];
    	
    	if(!empty($id)){
 			$imgPeliculaDAO = $this->_dao->imgPeliculaDAO($id);
 			if(!empty($imgPeliculaDAO->imagen)){
				unlink(IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS . $imgPeliculaDAO->imagen);
				unlink(IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS . THUMBNAIL . DS .$imgPeliculaDAO->imagen);
				unlink(IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS . MEDIUM . DS . $imgPeliculaDAO->imagen);
			}	
			$ok = $this->_dao->delete($id, $this->_tabla);
		}
		echo self::feedback($ok);
    }
    
	public function upload(){
		$idPelicula = $_POST['id_pelicula'];
    	if(isset($_FILES['imagen'])){
    		try{    			
    			$path = IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS;
    			$actions = array(array("action" => "crop", "path" => $path . THUMBNAIL . DS, "height" =>"50", "width" => "50"),
    							 array("action" => "crop", "path" => $path . MEDIUM . DS, "height" =>"150", "width" => "100"),
    							 array("action" => "save", "path" => $path));
    			$nombreImg = $this->uploadImagen($actions);
    			$urlImagen = URL_IMG . $this->_carpetaImg . "/" . $this->_anyo . "/" . $nombreImg;
	    		
    			if(!empty($idPelicula)){
    				$ok = $this->_dao->insert(array("id_pelicula" => $idPelicula, "imagen" => $nombreImg), "imagenes_pelicula");
    			} else {
    				$this->_dao->startTransaction();
    				$idPelicula = $this->_dao->insertId(array("muestra" => $this->_anyo, "titulo" => $_POST['id_nombre'], "ficha_tecnica" => "", "sinopsis" => ""), $this->_tabla);
    				if($idPelicula>0){
    					if($this->_dao->insert(array("id_pelicula" => $idPelicula, "imagen" => $nombreImg), "imagenes_pelicula")){
    						$this->_dao->commit();
    					}
    				}
    			}
    			echo "<input type='hidden' id='id' value='$idPelicula' />";
    			echo "<input type='hidden' id='nombre_imagen' value='$nombreImg' />";
    			echo "<img src='$urlImagen' height='100px' width='100px' />";
    		} catch (\Exception $e) {
    			echo("<p>problemas al subir la imagen</p>");
    			\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
    		}
    	}
    }
        
    public function deleteImagen(){
 		$id = $_POST['id'];
 		if(!empty($id)){
 			$imgPeliculaDAO = $this->_dao->imgPeliculaDAO($id);
 			if(!empty($imgPeliculaDAO->imagen)){
				unlink(IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS . $imgPeliculaDAO->imagen);
				unlink(IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS . THUMBNAIL . DS .$imgPeliculaDAO->imagen);
				unlink(IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS . MEDIUM . DS . $imgPeliculaDAO->imagen);
			}
 			$this->_dao->deleteImagenPelicula($id);
 			header("Location: ".URL_ADMIN."/pelicula/$id");
 			exit;
		}
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
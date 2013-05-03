<?php

namespace admin\controllers;

class convocatoriaController extends peliculaController{
	
   public function __construct($data) {
		parent::__construct($data);
		$this->_title = _("convocatoria.title");
		$this->_description = _("convocatoria.description");
		$this->_utf8 = 'S';
   }
   
   public function index(){

   		$this->addData(array("listado" => $this->_dao->peliculasConvocatoriaDAO($this->_anyo)));

   		if(!empty($this->_id)){
			
			$classUpload = "upload-img";
			$fileImg="";
			$idImgPelicula = 0;
			$imgThumbnail = "";
			$img = null;
			
			$pelicula = $this->_dao->fichaPeliculaConvocatoriaDAO($this->_id);
			
			if(!empty($pelicula->cartel)){
				$classUpload = "";
				$imgThumbnail = URL_IMG . "peliculas" . "/" . $this->_anyo . "/" . MEDIUM . "/" . $pelicula->cartel;
				$img = URL_IMG . "peliculas" . "/" . $this->_anyo . "/" . $pelicula->cartel;
				$idImgPelicula = $pelicula->id_imagen;
			}
			
			$this->addData(array("pelicula" => $pelicula,
								 "licencias" => $this->_dao->licenciasDAO(), "classUpload" => $classUpload,
			 					 "proyecciones" => $this->_dao->proyeccionesPeliculaDAO($this->_anyo),
								 "img" => $img, "imgThumbnail" => $imgThumbnail, "fileImg" => $fileImg,
								 "idImgPelicula" => $idImgPelicula));
		}
		
		$this->loadView();
		 
    }   
	
    public function view(){
 		$id = $_POST["id"];
		$accion = $_POST["accion"];
		try{			
	 		if(!empty($id)){
				$this->_dao->view($id, "convocatoria", $accion);
			}
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		}
		echo json_encode($this->_result);
    }
    
}
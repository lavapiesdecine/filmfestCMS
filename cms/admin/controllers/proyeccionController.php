<?php

	namespace admin\controllers;
	
	class proyeccionController extends \core\AdminController{
		
	     public function __construct($data) {
	    	$this->_data = $data;
	    	$this->cambioEdicion = true;
	    	$this->imprimir = true;
	    	$this->_tabla = "proyecciones";
	    	$this->_title = _("proyeccion.title");
	    	$this->_description = _("proyeccion.description");
	        parent::__construct($data);
	    }
	    
	    public function index(){
			
			$titulo = "";
		   	$descripcion = "";
		   	$horaProyeccion = "";
		   	$idEspacio = 0;
		   	$diaProyeccion = '';
	    	
		   if(!empty($this->_id)){
		   		$proyeccionDAO = $this->_dao->select($this->_id, $this->_tabla);
		   		$titulo = $proyeccionDAO->titulo;
		   		$descripcion = $proyeccionDAO->descripcion;
		   		$idEspacio = $proyeccionDAO->id_espacio;
		   		$diaProyeccion = date('d/m/Y', strtotime($proyeccionDAO->dia));
		   		$horaProyeccion = date("H:i", strtotime($proyeccionDAO->hora));
		   }	
		 	
		   $this->addData(array("listado" => $this->_dao->proyeccionesDAO($this->_anyo),
	    						 "espacios" => $this->_dao->espaciosProyeccionDAO(),
	    						 "titulo" => $titulo,
								 "descripcion" => $descripcion,
								 "hora" => $horaProyeccion,
								 "idEspacio" => $idEspacio,
								 "diaProyeccion" => $diaProyeccion));
			
		   $this->loadView();
	    }   
	    
	 	public function alta(){
	 		
			$campos = array("id_espacio" => $_POST['id_espacio'],
							"dia" => \core\util\Util::formatDate2Mysql($_POST['id_dia']),
							"hora" => $_POST['id_hora'],
							"titulo" => $_POST['id_titulo'],
							"descripcion" => $_POST['id_descripcion'],
							"anyo" => $_POST['id_edicion']);
			
			echo $this->_dao->insertUpdate($_POST['id'], $campos, $this->_tabla);
			
	    }
	    
	
	    public function imprimir(){
	    	$id = $this->_id;
	   		if (!empty($id)){
	   			$pdf = new \core\util\PDF();
	   			$pdf->setEdicion($this->_data->getEdicion());
	   			$pdf->AddPage();
				$pdf->SetAutoPageBreak('on', 30);
				$pdf->SetFont('Arial','B',16);
				$proyeccion = $this->_dao->proyeccionPDFDAO($id);
				$peliculas = $this->_dao->fichasPeliculaProyeccionDAO($id);
				$titulo = strftime("%A %#d", strtotime($proyeccion->dia)). " ".$proyeccion->hora." ".$proyeccion->espacio;
				$pdf->proyeccion($titulo, $peliculas);
				$pdf->Output(\core\util\Util::stripAccents($titulo).'.pdf', 'D');
	   		}
		}
	 	
	    
	}
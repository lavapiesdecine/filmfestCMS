<?php 

	namespace www\controllers;
	
	class peliculaController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$pelicula = $this->_dao->fichaPeliculaDAO($this->_id);
	    	$this->_title = $pelicula->titulo;
	    	$this->_description = \core\util\Util::substring($pelicula->sinopsis, 200);
	    	$this->addData(array("pelicula" => $pelicula));
	    	if(!empty($pelicula->id_donante)){
	    		$donante = $this->_dao->agradecimientoDAO($pelicula->id_donante);
	    		$this->addData(array("donante" => $donante));
	    	}
	    	$this->loadView();       
	    }
	}
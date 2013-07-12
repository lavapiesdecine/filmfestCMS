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
	    	
	    	//imagen
	    	if(!empty($pelicula->cartel)){
	    		$imagen = URL_IMG."peliculas/". $this->_anyo."/".$pelicula->cartel;
	    	} else {
	    		$imagen = \core\util\Util::getImageVideo($pelicula->enlace, false);
	    	}
	    	$this->addData(array("imagenPelicula" => $imagen));
	    	
	    	//agradecimientos
	    	if(!empty($pelicula->id_donante)){
	    		$donante = $this->_dao->agradecimientoDAO($pelicula->id_donante);
	    		$this->addData(array("donante" => $donante));
	    	}
	    	
	    	//twitterCard
	    	$card = new \stdClass();
    		$tituloURL = \core\util\Util::stripAccents($this->_title);
    		$card->url = BASE_URL."/pelicula/$pelicula->id/$tituloURL";
	    	$card->title = $this->_title;
	    	$card->description = $this->_description;
	    	$card->image = $imagen;
	    	if (!empty($pelicula->enlace)){	    			
	    		$card->video = $pelicula->enlace;
	    		$card->type =  'player';
	    	} else {
	    		$card->type = 'summary';
	    	}
	    	$this->_twitterCard = \core\util\UtilPagina::getTwitterCard($card); 
    		
	    	$this->loadView();       
	    }
	    
	}
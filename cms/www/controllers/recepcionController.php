<?php 

	namespace www\controllers;
	
	class recepcionController extends \core\Controller{
		
	    public function __construct($data) {
	        parent::__construct($data);
	    }
	    
	    public function index(){
	    	$TOTAL_LISTADO = 20;
	    	$autoproducciones = $this->_dao->numeroAutoproduccionesDAO($this->_anyo);
	    	$numeroTotal = count($autoproducciones);
	    	$totalPaginas = ceil($numeroTotal/$TOTAL_LISTADO);
			$pag = empty($this->_id) ? 1 : $this->_id;	
		    $inicio = empty($this->_id) ? 0 : ($pag-1) * $TOTAL_LISTADO;
	   		
		    $this->addData(array("total" => $numeroTotal, "peliculas" => $this->_dao->listadoAutoproduccionesDAO($this->_anyo, $inicio, $TOTAL_LISTADO),
	   	    					 "pag" => $pag, "totalPaginas" => $totalPaginas ));
		    $this->_title = _("recepcion.title");
		    $this->_description = _("recepcion.description");
			$this->loadView();
	        
	    }
	}
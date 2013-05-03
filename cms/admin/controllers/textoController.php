<?php

	namespace admin\controllers;
	
	class textoController extends \core\AdminController{
		
	    public function __construct($data) {
	    	$this->_tabla = "textos";
			$this->_title = _("texto.title");
			$this->_description = _("texto.description");
	        parent::__construct($data);
	    }
	    
	    public function index(){
			$texto = "";
			$titulo = "";
			$lang = 0;
			$idGaleria = 0;
			
			if(!empty($this->_id)){
		   		$textoDAO = $this->_dao->textoDAO($this->_id);
		   		$texto = $textoDAO->texto;
		   		$titulo = $textoDAO->titulo;
		   		$lang = $textoDAO->lang;
		   		$idGaleria = $textoDAO->id_galeria;
		   	}	
		   	
	    	$this->addData(array("listado" => $this->_dao->textosDAO($this->_anyo, false),
	    						 "texto" => $texto, "titulo" => $titulo, "lang"=> $lang, 
	    						 "langs" => $this->_dao->langsEdicionDAO($this->_anyo),
	    						 "idGaleria" => $idGaleria,
	    						 "galerias" => $this->_dao->galeriasDAO()));
	    	$this->loadView();
	    }
	    
	    
		public function alta(){
	 		
			$campos = array("titulo" => $_POST['id_titulo'], "texto" => $_POST['texto'],
							"muestra" => $_POST['id_muestra'], "lang" => $_POST['lang']);
			
			try{
				$this->_dao->startTransaction();
				$idTexto = $this->_dao->insertUpdateId($_POST['id'], $campos, $this->_tabla);
				if(!empty($_POST['id_galeria'])){
					$this->_dao->deleteGaleriaTexto($idTexto);
					$this->_dao->insert(array("id_texto" => $idTexto, "id_galeria" => $_POST['id_galeria']), "galeria_texto");
				}
				$this->_dao->commit();
			
			} catch (\Exception $e) {
				$this->_result = array("ok" => false, "msg" => $e->getMessage());
				$this->_dao->rollback();
			}
			echo json_encode($this->_result);
			
		}

	}
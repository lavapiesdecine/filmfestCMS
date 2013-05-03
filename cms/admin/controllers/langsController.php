<?php
namespace admin\controllers;
class langsController extends \core\AdminController{
	
	public function __construct($data) {
		$this->_tabla = "langs";
		$this->_title = _("langs.title");
		$this->_description = _("langs.description");
		parent::__construct($data);
    }
    
    public function index(){

    	$nombre = "";
	    $codigo = "";
	    $codificacion = "";
	    
	    if(!empty($this->_id)){
		    $language = $this->_dao->select($this->_id, $this->_tabla);
		    $nombre = $language->nombre;
			$codigo = $language->codigo;
			$codificacion = $language->codificacion;
	    }
	 	
    	$this->addData(array("listado" => $this->_dao->langsDAO(),
    						 "nombre" => $nombre, "codigo" => $codigo,
							 "codificacion" => $codificacion));
		$this->loadView();
    }
    
	public function alta(){
 		
 		$campos = array("nombre" => $_POST['id_nombre'],
						"codigo" => $_POST['id_codigo'],
						"lang" => substr($_POST['id_codigo'],0,2),
 						"codificacion" => $_POST['id_codificacion']);
		
		echo $this->alta($_POST['id'], $campos);

    }
    
    
}
<?php

namespace admin\controllers;

class galeriaController extends \core\AdminController{
	
    public function __construct($data) {
    	$this->_tabla = "galerias";	
    	$this->_title = _("galeria.title");
    	$this->_description = _("galeria.description");
        parent::__construct($data);
    }
    
    public function index(){
		$nombre = "";
		$descripcion = "";
		
		if(!empty($this->_id)){
	   		$galeriaDAO = $this->_dao->select($this->_id, $this->_tabla);
			$nombre = $galeriaDAO->titulo;
			$descripcion = $galeriaDAO->descripcion;
	    }	
	 	
    	$this->addData(array("listado" => $this->_dao->galeriasDAO(),
    							"nombre" => $nombre, 
    							"descripcion" => $descripcion));
    	$this->loadView();
    }
    
	public function alta(){
    	$id = $_POST['id'];
    	try {
			if(empty($id)){
				$folder = \core\util\Util::stripAccents($_POST['id_nombre']);
				$datos = array("titulo"=>$_POST['id_nombre'], "descripcion"=>$_POST['id_descripcion'], "galeria"=>$folder);
				$this->_dao->insert($datos, $this->_tabla);
				\core\util\UtilFile::mkdir(GALERIAS_PATH . $folder . DS . THUMBNAIL);
			}
			else{
				$this->_dao->update($id, array("titulo"=>$_POST['id_nombre'], "descripcion"=>$_POST['id_descripcion']), $this->_tabla);
			}
		} catch (\Exception $e){
	    	$this->_result = array("ok" => false, "msg" => $e->getMessage());
	    }
	    echo json_encode($this->_result);
    }
    
	public function delete(){
 		$id = $_POST['id'];
    	try {
			$galeriaDAO = $this->_dao->select($id, $this->_tabla);
			if(isset($galeriaDAO->galeria)){
				\core\util\UtilFile::recursirveRmdir(GALERIAS_PATH.$galeriaDAO->galeria);
				$this->_dao->delete($id, $this->_tabla);
			}
		} catch (\Exception $e){
	    	$this->_result = array("ok" => false, "msg" => $e->getMessage());
	    }
	    echo json_encode($this->_result);
    }

}
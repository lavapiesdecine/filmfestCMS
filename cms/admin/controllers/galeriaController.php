<?php

namespace admin\controllers;

class galeriaController extends \core\AdminController{
	
    public function __construct($data) {
    	$this->_tabla = "galerias";	
    	$this->cambioEdicion = true;
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
    	$ok = false;
		if(empty($id)){
			$folder = \core\util\Util::stripAccents($_POST['id_nombre']);
			$datos = array("titulo"=>$_POST['id_nombre'], "descripcion"=>$_POST['id_descripcion'], "galeria"=>$folder);
			if($this->_dao->insert($datos, $this->_tabla)){
				$folderThumbnail = GALERIAS_PATH . $folder . DS . THUMBNAIL;
				if(!mkdir($folderThumbnail, 0777, true)){
					\core\util\Error::add("error en ".__FUNCTION__. " generando $folderThumbnail");
				}
			}
		}
		else{
			$ok = $this->_dao->update($id, array("titulo"=>$_POST['id_nombre'], "descripcion"=>$_POST['id_descripcion']), $this->_tabla);
		}
	    echo self::feedback($ok);
    }
    
	public function delete(){
 		
    	$id = $_POST['id'];
    	$ok = false;
		$galeriaDAO = $this->_dao->select($id, $this->_tabla);
		if(isset($galeriaDAO->galeria)){
			\core\util\Util::recursirveRmdir(GALERIAS_PATH.$galeriaDAO->galeria);
			$ok = $this->_dao->delete($id, $this->_tabla);
		}
		echo self::feedback($ok);
    }

}
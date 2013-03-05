<?php 

	namespace core;
	
	abstract class AdminController extends Controller{
		
		protected $urlWebModulo;
		protected $imprimir;
		protected $_tabla;
		protected $_carpetaImg;
		protected $_pathLogo;
		protected $_urlLogo;
		protected $_menu = true;
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    	$this->_baseUrl = URL_ADMIN . '/' . ( $this->_anyo == DEFAULT_ANYO ? '' : $this->_anyo . '/');
	    	$this->_base = BASE_URL_ADMIN;
	    }
	    
		public function view(){
	 		$id = $_POST["id"];
			$accion = $_POST["accion"];
			$ok = false;
	 		if(!empty($id)){
				$ok = $this->_dao->view($id, $this->_tabla, $accion);
			}
			echo $ok;
	    }
	    
	    public function delete(){
	    	$id = $_POST['id'];
	    	$ok = true;
	    	if(!empty($id)){
		    	$query = $this->_dao->select($id, $this->_tabla);
		    	if(!empty($query->logo)){
		    		if (unlink($this->_pathLogo . $query->logo)){
		    			$ok = $this->_dao->delete($id, $this->_tabla);
		    		}
		    	} else {
		    		$ok = $this->_dao->delete($id, $this->_tabla);
		    	}
	    	}
	    	echo $ok;
	    }
	    
	    public function deleteImagen(){
	    	$id = $_POST['id'];
	    	if(!empty($id)){
	    		$query = $this->_dao->select($id, $this->_tabla);
	    		if(!empty($query->logo)){
	    			if (unlink($this->_pathLogo . $query->logo)){
	    				if($this->_dao->update($id, array("logo" => ""), $this->_tabla)){
	    					header("Location: ".URL_ADMIN."/".$this->_data->getRequest()->getControlador()."/$id");
	    					exit;
	    				}
	    			}
	    		}
	    	}
	    }
	    
	    /*
	    public function feedback($ok){
	    	return $ok ? _("feedback.bbdd.ok") : _("feedback.bbdd.ko"); 
	    }*/
	}
<?php 

	namespace core;
	
	class AdminController extends Controller{
		
		protected $urlWebModulo;
		protected $imprimir;
		protected $_tabla;
		
		protected $_menu = true;
		protected $_result = array("ok" => true);
		protected $_imgAction;
		protected $_imgUrl;
		protected $_imgPath;
		
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    	$this->_baseUrl = URL_ADMIN . '/' . ( $this->_anyo == DEFAULT_ANYO ? '' : $this->_anyo . '/');
	    	$this->_base = BASE_URL_ADMIN;
	    }
	     
	    /**
	     * show thumnail
	     */
	    public function showImg($thumbnail){
	    	$html = "<head><style type='text/css'> body{margin: 0;}</style></head>"
	    			."<input type='hidden' id='nombre_imagen' name='nombre_imagen' value='".$thumbnail."' />"
	    			."<img src='".$this->_imgUrl .  $thumbnail."' height='100px' width='100px' />";
	    	echo $html;
	    }
	     
	    /**
	     * show error
	     */
	    public function showError($msg){
	    	$error = error_get_last();
	    	if(!empty($error)){
	    		$msg .= $error["message"] . "<br>" . "line " . $error["line"];
	    	}
	    	$html = "<head><style type='text/css'> body{margin: 0;font-size: 8px;}</style></head>";
	    	echo $html . $msg;
	    }
	    
	    
	    public function insert($id, $campos){
	    	try{
	    		$this->_dao->insertUpdate($id, $campos, $this->_tabla);
	    	} catch (\Exception $e){
	    		$this->_result = array("ok" => false, "msg" => $e->getMessage());
	    	}
	    	return json_encode($this->_result);
	    }
	    
		public function view(){
	 		$id = $_POST["id"];
			$accion = $_POST["accion"];
			
			try{
		 		if(!empty($id)){
					$this->_dao->view($id, $this->_tabla, $accion);
				}
			} catch (\Exception $e){
				$this->_result = array("ok" => false, "msg" => $e->getMessage());
			}
			echo json_encode($this->_result);
	    }
	    
	    public function delete(){
	    	$id = $_POST['id'];
	    	try{
		    	if(!empty($id)){
			    	$query = $this->_dao->select($id, $this->_tabla);
			    	if(!empty($query->logo)){
			    		\core\util\UtilFile::deleteFile($this->_imgPath . $query->logo);
			    		$this->_dao->delete($id, $this->_tabla);
			    	} else {
			    		$this->_dao->delete($id, $this->_tabla);
			    	}
		    	}
	    	} catch (\Exception $e){
				$this->_result = array("ok" => false, "msg" => $e->getMessage());
			}
			echo json_encode($this->_result);
	    }
	    
	    public function deleteImagen(){
	    	$id = $_POST['id'];
	    	try{
		    	if(!empty($id)){
		    		$query = $this->_dao->select($id, $this->_tabla);
		    		if(!empty($query->logo)){
		    			\core\util\UtilFile::deleteFile($this->_imgPath . $query->logo);
		    			$this->_dao->update($id, array("logo" => ""), $this->_tabla);
	    			}
		    	}
	    	} catch (\Exception $e){
	    		$this->_result = array("ok" => false, "msg" => $e->getMessage());
	    	}
	    	echo json_encode($this->_result);
	    }
	    
	    /**
	     * upload and process thumnail
	     * $id module
	     * $name img
	     */
	    public function uploadImg($id, $name){
	    	try{
	    		if(isset($_FILES['imagen'])){
	    				
	    			$nombreImg =  empty($name) ? date('YmdHms') : \core\util\Util::stripAccents($name);
	    				
	    			\core\util\UtilImage::processImg($_FILES['imagen'], $this->_imgAction, $nombreImg);
	    				
	    			$thumbnail = $nombreImg . ".jpg";
	    				
	    			if(!empty($id)){
	    				$this->_dao->update($id, array("logo" => $thumbnail), $this->_tabla);
	    			}
	    				
	    			$this->showImg($thumbnail);
	    		}
	    	} catch (\Exception $e){
	    		$this->showError($e->getMessage());
	    	}
	    }
	   
	}
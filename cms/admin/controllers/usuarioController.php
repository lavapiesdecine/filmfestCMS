<?php

namespace admin\controllers;

class usuarioController extends \core\AdminController{
	
     public function __construct($data) {
     	$this->_tabla = "usuarios";
     	
     	$this->_title = _("usuario.title");
     	$this->_description = _("usuario.description");
     	
     	$this->_imgPath = ADMIN_PATH . 'vista' . DS . 'skins' . DS . "commons" . DS .'img' . DS . "usuarios" . DS;
     	$this->_imgUrl = BASE_URL_ADMIN . "/vista/skins/commons/img/usuarios/";
     	$this->_imgAction = array(array("action" => "resize", 
     								    "path" => $this->_imgPath, 
     								    "height" =>"40", "width" => 40));
     	
    	parent::__construct($data);
    }
    
    public function index(){
    	
		$nivelAcceso = 0;
		$classPassword = "";
		$usuario = "";
		$email = "";
		$nombre ="";
		$classUpload = "upload-img";
		$img = null;
		$fileImg = "";
		$perfiles = array();
		
		if(!empty($this->_id)){
	   		$classPassword = "oculto";
	   		$usuarioDAO = $this->_dao->select($this->_id, $this->_tabla);
	   		$nombre = $usuarioDAO->usuario;
	   		$email = $usuarioDAO->email;
	   		$perfiles = $this->_dao->nivelesNavegacionUsuarioDAO($usuarioDAO->id);
	   		if(!empty($usuarioDAO->logo)){
	   			$classUpload = "";
	   			$fileImg = $usuarioDAO->logo;
	   			$img = $this->_imgUrl . $fileImg;
	   		}
	 	}		
	 	
	 	$perfilesSeleccionados = "";
	 	foreach ($perfiles as $index =>$perfil){
	 		$perfilesSeleccionados .= $perfil->id.($index<(count($perfiles)-1) ? ",":"");
	 	}
	 	
	 	$this->addData(array("listado" => $this->_dao->usuariosDAO(),
    						 "perfiles" => $perfiles,
	 						 "perfilesPosibles" => $this->_dao->nivelesNavegacionPosiblesDAO($perfilesSeleccionados),
	 						 "nivelAcceso" => $nivelAcceso, "classPassword" => $classPassword,
	 						 "img" => $img, "fileImg" => $fileImg, "classUpload" => $classUpload,
							 "nombre" => $nombre, "email" => $email));
		
		$this->loadView();

    }   
    
    public function alta(){
    	$id = $_POST['id'];
    	$campos = array("usuario" => $_POST['nombre'], "email" => $_POST['id_email']);
    			
    	try{
    		/* logo por defecto */
    		if (!empty($_POST['file_imagen'])){
    			$logo = $_POST['file_imagen'];
    		} else {
    			$logo = \core\util\Util::stripAccents($_POST['nombre']) . ".jpg";
    			\core\util\UtilFile::copyFile($this->_imgPath . "default.jpg", 
    										  $this->_imgPath . $logo);
    		}
    		
    		$campos = array_merge($campos, array("logo" => $logo));
    		
			$this->_dao->startTransaction();
	 		if(!empty($id)){
				$this->_dao->update($id, $campos, $this->_tabla);			
			}
			else{
				$campos = array_merge($campos, array("pass" => md5($_POST['id_password'])));
				$id = $this->_dao->insertId($campos, $this->_tabla);
				self::sendMail($campos);
			}
			
			if (!empty($_POST['perfilesSelected'])){
				$perfiles = explode(",", $_POST['perfilesSelected']);
				if(!empty($id)){
					$this->_dao->deletePerfilesUsuario($id);
					foreach ($perfiles as $perfil) {
						$this->_dao->insert(array("id_usuario" => $id, "id_perfil" => $perfil), "usuario_perfil");
					}
				}
			}
			$this->_dao->commit();
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
			$this->_dao->rollback();
		}
		echo json_encode($this->_result);
	}
	
	private function sendMail(Array $usuario){
		$mail = new \core\util\Mailer();
		$mail->setAsunto("Alta en ".BASE_URL);
		$cuerpo = "<html><body>Hola ".$usuario["usuario"].",<br> te has inscrito correctamente.</body></html>";
		$mail->setMensaje($cuerpo.$mail->setFirma($this->_data->getEdicion()->nombre));
		$mail->sendMail();
	}
	
	
	public function upload(){
		$this->uploadImg($_POST['id_usuario'], $_POST['id_nombre']);
	}
	
}
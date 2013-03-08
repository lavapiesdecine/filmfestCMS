<?php

namespace admin\controllers;

class usuarioController extends \core\AdminController{
	
     public function __construct($data) {
     	$this->_tabla = "usuarios";
     	$this->_carpetaImg = "usuarios";
     	$this->_pathLogo = ADMIN_PATH . 'vista' . DS . 'skins' . DS . "commons" . DS .'img' . DS . $this->_carpetaImg . DS;
     	$this->_urlLogo = BASE_URL_ADMIN . '/vista/skins/commons/img/'. $this->_carpetaImg . "/";
     	$this->_title = _("usuario.title");
     	$this->_description = _("usuario.description");
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
	   			$img = $this->_urlLogo . $fileImg;
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
    			
    	if (!empty($_POST['file_imagen'])){
    		$campos = array_merge($campos, array("logo" => $_POST['file_imagen']));
    	}
			
		
 		if(!empty($id)){
			$ok = $this->_dao->update($id, $campos, $this->_tabla);			
		}
		else{
			$campos = array_merge($campos, array("pass" => md5($_POST['id_password'])));
			$id = $this->_dao->insertId($campos, $this->_tabla);
			self::sendMail($campos);
		}
		
		if (!empty($_POST['perfilesSelected'])){
			$perfiles = explode(",", $_POST['perfilesSelected']);
			if(!empty($id)){
				$ok = $this->_dao->deletePerfilesUsuario($id);
				foreach ($perfiles as $perfil) {
					$ok = $this->_dao->insert(array("id_usuario" => $id, "id_perfil" => $perfil), "usuario_perfil");
				}
			}
		}
		
		echo $ok;
	}
	
	private function sendMail(Array $usuario){
		$mail = new \core\util\Mailer();
		$mail->setAsunto("Alta en ".BASE_URL);
		$cuerpo = "<html><body>Hola ".$usuario["usuario"].",<br> te has inscrito correctamente.</body></html>";
		$mail->setMensaje($cuerpo.$mail->setFirma($this->_data->getEdicion()->nombre));
		$mail->sendMail();
	}
	
	public function upload(){
		if(isset($_FILES['imagen'])){
			try{
				$actions = array(array("action" => "resize", "path" => $this->_pathLogo, "height" =>"40"));
				$nombreImg = $this->uploadImagen($actions);
				$urlImagen = $this->_urlLogo . $nombreImg;
				if(!empty($_POST['id_usuario'])){
					$ok = $this->_dao->update($_POST['id_usuario'], array("logo" => $nombreImg), $this->_tabla);
				}
				echo "<input type='hidden' id='nombre_imagen' value='$nombreImg' />";
				echo "<img src='$urlImagen' height='100px' width='100px' />";
			} catch (\Exception $e) {
				echo("<p>problemas al subir la imagen</p>");
				\core\util\Error::add(" error en ".__FUNCTION__. " : ". $e->getMessage());
			}
		}
	}
	
}
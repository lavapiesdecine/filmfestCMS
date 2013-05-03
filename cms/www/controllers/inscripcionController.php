<?php 
	namespace www\controllers;

	class inscripcionController extends \core\Controller{
		
		private $_carpetaImg = "peliculas";
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->_title = _("inscripcion.title");
	    	$this->_description = _("inscripcion.description");
	    	$this->loadView();
	    }
		   
	    public function contacto(){
	    	$idPelicula = isset($_SESSION["id_pelicula"]) ? $_SESSION["id_pelicula"] : null;
	    	try{
		    	$this->_dao->startTransaction();
				if(empty($idPelicula)){
					if (isset($_POST['id_nombre'])){
						$idPelicula = $this->_dao->insertId(array("titulo"=>"","ficha_tecnica"=>"","sinopsis"=>"", "muestra" => $this->_anyo, "alta"=>"N"), "peliculas");
						\core\util\Log::add($idPelicula, false);
						\core\util\Log::add($_POST['id_nombre'], false);
						$_SESSION["id_pelicula"] = $idPelicula;
						$this->_dao->insert(array("id"=>$idPelicula,"id_pelicula"=>$idPelicula,"autor"=>"", "duracion"=>"", "alta"=>"N"), "convocatoria");
						$this->_dao->insert(array("id_pelicula"=>$idPelicula,"autor"=>$_POST['id_nombre'], "email" => $_POST['id_email'], "telefono"=>$_POST['id_telefono'], "tipo"=>$_POST['id_tipocontacto']), "autores");
					}
				} else {
					if (isset($_POST['id_nombre'])){
						$this->_dao->update($idPelicula, array("autor"=>$_POST['id_nombre'],"email"=>$_POST['id_email'],"telefono"=>$_POST['id_telefono'], "tipo"=>$_POST['id_tipocontacto']), "autores");		
					}
					else{
						unset($_SESSION["id_pelicula"]);
					}
				}	
				$this->_dao->commit();
				echo "<p><strong>"._("inscripcion.datos.contacto")."</strong></p>"._("inscripcion.nombre")." ".$_POST['id_nombre']."<br>"._("inscripcion.email").": ".$_POST['id_email']."<br>"._("inscripcion.telefono").": ".$_POST['id_telefono']."<br>";
				echo "<p><a href='#'>&laquo; "._("inscripcion.modificar")."</a></p>";
			} catch (\Exception $e){
	    		$this->_dao->rollback();
	    		echo "<div class='msg'><strong>"._("inscripcion.error")."</strong></div>";
	    		\core\util\Error::add("error en el formulario de inscripcion ".mysql_error()." $idPelicula");
	    	}
	    	
	    }
	    
	    public function pelicula(){
	    	$idPelicula = $_SESSION["id_pelicula"];
	    	try{
	    		if(!empty($idPelicula)){
	    			$duracion = (!empty($_POST["id_minutos"]) ? $_POST["id_minutos"]."' " : "") . (!empty($_POST["id_segundos"]) ? $_POST["id_segundos"]."''" : "");  
		    		$this->_dao->startTransaction();
					$web = $_POST["id_web"];
					$ficha = "<p><strong>".$_POST['id_autor']."</strong><br>"
					."$duracion / " . $_POST["id_anyo"]
					.($_POST["id_genero"]!="" ? " / ".$_POST["id_genero"]:"")
					.($_POST["id_pais"]!="" ? " / ".$_POST["id_pais"]:"")
					.($web!='' ? "<br><a href=\"$web\">$web</a></p>" : "");
					$tecnica ="<p>".nl2br($_POST['id_tecnica'])."</p>";
					
					$data = array("titulo"=>$_POST['id_titulo'],"ficha_tecnica"=>$ficha.$tecnica,"sinopsis"=>$_POST['id_sinopsis']);
					$this->_dao->update($idPelicula, $data, "peliculas");
					$data = array("autor"=>$_POST['id_autor'],"duracion"=>$duracion,"anyo"=>$_POST['id_anyo'], "genero"=>$_POST['id_genero'],"pais"=>$_POST['id_pais'],"web"=>$_POST['id_web']);
					$this->_dao->update($idPelicula, $data, "convocatoria");					
					$this->_dao->commit();
					echo "<p><strong>"._("inscripcion.datos.obra")."</strong></p>".$ficha."<br><p><a href='#' >&laquo; "._("inscripcion.modificar")."</p></a>";
					
				}
			} catch (\Exception $e){
				$this->_dao->rollback();
				echo utf8_encode("<div class='msg'><strong>"._("inscripcion.error")."</strong></div>");
				\core\util\Error::add("error en el formulario de inscripcion");
			}
	    }
	    
	    public function adicional(){
	    	$data = array("coste"=>$_POST['id_coste'],"recursos"=>$_POST['id_recursos'],"comentarios"=>$_POST['id_comentarios']);
	    	try{
	    		$this->_dao->update($_SESSION["id_pelicula"], $data, "convocatoria");
	    		echo "<p><strong>"._("inscripcion.datos.adicional")."</strong></p>";
	    		echo "<p><a href='#'>&laquo; "._("inscripcion.modificar")."</a></p>";
    		} catch (\Exception $e){
    			$this->_dao->rollback();
    			echo utf8_encode("<div class='msg'><strong>"._("inscripcion.error")."</strong></div>");
    			\core\util\Error::add("error en el formulario de inscripcion");
    		}
	    }
	    
	    public function licencia(){
		    try{	
	    		$this->_dao->update($_SESSION["id_pelicula"], array("id_licencia"=>$_POST['id_licencia']), "peliculas");
		    	echo "<p><strong>"._("incripcion.licencia")."</strong></p><img src='".URL_LOGO."licencias/".$_POST['id_licencia'].".png' />";
		    	echo "<p><a href='#'>&laquo; "._("inscripcion.modificar")."</a></p>";
		    } catch (\Exception $e){
    			$this->_dao->rollback();
    			echo utf8_encode("<div class='msg'><strong>"._("inscripcion.error")."</strong></div>");
    			\core\util\Error::add("error en el formulario de inscripcion");
    		}
	    }
	   
	    public function upload(){ 
	     	if(isset($_FILES['imagen'])){
		    	$idPelicula = $_SESSION["id_pelicula"];
		    	$anyo = $this->_anyo;
		    	try{
		    		$path = IMG_PATH . $this->_carpetaImg . DS . $this->_anyo . DS;
		    		$actions = array(array("action" => "crop", "path" => $path . THUMBNAIL .DS, "height" =>"50", "width" => "50"),
		    						 array("action" => "crop", "path" => $path . MEDIUM . DS, "height" =>"100", "width" => "150"),
		    						 array("action" => "save", "path" => $path));
		    		$nombreImagen = $this->uploadImagen($actions);
		    		$urlImagen = URL_IMG . $this->_carpetaImg . "/" . $anyo . "/" . $nombreImagen;
		    		
		    		if(!empty($idPelicula)){
		    			$this->_dao->insert(array("id_pelicula" => $idPelicula, "imagen" => $nombreImagen), "imagenes_pelicula");
					} 
					echo "<img src='$urlImagen' height='100px' width='100px'/>";
					
		    	} catch (\Exception $e) {
		    		echo(_("inscripcion.error.img"));
		    		\core\util\Error::add("error en ".__FUNCTION__. " : ". $e->getMessage());
		    	}
		    }
	    }
	     
	    public function multimedia(){
	    	$idPelicula = $_SESSION["id_pelicula"];
	    	$video = \core\util\Util::getUrlVideo($_POST["id_video"]);
	    	try{ 
				$this->_dao->update($idPelicula, array("enlace"=>$video,"video_descarga"=>$_POST["id_videodescarga"]), "peliculas");
				$msgFeedback = "<h3>"._("inscripcion.confirm")."</h3><p>"._("inscripcion.confirm.1")."</p>";
				self::sendEMailOK($idPelicula);
			} catch (\Exception $e) {
				$msgFeedback = utf8_encode("<div class='msg'><strong>"._("inscripcion.error")."</strong></div>");
				\core\util\Error::add("error en el formulario de inscripcion");
			}
			echo $msgFeedback;
			unset($_SESSION['id_pelicula']);
	    }
	    
		private function sendEmailOK($idPelicula){	
			$datos = $this->_dao->datosContactoDAO($idPelicula);
			$edicion = $this->_data->getEdicion()->nombre;
			$autor = $datos->autor;
			$titulo = $datos->titulo;
			$email = $datos->email;
			$asunto = "Inscripción en la $edicion";
			
			$cuerpo = "<html><body><p>Hola $autor,</p>";
			$cuerpo .= "<p><strong>$titulo</strong> se ha inscrito correctamente para la <strong>$edicion</strong>. </p>";
			$cuerpo .= "<p>Puedes ver la ficha en este link <strong>".BASE_URL."pelicula/$idPelicula</strong></p>";
			$cuerpo .= "<p>Gracias por participar!!</p></body></html>";
			
			$mail = new \core\util\Mailer();
			$mail->setAsunto($asunto);
			$mail->setMensaje($cuerpo.$mail->setFirma($edicion));
			$mail->sendMail();
		}
	    
	 	 
	}
<?php
namespace core\util;

class Mailer {
	 
	private $_destinatario;
	private $_firma;
	private $_headers;
	private $_asunto;
	private $_mensaje;
	
	public function __construct(){
	
	}
	
	public function getDestinatario(){
		return $this->_destinatario;
	}
	public function setDestinatario($destinatario){
		$this->_destinatario = $destinatario;
	}
	
	public function getAsunto(){
		return $this->_asunto;
	}
	public function setAsunto($asunto){
		$this->_asunto = $asunto;
	}
	
	public function getMensaje(){
		return $this->_mensaje;
	}
	public function setMensaje($mensaje){
		$this->_mensaje = $mensaje;
	}
	
	public function setHeaders(){
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: ".$_SERVER['SERVER_NAME']." <".EMAIL_WEB.">\r\n";
		$headers .= "Bcc: ".EMAIL_WEB."\r\n";
		$this->_headers = $headers;
	}
	public function setFirma($edicion){
		$firma = "<div style='font: small Arial, Helvetica, sans-serif;margin-top:30px;'><table>";
		$firma .= "<tr><td><img src='".URL_IMG."logo_lavapies_firma.JPG'></td>";
		$firma .= "<td>Lavapiés de Cine. $edicion<br>";
		$firma .= EMAIL_WEB. "<br>";
		$firma .= $_SERVER['SERVER_NAME']."<br>";
		$firma .= "</td></tr></table></div>";
		$this->_firma = $firma;
	}
	
	public function sendMail(){
		self::setHeaders();
		try{
			if(SEND_MAIL){
				mail($this->_destinatario, $this->_asunto, $this->_mensaje, $this->_headers);
			}
		} catch (\Exception $e) {
        	\core\util\Error::add("<strong>error en ".__FUNCTION__. "</strong><br>". $e->getMessage());
        }
	}
	
	
}
<?php 

	namespace core\util;
	
	class Error {
	
		static function add($error){
		 	$error = date("Y-m-d H:i:s (T)"). " | $error <br>";
		 	if(MAIL_ERROR){
		 		$mail = new Mailer();
		 		$mail->setAsunto("error ");
		 		$mail->setDestinatario(ADMIN_EMAIL);
		 		$mail->setMensaje($error);
		 		$mail->sendMail();
		 	} else{
		 		Log::add($error, true);
		 	}
		}
	}
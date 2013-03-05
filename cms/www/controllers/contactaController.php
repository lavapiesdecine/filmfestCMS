<?php

	namespace www\controllers;

	class contactaController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->_title = _("contacta.title");
	    	$this->_description = _("contacta.description");
	    	$this->loadView();
	    }
	    
	    public function send(){
	   		$asunto = $_POST["asunto"];
			$email = $_POST["email"];
			$comentario = "<pre>".$_POST["comentario"]."</pre>";		 
			
			$cuerpo = "<html><body>$email ha escrito: <br><pre> $comentario </pre></body></html>";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: www.lavapiesdecine.net <muestra@lavapiesdecine.net>\r\n";
			
			mail(EMAIL_WEB, "[lavapiesdecine.net] $asunto", $cuerpo, $headers);
	  
	        echo "<div id='correcto'>"._("contacta.confirm")." </div> ";        
			
	    }
	    
	}
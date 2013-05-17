<?php 

	function __autoload($class) {
		
		$file = "";
		$class = explode("\\", $class);
		
		for($i=0; $i<count($class); $i++){
			$file .= $class[$i] . (count($class)==($i+1) ? "" : DIRECTORY_SEPARATOR);
		}
		
		require(CMS_PATH."$file.php");
	
	}

	function error_handler($errno, $errmsg, $file, $line, $ctxt){ 
	   if ( error_reporting() == 0 ){ 
	   		return;
	   } 	  
	   $msg = sprintf('Error %d: %s (%s:%d)', $errno, $errmsg, $file, $line );
	   
	   if ($errno == 8) {
	   		\core\util\Error::add("error: $msg");
	   }
	}
	
	function exception_handler(Exception $e) {
		include("./cms/error.phtml");
	}
<?php 

	function __autoload($class) {
		
		if(file_exists("controllers" . DIRECTORY_SEPARATOR . "$class.php")) {
			require("controllers" . DIRECTORY_SEPARATOR . "$class.php");
		} else {
			require("core" . DIRECTORY_SEPARATOR . "$class.php");
		}
	}
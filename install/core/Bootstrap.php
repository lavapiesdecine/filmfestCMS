<?php
	
	class Bootstrap {
		
		public static function run(){	
			
			$url = explode("/", $_SERVER['REQUEST_URI']);
			$numURL = count(explode("/",str_replace('http://', '', URL_INSTALL)));
			
			$controller = isset($url[$numURL]) && !empty($url[$numURL]) ? $url[$numURL] : "intro";
			
			if(self::check($controller)){
				
				$controller .= "Controller";
				
				$method = (isset($url[$numURL+1]) ? $url[$numURL+1] : "index");
				
				$controller = new $controller($controller);
				
				call_user_func(array($controller, $method));
			
			} else {
				$errorMsg = "Error 404";
				include("error.php");
				exit;
			}
			
		}
		
		private static function check($controller){
			$steps = unserialize(STEPS);
			$check =  false;
			foreach ($steps as $step) {
				if($controller == $step[0]){
					return true;
				}
			}
			return $check;
		}
		
	}
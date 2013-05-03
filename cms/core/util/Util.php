<?php

	namespace core\util;	

	class Util {    
	
		public static function substring($string, $length){	  
		    $stringDisplay = substr(strip_tags(html_entity_decode($string)), 0, $length);
		    if (strlen(strip_tags($string)) > $length){
		        $stringDisplay .= ' ...';
		    }
		    return $stringDisplay;
		}
		
		public static function stripAccents($string){
	
			// Tranformamos todo a minusculas
			$string = strtolower($string);
			
			// se cambia espacio por guiones
			$find = array(' ', '&', '\r\n', '\n', '+');
			$string = str_replace ($find, '-', $string);
			
			// Eliminamos y Reemplazamos demas caracteres especiales
			$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
			$repl = array('', '-', '');
	
			return preg_replace ($find, $repl, $string);
		}
		
		public static function recortaFicha($ficha){
	    	$pos = strpos($ficha, "</p>");
	    	if ($pos>200){
	    		$ficha = self::substring($ficha, 100);	  
	    	} else {
		    	$ficha = strip_tags(substr($ficha, 0, $pos));
	    	}
	    	return $ficha;
		}
		
		public static function getUrlVideo($video){
			
			$regex = "/[http|https]:\/\/(:?www.)?(\w*)/";
			preg_match($regex, $video, $match);
			$stream = $match[2];
			switch ($stream){
				case "youtube":
					$regex = "/(youtube\.com|youtu\.be)\/(v\/|u\/|embed\/|watch\?v=)?([^#\&\?]*).*/i";
					preg_match($regex, $video, $match);
					$video = "http://www.youtube.com/v/".$match[3];
					break;
				case "youtu":
					$regex = "/(youtube\.com|youtu\.be)\/(v\/|u\/|embed\/|watch\?v=)?([^#\&\?]*).*/i";
					preg_match($regex, $video, $match);
					$video = "http://www.youtube.com/v/".$match[3];
					break;
				case "vimeo":
					$regex = "/[http|https]:\/\/(?:www.)?(\w*).com\/(\d*)/";
					preg_match($regex, $video, $match);
					$video = "http://vimeo.com/".$match[2];
					break;
					
				case "blip":
					$regex = "/(blip\.tv)\/play\/([a-zA-Z0-9\?\=\-]+)/i";
					preg_match($regex, $video, $match);
					$video = "http://blip.tv/play/".$match[2];
					break;
				break;
				
				case "dailymotion":
					$regex = "/dailymotion.com\/video\/(.*)\/?(.*)/";
					preg_match($regex, $video, $match);
					$video = "http://www.dailymotion.com/video/".$match[1];
					break;
						
			}
			return $video;
		}
		
	
		public static function getColorTemplate($anyo){
			$color = null;
			if ($anyo=='2010'){
				$colores =  array ('b', 'y', 'm', 'c');
				$color = $colores[array_rand($colores)];
			}
			return $color;
		}
		

		
		
		public static function formatDate2Mysql($date){
			$format = explode("/", $date);
			return $format[2]."-".$format[1]."-".$format[0];
		}
		
} 
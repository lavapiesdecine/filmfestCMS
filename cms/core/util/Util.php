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
		
		public static function formatBytes($bytes){
	    	$units = array('B', 'KB', 'MB', 'GB');
	    	for($i=0; $bytes > 1024; $i++){ 
	    		$bytes = $bytes/1024;
	    	}
	    	return number_format($bytes,1)." ".$units[$i];
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
			
			$regexYoutube = "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#";
			
			if (!empty($video)){
				if (preg_match($regexYoutube, $video)){
					preg_match($regexYoutube, $video, $match);
					return "http://www.youtube.com/v/".$match[0];
				}
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
		
		public static function getFiles($folder, $extension){
			$files = array();
			if ($handle = opendir($folder)){
	    		$j = 0;
	    		while (false !== ($file = readdir($handle))) {		
					if (strpos($file, ".$extension")>0){
							$files[$j] = $file;
			        }	
			        $j++;
			    }
			    closedir($handle);
			}
			
			return $files;
		}

		public static function getFolder($dir, $exception){
			$folders = "";
			
			if ($handle = opendir($dir)){
				// loop through the items
				$j = 0;
				while ($folder = readdir($handle)){
					if (!in_array($folder, $exception)){
						$folders[$j] = $folder;
					}
					$j++;
				}
				closedir($handle);
			} 
			return $folders;
		}
				
		public static function recursiveCopy($source, $dest){
			if (is_dir($source)) {
				if (!is_dir($dest)) {
			        mkdir($dest);
			    }
			    $objects = scandir($source);
			    foreach ($objects as $object) {
			    	if ($object != "." && $object != "..") {
			    		\core\util\Log::add($object);
					    if (is_file($object)) {
					        if (!copy($source. DS . $object, $dest . DS . $object)){
					        	\core\util\Error::add("<strong>error en al copiar $source/$object > $dest/$object");
					        }
					    }
					    if (is_dir($object)) {
					    	\core\util\Log::add("recursiveCopy $source/$object > $dest/$object");
			    			self::recursiveCopy($source . DS . $object, $dest . DS . $object);
					    }
			    	}
			    }
			 	reset($objects);
				return true;
			} else {
				return false;
			}
		}
		
	 	/**
	 	 * elimina un directorio.
	 	 * @param path directorio
	 	 */
	 	public static function recursirveRmdir($dir) {
	 		try {
	 		    echo "eliminando $dir";
	 		   if (is_dir($dir)) {
			   	 $objects = scandir($dir);
			   	 foreach ($objects as $object) {
			       if ($object != "." && $object != "..") {
			       	 if (filetype($dir."/".$object) == "dir"){ 
			         	self::recursirveRmdir($dir."/".$object); 
			         } else { 
			         	unlink($dir."/".$object);
			         }
			       }
			     }
			     reset($objects);
			     rmdir($dir);
			   }
			} catch (\Exception $e) {
				\core\util\Log::add("recursirveRmdir $dir");
			}
		}
		
		public static function formatDate2Mysql($date){
			$format = explode("/", $date);
			return $format[2]."-".$format[1]."-".$format[0];
		}
		
} 
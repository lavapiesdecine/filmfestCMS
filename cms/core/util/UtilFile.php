<?php

	namespace core\util;
	
	
	class UtilFile {
		
		
		public function getExtension($type){
			
			$extension = 'pdf';
			
			switch ($type){
				case 'application/pdf':
					$extension = 'pdf';
					break;
				case 'application/x-pdf':
					$extension = 'pdf';
					break;
				case 'application/rtf':
					$extension = 'rtf';
					break;
				case 'image/jepg':
				case 'image/jpg':
					$extension = 'jpg';
					break;
				case 'application/msword':
					$extension = 'doc';
					break;
					
			}
			return $extension;
		}
		
		public function errorUpload($error){
			switch ($error){
				case 1: $msg = 'File exceeded upload_max_filesize'; break;
				case 2: $msg = 'File exceeded max_file_size'; break;
				case 3: $msg = 'File only partially uploaded'; break;
				case 4: $msg = 'No file uploaded'; break;
			}
			return $msg;
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
		
		public static function formatBytes($bytes){
			$units = array('B', 'KB', 'MB', 'GB');
			for($i=0; $bytes > 1024; $i++){
				$bytes = $bytes/1024;
			}
			return number_format($bytes,1)." ".$units[$i];
		}
		
		public static function mkdir($dir){
			if(!is_dir($dir) && !mkdir($dir, 0777, true)){
				throw new \Exception("error al mkdir $dir");
			}
		}
		public static function deleteFile($file){
			if (!unlink($file)){
				throw new \Exception("error al eliminar archivo $file");
			}
		}
		public static function copyFile($src_file, $dst_file){
			if(!copy($src_file, $dst_file)){
				throw new \Exception("error copy $src_file $dst_file ");
			} else {
				chmod($dst_file, 0777);
			}
		}
		public static function recursiveCopy($source, $dest){
			if (is_dir($source)) {
		
				self::mkdir($dest);
		
				$objects = scandir($source);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (is_file($source . DS . $object)) {
							self::copyFile($source. DS . $object, $dest . DS . $object);
						}
						if (is_dir($source . DS . $object)) {
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
			if (is_dir($dir)) {
				$objects = scandir($dir);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (filetype($dir."/".$object) == "dir"){
							self::recursirveRmdir($dir."/".$object);
						} else {
							self::deleteFile($dir."/".$object);
						}
					}
				}
				reset($objects);
				rmdir($dir);
			}
		}
		
		
	}
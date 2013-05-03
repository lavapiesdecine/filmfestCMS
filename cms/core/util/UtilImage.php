<?php

	namespace core\util;
	
	/**
	 * GD Library functions
	 */
	class UtilImage {
	   	
		private static $_ext = ".jpg";
		
		/**
		 * upload img temp dir and process img with GD Library
		 */
		public function processImg($file, $actions, $name){
			 
			if($file['error']){
				$msg = \core\util\UtilFile::errorUpload($_FILES['imagen']['error']);
				throw new \Exception("error upload: $msg ");
				
			}
			 
			$fileTmp = TMP_PATH . $file['name'];
			UtilFile::copyFile($file['tmp_name'], $fileTmp);
		
			$image = new \core\classes\Imagen($fileTmp);
			$image->setName($name);
		
			foreach ($actions as $key => $data){
				switch ($key) {
					case 'crop':
					case 'md':
						self::crop($image, $data['path'], $data['width'], $data['height']);
						break;
					case 'resize':
						self::resize($image, $data['path'], $data['height'], $data['width']);
						break;
					case 'save':
						self::save($image, $data['path']);
						break;
					case 'grayscale':
						self::grayscale($image, $data['path']);
						break;
				}
			}
		
			UtilFile::deleteFile($fileTmp);
		
		}
		
		
		private static function resize($image, $ruta, $alturaMaxima, $anchuraMaxima){
			
			$imageFile = self::imageCreate($image);
			
			if ($image->getHeight() > $alturaMaxima){ 
				$ratio = $image->getHeight() / $alturaMaxima;
				$anchura = round($image->getWidth() / $ratio);
				
				$newImg = self::imagecreatetruecolor($anchura, $alturaMaxima);
				
				self::imagecopyresized($newImg, $imageFile, 0, 0, 0, 0, $anchura, $alturaMaxima, $image->getWidth(), $image->getHeight());
				self::export($newImg, $ruta . $image->getName().self::$_ext, 100);
	        	
	            self::imagedestroy($imageFile); 
		        self::imagedestroy($newImg);
		        
			} else {
				self::export($newImg, $ruta . $image->getName().self::$_ext, 100);
			}
		}
		
		private static function crop($image, $ruta, $x, $y){
			$altura = $image->getHeight();
			$anchura = $image->getWidth();
			$ratio = $anchura/$altura;
			
			$imageFile = self::imageCreate($image);
			$newImg = self::imagecreatetruecolor($x,$y);
			
			$wm = $anchura/$x;
	    	$hm = $altura/$y;
	    	$h_height = $y/2;
	    	$w_height = $x/2;
	    	
	   		if($anchura > $altura) {
	   		 	$adjusted_width = $anchura / $hm;
			    $half_width = $adjusted_width / 2;
			    $int_width = $half_width - $w_height;
	    		self::imagecopyresampled($newImg,$imageFile,-$int_width,0,0,0,$adjusted_width,$y,$anchura,$altura);
	   		} elseif(($anchura < $altura) || ($anchura == $altura)) {
	   		 	$adjusted_height = $altura / $wm;
			    $half_height = $adjusted_height / 2;
			    $int_height = $half_height - $h_height;
			    self::imagecopyresampled($newImg,$imageFile,0,-$int_height,0,0,$x,$adjusted_height,$anchura,$altura);
			} else {
			 	self::imagecopyresampled($newImg,$imageFile,0,0,0,0,$nw,$nh,$anchura,$altura);
			}
			
			self::export($newImg, $ruta . $image->getName().self::$_ext, 100);
			 
			self::imagedestroy($imageFile);
			self::imagedestroy($newImg);
		}
		
		private static function grayscale($image, $ruta, $ext){
			$imageFile = self::imageCreate($image);
			self::imagefilter($imageFile, IMG_FILTER_GRAYSCALE);
			self::export($imageFile, $ruta . $image->getName() . "_off".self::$_ext, 100);
		}
		
		private static function save($image, $ruta, $ext){
			$imageFile = self::imageCreate($image);
			$calidad = 100;
			if ($image->getSize > 100000){
				$calidad = 70;
			}
			self::export($imageFile, $ruta . $image->getName().self::$_ext, $calidad);
		}
		
		
		private function imageCreate($image){
			switch ($image->getExtension()) {
				case 'jpg':
				case 'jpeg':
					$image = self::imagecreatefromjpeg($image->getFile());
					break;
				case 'png':
					$image = self::imagecreatefrompng($image->getFile());
					break;
				case 'gif':
					$image = self::imagecreatefromgif($image->getFile());
					break;
				default:
					throw new \Exception("image type not recognized");
					break;
			}
			return $image;
			
		}
		
		private static function export($image, $path, $calidad){
			self::imagejpeg($image, $path, $calidad);
		}
		
		/**
		 *  gda functions
		 */
		
		private static function imagecreatefromjpeg($file){
			$image = imagecreatefromjpeg($file);
			if(!$image){
				throw  new \Exception("error img ");
			} else {
				return $image;
			}
		}
		private static function imagecreatefrompng($file){
			$image = imagecreatefrompng($file);
			if(!$image){
				throw  new \Exception("error img ");
			} else {
				return $image;
			}
		}
		private static function imagecreatefromgif($file){
			$image = imagecreatefromgif($file);
			if(!$image){
				throw  new \Exception("error img ");
			} else {
				return $image;
			}
		}
		private function imagejpeg($image, $filename, $quality){
			if(!imagejpeg($image, $filename, $quality)){
				throw  new \Exception("error img ");
			}
		}
		private function imagepng($image, $filename, $quality){
			if(!imagepng($image, $filename, $quality)){
				throw  new \Exception("error img ");				
			}
		}
		private function imagedestroy($image){
			 if(!imagedestroy($image)){
			 	throw  new \Exception("error img ");
			 }
		}
		private function imagecopyresized($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
			if(!imagecopyresized($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)){
				throw  new \Exception("error img ");
			}
		}
		private function imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
			if(!imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)) {
				throw  new \Exception("error img ");
			}
		}
		private function imagefilter($image, $filtertype) {
			if(!imagefilter($image, $filtertype)){
				throw  new \Exception("error img ");
			}
		}
		private function imagecreatetruecolor($width, $height){
			$image = imagecreatetruecolor($width, $height);
			if(!$image){
				throw  new \Exception("error img ");
			} else {
				return $image;
			}
		}
		
	}
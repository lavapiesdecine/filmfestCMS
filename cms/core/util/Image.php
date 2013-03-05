<?php

	namespace core\util;
	
	class Image {
	    
	    private $_file;
	    private $_imageWidth;
	    private $_imageHeight;
	    private $_name;
	    private $_path;
	    private $_type;
	    private $_extension;
	    private $_fileSize;
	    
	    public function __construct($file){
	    	$this->_file = $file;
	        $info = getimagesize($file);
	        $this->_imageWidth = $info[0];
			$this->_imageHeight = $info[1];
	        $info = pathinfo($file);
	        Log::add($info, false);
	        $this->_extension = $info['extension'];
	        $this->_fileSize = filesize($file);
		}
	   
		//TODO: funcion optimizar imagenes
		public function optimize(){
			if ($this->_fileSize > 100000){
				
			} 
		}
		
		public function getImageFile(){
			switch ($this->_extension) {
				case 'jpg':
				case 'jpeg':
					$this->_extension = 'jpg';
					return imagecreatefromjpeg($this->_file);
					break;
				case 'png':
					$this->_extension = 'png';
					return imagecreatefrompng($this->_file);
					break;
				case 'gif':
					$this->_extension = 'gif';
					return imagecreatefromgif($this->_file);
					break;
				default:
					throw new \Exception("image type not recognized");
					break;
			}
			
		}
		
		
		public function resize($ruta, $alturaMaxima){
			if ($this->_imageHeight > $alturaMaxima){ 
				$ratio = ($this->_imageHeight / $alturaMaxima);
				$anchura = round($this->_imageWidth / $ratio);
				
				$image = $this->getImageFile();
				$newImg = imagecreatetruecolor($anchura, $alturaMaxima);
				imagecopyresized ($newImg, $image, 0, 0, 0, 0, $anchura, $alturaMaxima, $this->_imageWidth, $this->_imageHeight) or die("<p>problemas al tratar el archivo</p>");
				imagejpeg($newImg, $ruta . "/" . $this->_name.'.jpg', 100);
	        	
	            imagedestroy($image); 
		        imagedestroy($newImg);
			} else {
				$this->save($ruta);
			}
		}
		
		public function crop($ruta, $x, $y){
			$altura = $this->_imageHeight;
			$anchura= $this->_imageWidth;
			$ratio = $anchura/$altura;
			
			$image = $this->getImageFile();
			$newImg = imagecreatetruecolor($x,$y);
			
			$wm = $anchura/$x;
	    	$hm = $altura/$y;
	    	$h_height = $y/2;
	    	$w_height = $x/2;
	   		 if($anchura > $altura) {
	   		 	$adjusted_width = $anchura / $hm;
			    $half_width = $adjusted_width / 2;
			    $int_width = $half_width - $w_height;
	    		imagecopyresampled($newImg,$image,-$int_width,0,0,0,$adjusted_width,$y,$anchura,$altura);
	   		 } elseif(($anchura < $altura) || ($anchura == $altura)) {
	   		 	$adjusted_height = $altura / $wm;
			    $half_height = $adjusted_height / 2;
			    $int_height = $half_height - $h_height;
			    imagecopyresampled($newImg,$image,0,-$int_height,0,0,$x,$adjusted_height,$anchura,$altura);
			 } else {
			 	imagecopyresampled($newImg,$image,0,0,0,0,$nw,$nh,$anchura,$altura);
			 }
			 if(!imagejpeg($newImg, $ruta . $this->_name . ".jpg", 100)){
			 	throw new \Exception("error al guardar ".$ruta . $this->_name . ".jpg");
			 } 
			 
			 imagedestroy($image);
			 imagedestroy($newImg);
		}
		
		public function grayscale($ruta){
			$img = imagecreatefrompng($ruta);
			imagefilter($im, IMG_FILTER_GRAYSCALE);
			imagepng($im, 'dave.png');
		}
		
		public function save($ruta){
			$calidad = 100;
			$image = $this->getImageFile();
			
			if(!imagejpeg($image, $ruta . DS . $this->_name . ".jpg", 100)){
				throw new \Exception("error al guardar ".$ruta . $this->_name . ".jpg");
			}
			/*
			if(!move_uploaded_file($this->_file, $ruta . $this->_name . ".jpg")) {
				throw new \Exception("error al mover " .  $ruta . $this->_name . ".jpg");
			}*/
		}
		
		public function deleteFile(){
			unlink($this->_file);
		}
		
		
		public function getWidth(){
			return $this->_imageWidth;
		}
		public function getHeight(){
			return $this->_imageHeight;
		}
		
		public function getDimension(){
			return $this->_imageWidth."px x ".$this->_imageHeight."px";
		}
		
		public function getSize(){
			return Util::formatBytes($this->_fileSize);
		}
		
		public function getName(){
			return $this->_name;
		}
		public function setName($name){
			$this->_name = $name;
		}
		public function getPath(){
			return $this->_path;
		}
		public function setPath($path){
			$this->_path = $path;
		}
		public function getType(){
			return $this->_type;
		}
		public function setType($type){
			$this->_type = $type;
		}
		public function getExtension(){
			return $this->_extension;
		}
		public function setExtension($extension){
			$this->_extension = $extension;
		}
	}
<?php

	namespace core\classes;
	
	class Imagen extends File{
	    
	    private $_width;
	    private $_height;
	    
	    public function __construct($file){
	    	$this->setFile($file);
	        $info = getimagesize($file);
	        $this->_width = $info[0];
			$this->_height = $info[1];
	        $info = pathinfo($file);
	        $this->setExtension($info['extension']);
	        $this->setFileSize(filesize($file));
		}
		
		public function getDimension(){
			return $this->_width."px x ".$this->_height."px";
		}
		
		public function getWidth(){
			return $this->_width;
		}
		public function getHeight(){
			return $this->_height;
		}
		
	}
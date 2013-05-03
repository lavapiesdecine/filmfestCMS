<?php

	namespace core\classes;
	
	class File {
	    
	    private $_file;
	    private $_name;
	    private $_path;
	    private $_type;
	    private $_extension;
	    private $_fileSize;
	    
	    public function __construct($file){
	    	$this->_file = $file;
	        $info = pathinfo($file);
	        $this->_extension = $info['extension'];
	        $this->_fileSize = filesize($file);
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
		public function getFile(){
			return $this->_file;
		}
		public function setFile($file){
			$this->_file = $file;
		}
		public function getFileSize(){
			return $this->_fileSize;
		}
		public function setFileSize($size){
			$this->_fileSize = $size;
		}
		
		public function getSize(){
			return \core\util\UtilFile::formatBytes($this->_fileSize);
		}
		
		public function __toString(){
			return "File  nombre: ".$this->getName(). " | extension ".$this->getExtension(). " | size ".$this->getSize() ;
		}
		
		
	}
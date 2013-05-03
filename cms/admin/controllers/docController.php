<?php

namespace admin\controllers;

class docController extends \core\AdminController{
	
    public function __construct($data) {
    	$this->_title = _("doc.title");
	    $this->_description = _("doc.description");
	    
	    $this->_tabla = "docs";
	    
	    $this->_imgUrl = URL_LOGO . "docs" . "/";
	    $this->_imgPath = LOGO_PATH . "docs" . DS;
	    $this->_imgAction = array("crop" => array("path" => $this->_imgPath,
	    										  "height" =>"80", "width" => "80"));
	    	
	    parent::__construct($data);
    }
    
    public function index(){
		$nombre = "";
		$nombreImg = "";
		$descripcion = "";
		$classUpload = "upload-img";
		$classUploadImg = "upload-img";
		$fileName = "";
		$fileNameImg = "";
		$urlFile = "";
		$file = "";
		$infoFile = null; 
    	$sizeFile = 0;
    	$extension = "";
		
	    if(!empty($this->_id)){
	    	
	   		$documentoDAO = $this->_dao->select($this->_id, $this->_tabla);
	        if(!empty($documentoDAO)){
				$classUpload = "";
				$file = DOC_PATH.$documentoDAO->archivo;
				$infoFile = pathinfo($file);
				$sizeFile = \core\util\UtilFile::formatBytes(filesize($file));
				$nombre = $documentoDAO->nombre;
				$nombreImg = $documentoDAO->imagen;
				if(!empty($nombreImg)){
					$fileNameImg = $this->_imgUrl . $documentoDAO->imagen;
					$classUploadImg = "";
				}
				$descripcion = $documentoDAO->descripcion;
				$fileName = $documentoDAO->archivo;
				$urlFile = URL_DOC.$documentoDAO->archivo;
				$extension = $infoFile['extension'];
			}
	    }	
	 	
    	$this->addData(array("listado" => $this->_dao->documentosDAO($this->_anyo),
    							"nombre" => $nombre, "nombreImg" => $nombreImg, "urlFile" =>  $urlFile,
    							"descripcion" => $descripcion,"classUpload" => $classUpload, "classUploadImg" => $classUploadImg,
    							"fileName" => $fileName,"fileNameImg" => $fileNameImg, "infoFile" => $infoFile, 
    							"sizeFile" => $sizeFile,"file"=>$file, "extension"=>$extension));
    	
		$this->loadView();
    }
    
	public function alta(){
 		
		$campos = array("nombre" => $_POST['nombre'], 
						"imagen" => $_POST['file_imagen'],
						"descripcion" =>  $_POST['descripcion'],
						"archivo" => $_POST['file_doc'],
						"muestra" => $_POST['id_edicion']);
		
		echo $this->insert($_POST['id'], $campos);
		
    }
    
	public function delete(){
 		
    	$id = $_POST['id'];
    	
    	try {
			$documentoDAO = $this->_dao->select($id, $this->_tabla);
			if(isset($documentoDAO->archivo)){
				if($this->_dao->delete($id, $this->_tabla)){
					\core\util\UtilFile::deleteFile($this->_imgPath . $documentoDAO->imagen);
					\core\util\UtilFile::deleteFile(DOC_PATH.$documentoDAO->archivo);
				}
			}
		} catch (\Exception $e){
			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		}
		echo json_encode($this->_result);
    }
	
	public function uploadDoc(){ 	
		if(isset($_FILES['doc'])){

			try{
				
				if ($_FILES['doc']['error'] > 0){
					$msg = \core\util\UtilFile::errorUpload($_FILES['doc']['error']);
					throw new \Exception("error upload: $msg " . $_FILES['doc']['name']);
				}
		 
				if(isset($_POST['id_nombreDoc'])){
					$nombreDoc = \core\util\Util::stripAccents($_POST['id_nombreDoc']);
				}
		 
				$extension = \core\util\UtilFile::getExtension($_FILES['doc']['type']);
		 
				$file_dest = DOC_PATH.$nombreDoc.".".$extension;
		 
				\core\util\UtilFile::copyFile($_FILES['doc']['tmp_name'], $file_dest);
		 
				$file = new \core\classes\File($file_dest);
				$file->setName($nombreDoc);

				$id = $this->_dao->insertId(array("muestra" => $this->_anyo, "nombre" => $file->getName(), "archivo" => $file->getName().".".$file->getExtension()), $this->_tabla);

				$html = "<head><link rel='stylesheet' href='".$this->_base . "/vista/skins/" . $this->_data->getSkin() . "/css/upload.css"."' type='text/css' />"
						."<input type='hidden' id='id' value='$id' />"
						."<input type='hidden' id='nombre_doc' value='".$file->getName().".".$file->getExtension()."' />"
						."<a  class=".$file->getExtension()." href='".URL_DOC.$file->getName().".".$file->getExtension()."'>".$file->getName().".".$file->getExtension()."</a></strong> <span>".$file->getSize()."</span>";
				
				echo $html;

			} catch (\Exception $e){
		    	$this->showError($e->getMessage());
		    }
	    }
	}
	
	public function upload(){
		$this->uploadImg(null, $_POST['id_nombre']);
	}
	
	
}
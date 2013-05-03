<?php

	namespace admin\controllers;
	
	class paginaController extends \core\AdminController{
		
	     public function __construct($data) {
	    	$this->_tabla = "pagina";
	    	$this->_title = _("pagina.title");
	    	$this->_description = _("pagina.description");
	    	parent::__construct($data);
	    }
	    
	    public function index(){
			$urlPagina = "";
			$langPagina = "";
			$layoutPagina  = "";
			$idPaginaPadre  = 0;
			$idWebModulo  = 0;
			$orden = 0;
			$portada = false;
			$textos = "";
			$skinPagina = $this->_anyo;
			
			 if(!empty($this->_id)){
		   		$paginaDAO = $this->_dao->select($this->_id, $this->_tabla);
		  		$idPaginaPadre = $paginaDAO->id_paginapadre;
		  		$idWebModulo = $paginaDAO->id_webmodulo;
		  		$urlPagina = $paginaDAO->url;
		  		$layoutPagina = $paginaDAO->layout;
		  		$skinPagina = $paginaDAO->skin;
		  		$textos =  $this->_dao->textosPaginaDAO($this->_id);
		  		$menuDAO = $this->_dao->webMenuDAO($this->_id);
	
		  		if(!empty($menuDAO)){
		  			$orden = $menuDAO->orden;
		  			$portada = $menuDAO->portada=='S';
		  		}
			}
		 	
	    	$this->addData(array("listado" => $this->_dao->paginasDAO($this->_anyo),
	    							"textosPosibles" => $this->_dao->textosPosiblesPaginaDAO($this->_anyo),
	    							"textos" => $textos,
	    							"orden" => $orden,
									"urlPagina" => $urlPagina,
									"layoutPagina"=>$layoutPagina,
	    							"idPaginaPadre" => $idPaginaPadre,
			  						"idWebModulo" => $idWebModulo,
	    							"portada" => $portada, 
	    							"paginasPadre" => $this->_dao->paginasPadreDAO($this->_id, $this->_anyo), 
	    							"layouts" => \core\util\UtilFile::getFiles(WEB_PATH . "vista" . DS . "skins" . DS . $skinPagina . DS . "layouts", "phtml"),
	    							"skins" => \core\util\UtilFile::getFolder(WEB_PATH . "vista" . DS . "skins", array(".", "..","commons")),
	    							"skinPagina" => $skinPagina,
	    							"webmodulos" => $this->_dao->webModulosSelectDAO()));
			
			$this->loadView();
	
	    }   
	   	
	    public function alta(){
	 		
	    	$id = $_POST['id'];

	    	try{
	    	
	    		$this->_dao->startTransaction();
		 		
				$campos = array("url"=>$_POST['id_url'],"id_webmodulo"=>$_POST['id_modulo'],"skin"=>$_POST['id_skin'], "layout"=>$_POST['id_layout'],"id_paginapadre"=>$_POST['id_paginapadre']);
		 		
		 		if(empty($id)){
		 			$campos = array_merge($campos, array("muestra"=>$_POST['id_edicion']));
		 			$id = $this->_dao->insertId($campos, $this->_tabla);
				}
				else{
					$this->_dao->update($id, $campos, $this->_tabla);
				}
				
				//textos
				if (!empty($_POST['textosSelected'])){
					$textos = explode(",", $_POST['textosSelected']);
					$this->_dao->deleteTextoPagina($id);
					foreach ($textos as $id_texto) {
						$this->_dao->insert(array("id_pagina" => $id, "id_texto" => $id_texto), "pagina_texto");
					}
				}
				
				//menu
				if (!empty($_POST['id_orden'])){
					$this->_dao->deleteMenu($id);
					$this->_dao->insert(array("id_pagina" => $id, "orden"=>$_POST['id_orden'], "portada"=> $_POST['id_portada']=='S'?'S':'N'), "menu");
				} else {
					$this->_dao->deleteMenu($id);
				}
					
				$this->_dao->commit();
	
	 		} catch (\Exception $e) {
	 			$this->_result = array("ok" => false, "msg" => $e->getMessage());
		    	$this->_dao->rollback();
		    }
		    echo json_encode($this->_result);
	    }
	    
	    public function load(){
	    	
	    	$options = "<option value='0'>"._("form.select")."</option>";
	    	
			if(isset($_POST['id_edicion'])){
				$paginasPadreDAO = $this->_dao->paginasPadreDAO(null, $_POST['id_edicion']);
				foreach ($paginasPadreDAO as $paginaPadre) {
					$options .= "<option value='".$paginaPadre->id."'>".$paginaPadre->titulo."</option>";
				}
			}
			print $options;
	    }

	    public function loadLayouts(){
	    	$options = "<option value='0'>"._("form.select")."</option>";
	    	
	    	if(isset($_POST['id_skin'])){
	    		
	    		$layouts = \core\util\UtilFile::getFiles(WEB_PATH . "vista" . DS . "skins" . DS . $_POST['id_skin'] . DS . "layouts", "phtml");
	    			
	    		foreach ($layouts as $layout) {
	    				$options .= "<option value='".$layout."'>".$layout."</option>";
	    		}
	    		
	    		print $options;
	    		
			}
	    }
	    
	}
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
	    							"layouts" => \core\util\Util::getFiles(WEB_PATH . "vista" . DS . "skins" . DS . $skinPagina . DS . "layouts", "phtml"),
	    							"skins" => \core\util\Util::getFolder(WEB_PATH . "vista" . DS . "skins", array(".", "..","commons")),
	    							"skinPagina" => $skinPagina,
	    							"webmodulos" => $this->_dao->webModulosSelectDAO()));
			
			$this->loadView();
	
	    }   
	   	
	    public function alta(){
	 		
	    	$idMenu = $_POST['id'];
	 		$this->_dao->startTransaction();
	 		
			$campos = array("url"=>$_POST['id_url'],"id_webmodulo"=>$_POST['id_modulo'],"skin"=>$_POST['id_skin'], "layout"=>$_POST['id_layout'],"id_paginapadre"=>$_POST['id_paginapadre']);
	 		
	 		if(empty($idMenu)){
	 			$campos = array_merge($campos, array("muestra"=>$_POST['id_edicion']));
	 			$idMenu = $this->_dao->insertId($campos, $this->_tabla);
	 			$ok = $idMenu > 0;
			}
			else{
				$ok = $this->_dao->update($idMenu, $campos, $this->_tabla);
			}
			
			//textos
			if (!empty($_POST['textosSelected'])){
				$textos = explode(",", substr($_POST['textosSelected'],1));
				if($ok){
					if($this->_dao->deleteTextoPagina($idMenu)){
						foreach ($textos as $id_texto) {
							$this->_dao->insert(array("id_pagina" => $idMenu, "id_texto" => $id_texto), "pagina_texto");
						}
					}
				}
			}
			
			//menu
			if($ok){
				if (!empty($_POST['id_orden'])){
					if($this->_dao->deleteMenu($idMenu)){
						$this->_dao->insert(array("id_pagina" => $idMenu, "orden"=>$_POST['id_orden'], "portada"=> $_POST['id_portada']=='S'?'S':'N'), "menu");
					} 
				} else {
					$this->_dao->deleteMenu($idMenu);
				}
			}	
			
			if($ok){
				$this->_dao->commit();
			} else {
				$this->_dao->rollback();
			}
			echo $ok;
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
	    		
	    		$layouts = \core\util\Util::getFiles(WEB_PATH . "vista" . DS . "skins" . DS . $_POST['id_skin'] . DS . "layouts", "phtml");
	    			
	    		foreach ($layouts as $layout) {
	    				$options .= "<option value='".$layout."'>".$layout."</option>";
	    		}
	    		
	    		print $options;
	    		
			}
	    }
	    
	}
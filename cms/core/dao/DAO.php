<?php

namespace core\dao;

class DAO extends Database{
	
	private static $_singleton;
	
	public static function getInstance(){
		if (is_null(self::$_singleton)) {
			self::$_singleton = new DAO();
		}
		return self::$_singleton;
	}
	
	public function __construct() {
	   parent::__construct();
    }
	
    /**
     * load pagina 
     */
    public function paginaPortada($anyo, $lang){
    	
    	$sql = "SELECT q. *
				FROM (
					 select p.id, p.muestra anyo, m.modulo, p.layout, p.skin, p.url, t.titulo, t.id as id_texto, p.id_paginapadre, IFNULL( t.lang, '".DEFAULT_LANG."' ) lang, IF( t.lang = '".DEFAULT_LANG."', 2, 1 ) orden
    				 from ".$this->_prefix."pagina p
    				 LEFT JOIN ".$this->_prefix."pagina_texto pt ON p.id = pt.id_pagina, ".$this->_prefix."textos t,
    				 ".$this->_prefix."web_modulos m, ".$this->_prefix."menu mn
    				 where m.id=p.id_webmodulo and p.id = mn.id_pagina and pt.id_texto=t.id and mn.portada='S' and p.muestra = '$anyo' and p.alta='S' AND t.lang IN ('".DEFAULT_LANG."', '$lang')
    				 union
    				 SELECT p.id, p.muestra anyo, m.modulo, p.layout, p.skin, p.url, '' titulo, 0, p.id_paginapadre, '".DEFAULT_LANG."' lang, 3 orden
					 FROM ".$this->_prefix."pagina p, ".$this->_prefix."web_modulos m, ".$this->_prefix."menu mn
					 WHERE m.id = p.id_webmodulo
					 and p.id = mn.id_pagina 
					 and mn.portada='S'
					 and p.alta='S'
					 and p.muestra='$anyo'
    				 ) q
    			ORDER BY orden
    			LIMIT 0 , 1";
    	
    	\core\util\Log::add($sql, false);
    	return parent::selectQuery($sql, false, __FUNCTION__.$anyo.$lang);
    }
    
    public function paginaDAO($pagina, $anyo, $lang){
		
			$sql = "SELECT q. *
					FROM (
						SELECT p.id, p.muestra anyo, 'texto' modulo, p.layout, p.skin, p.url, t.titulo, t.texto descripcion, t.id as id_texto, p.id_paginapadre, IFNULL( t.lang, '".DEFAULT_LANG."' ) lang, IF( t.lang = '".DEFAULT_LANG."', 2, 1 ) orden, p.alta
						FROM ".$this->_prefix."pagina p
						LEFT JOIN ".$this->_prefix."pagina_texto pt ON p.id = pt.id_pagina, ".$this->_prefix."textos t
						LEFT JOIN ".$this->_prefix."pagina_texto pt2 ON t.id = pt2.id_texto
						WHERE pt.id_pagina = pt2.id_pagina
						AND pt.id_texto = pt2.id_texto
						AND t.lang IN ('".DEFAULT_LANG."', '$lang')
						UNION
						SELECT p.id, p.muestra anyo, m.modulo, p.layout, p.skin, p.url, '' titulo, '' descripcion, 0, p.id_paginapadre, '".DEFAULT_LANG."' lang, 3 orden, p.alta
						FROM ".$this->_prefix."pagina p, ".$this->_prefix."web_modulos m
					WHERE m.id = p.id_webmodulo	) q
					WHERE url = '$pagina' and anyo = '$anyo' and alta = 'S'
					ORDER BY orden
					LIMIT 0 , 1";
			
			\core\util\Log::add($sql, false);
			return parent::selectQuery($sql, false, __FUNCTION__.$pagina.$anyo.$lang);
	}
	public function paginaByIdDAO($controller, $id, $anyo){
		if ($controller=='pelicula'){
			$sql = "select 0 id, muestra as anyo, 'pelicula' modulo, 'pelicula.phtml' layout, muestra skin, 0 id_texto, 0 id_paginapadre from ".$this->_prefix."peliculas where id='$id'";
		} else if($controller=='proyecciones'){
			$sql = "select 0 id, anyo, 'proyecciones' modulo, '2columnas.phtml' layout, anyo skin, 0 id_texto, 0 id_paginapadre from ".$this->_prefix."proyecciones ".($id>0 ? "where DATE_FORMAT(dia, '%d%m%Y') ='$id'" : "where anyo ='$anyo'");
		}
		\core\util\Log::add($sql, false);
		return parent::selectQuery($sql, false, __FUNCTION__.$controller.$id.$anyo);
	}
	
	/* ediciones */
	public function edicionDAO($id){
		return parent::selectQuery("select id, nombre, descripcion, cartel, fecha_inicio, fecha_fin from ".$this->_prefix."ediciones where id='$id'", false, __FUNCTION__.$id);
	}
	public function edicionesDAO(){
		return parent::selectQuery("select id, nombre, descripcion, cartel, DATE_FORMAT(fecha_inicio, '%d/%c') as inicio, DATE_FORMAT(fecha_fin, '%d/%c') as fin from ".$this->_prefix."ediciones e where e.alta='S' order by e.fecha_inicio asc", true, __FUNCTION__);
	}
	 
	/* autoproducciones */
	public function convocatoriaDAO($anyo){
		return parent::selectQuery("select e.id, e.nombre, DATE_FORMAT(e.fecha_inicio, '%d/%c') as inicio, DATE_FORMAT(e.fecha_fin, '%d/%c') as fin, c.url, c.cartel, c.descripcion from ".$this->_prefix."convocatorias c, ".$this->_prefix."ediciones e where e.id=c.id and c.alta='S' and c.id='$anyo' order by e.id desc", true, __FUNCTION__.$anyo);
	}
	public function convocatoriasDAO(){
		return parent::selectQuery("select e.id, e.nombre, DATE_FORMAT(e.fecha_inicio, '%d/%c') as inicio, DATE_FORMAT(e.fecha_fin, '%d/%c') as fin, c.url, c.cartel, c.descripcion from ".$this->_prefix."convocatorias c, ".$this->_prefix."ediciones e where e.id=c.id and c.alta='S' order by e.id desc", true, __FUNCTION__); 
	}
	
	/* menu */
	public function menuDAO($anyo){
		return parent::selectQuery("select p.id, p.url, p.id_paginapadre as modulo_padre, m.portada from ".$this->_prefix."pagina p, ".$this->_prefix."menu m where p.id=m.id_pagina and p.alta='S' and p.muestra='$anyo' and id_paginapadre=0 order by m.orden", true, __FUNCTION__.$anyo);
	}
	public function submenuDAO($idPaginaPadre){
		return parent::selectQuery("select p.id, p.url, m.orden as orden, p.id_paginapadre as modulo_padre, m.portada from ".$this->_prefix."pagina p, ".$this->_prefix."menu m where p.id=m.id_pagina and p.alta='S' and p.id_paginapadre=$idPaginaPadre order by m.orden", true, __FUNCTION__.$idPaginaPadre);
	}
	
	/*mail confirmacion */
	public function datosContactoDAO($idPelicula){
		return parent::selectQuery("select p.titulo, a.email, a.autor from ".$this->_prefix."autores a, ".$this->_prefix."peliculas p where p.id=a.id_pelicula and a.id_pelicula=$idPelicula", false, __FUNCTION__.$idPelicula); 	   		
	}
    
	/* material inscrito */
	public function numeroAutoproduccionesDAO($anyo){
		return parent::selectQuery("select p.titulo from ".$this->_prefix."peliculas p, ".$this->_prefix."convocatoria c where p.id=c.id_pelicula and p.muestra='$anyo' and c.alta='S'", true, __FUNCTION__.$anyo);
	}
    public function listadoAutoproduccionesDAO($anyo, $inicio, $total){
			$sql = "select p.titulo, c.id, c.duracion, c.anyo, c.genero, c.pais, l.id as id_licencia, l.nombre as nombre_licencia, c.autor, p.sinopsis, c.fecha_alta, p.enlace as video,
						    IF(i.imagen is null, '".URL_IMG."gris.jpg', concat('".URL_IMG."peliculas/$anyo/tn/', i.imagen)) as cartel
							from ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula, ".$this->_prefix."convocatoria c, ".$this->_prefix."licencias l 
						    where p.id=c.id_pelicula and p.id_licencia = l.id and p.muestra='$anyo' and c.alta='S' order by p.id desc LIMIT $inicio, $total";
			return parent::selectQuery($sql, true, __FUNCTION__.$anyo.$inicio.$total);
	}
	
	/* material seleccionado */
	public function seleccionDAO($anyo){
			$sql = "select p.id, p.titulo, c.autor, p.muestra, 
							IF(i.imagen is null, '".URL_IMG."gris.jpg', concat('".URL_IMG."peliculas/$anyo/md/', i.imagen)) as cartel 
							from ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula, 
							".$this->_prefix."peliculas p1 LEFT JOIN ".$this->_prefix."proyeccion_pelicula pp ON p1.id = pp.id_pelicula,									
							".$this->_prefix."convocatoria c 
							where p.id=c.id_pelicula and p.id = p1.id and p.muestra='$anyo' and pp.id_proyeccion > 0";
			
			return parent::selectQuery($sql, true, __FUNCTION__.$anyo);
	}
	
	/* texto */
	public function textoDAO($id){
		return parent::selectQuery("select t.*, gt.id_galeria from ".$this->_prefix."textos t LEFT JOIN ".$this->_prefix."galeria_texto gt ON t.id=gt.id_texto where t.id=$id", false, __FUNCTION__.$id);
	}
	
	/* espacios */
	public function espaciosDAO($anyo){
		return parent::selectQuery("select * from ".$this->_prefix."espacios where alta='S' and id in (select id_espacio from ".$this->_prefix."proyecciones where anyo='$anyo')", true, __FUNCTION__.$anyo);
	}
	
	/* img y video */
	public function escaparateDAO(){
		return parent::selectQuery("select * from ".$this->_prefix."galerias g, ".$this->_prefix."imagenes i where g.id = i.id_galeria and g.alta='S' and i.alta='S' order by g.id", true, __FUNCTION__); 
	}
	
	/* img para textos */
	public function imagenesDAO($idGaleria){ 
		return parent::selectQuery("select i.*, g.galeria from ".$this->_prefix."imagenes i, ".$this->_prefix."galerias g where i.id_galeria=g.id and i.id_galeria=$idGaleria order by i.id", true, __FUNCTION__.$idGaleria);
	}
	
	/* proyecciones */
	public function proyeccionesDAO($diaProyeccion, $anyo){
		$sql = "select pr.titulo, pr.dia, TIME_FORMAT(pr.hora,'%H:%i') as hora, e.espacio, e.direccion, e.id as id_espacio, pr.id as id_proyeccion from ".$this->_prefix."espacios e, ".$this->_prefix."proyecciones pr where e.id=pr.id_espacio and pr.alta='S'";
		if(empty($diaProyeccion)){
			$sql .= " and pr.anyo = '$anyo' order by pr.dia, pr.hora";
		} else {
			$sql .= " and DATE_FORMAT(pr.dia, '%d%m%Y') = '$diaProyeccion' order by pr.hora";
		}
		
	    return parent::selectQuery($sql, true, __FUNCTION__.$diaProyeccion.$anyo);
	}
	public function proyeccionesPorDiaDAO($id){
		return parent::selectQuery("select pr.id as id_proyeccion, pr.titulo, TIME_FORMAT(pr.hora,'%H:%i') as hora, e.espacio from ".$this->_prefix."espacios e, ".$this->_prefix."proyecciones pr where e.id=pr.id_espacio and pr.dia = '$id' and pr.alta='S' order by pr.hora", true, __FUNCTION__.$id); 
	}
	public function peliculasProyeccionesDAO($id){
		return parent::selectQuery("select p.id, p.titulo, p.ficha_tecnica, p.sinopsis, i.imagen as cartel 
						   			from ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula, 
									".$this->_prefix."peliculas p2 LEFT JOIN ".$this->_prefix."proyeccion_pelicula pp  ON p2.id = pp.id_pelicula
									where p.id=p2.id and pp.id_proyeccion=$id 
									order by p.id asc", true,__FUNCTION__.$id); 	
	}
	
	/* pelicula */
	public function peliculaDAO($id){
		return parent::selectQuery("select * from ".$this->_prefix."peliculas where id=$id", false, __FUNCTION__.$id);   
	}
	public function agradecimientoDAO($id){
		return parent::selectQuery("select * from ".$this->_prefix."donantes where id=$id", false, __FUNCTION__.$id);
	}
	
	public function fichaPeliculaDAO($id){
		return parent::selectQuery("SELECT p.* , i.imagen AS cartel, l.nombre AS nombre_licencia, l.url AS url_licencia, dp.id_donante
									FROM ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula, 
								    ".$this->_prefix."peliculas pe LEFT JOIN ".$this->_prefix."donante_pelicula dp ON pe.id = dp.id_pelicula,
									".$this->_prefix."licencias l
									WHERE p.id = pe.id
									AND p.id_licencia = l.id and p.id=$id", false, __FUNCTION__.$id);
		
	}
	
	
	/* descargas */
	public function documentosDAO($anyo){
		return parent::selectQuery("select * from ".$this->_prefix."docs where id>0 and muestra='$anyo' and alta='S'", true, __FUNCTION__.$anyo); 
	}
	
	//langs
	public function langsEdicionDAO($anyo){
		return parent::selectQuery("select l.* from ".$this->_prefix."langs l, ".$this->_prefix."lang_edicion e where l.lang=e.lang and id_edicion='$anyo'", true, __FUNCTION__.$anyo); 
	}

}
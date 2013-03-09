<?php 

namespace core\dao;

class AdminDAO extends Database{
	
	private static $_singleton;
	
	public static function getInstance(){
		if (is_null(self::$_singleton)) {
			self::$_singleton = new AdminDAO();
		}
		return self::$_singleton;
	}
	
	public function __construct() {
	   parent::__construct();
    }
	
    /* validacion */
	public function validaDAO($nombre){
		$sql = sprintf("SELECT u.id, pass, u.usuario, u.email, u.logo FROM ".$this->_prefix."usuarios u WHERE u.usuario='%s'", parent::sanitize($nombre));
		return parent::selectQuery($sql);
	}
	  
    /* menu */
    public function menuDAO($nivelAcceso){
    	return parent::selectQuery("SELECT m.id, m.modulo, mp.id_perfil, m.modulo_padre, m.modulo as url, 'N' as portada FROM ".$this->_prefix."modulos m, ".$this->_prefix."modulo_perfil mp WHERE m.id=mp.id_modulo and mp.id_perfil=$nivelAcceso and modulo_padre=0 and alta='S'", true);
	}
	
	public function submenuDAO($moduloMenu){
		return parent::selectQuery("SELECT m.id, m.modulo, mp.id_perfil, m.modulo_padre, m.modulo as url, 'N' as portada FROM ".$this->_prefix."modulos m, ".$this->_prefix."modulo_perfil mp WHERE m.id=mp.id_modulo and m.modulo_padre=$moduloMenu and alta='S'", true);
	}
	 
    /*usuario*/
	public function usuariosDAO(){
		return parent::selectQuery("select id, usuario as titulo, alta from ".$this->_prefix."usuarios order by id", true);
	}
	public function nivelesAccesoDAO(){
		return parent::selectQuery("select id, profile as titulo, alta from ".$this->_prefix."perfiles order by id", true);
	}
	public function nivelesNavegacionDAO(){
		return parent::selectQuery("select id, profile as titulo from ".$this->_prefix."perfiles where alta='S' order by id", true);
	}
	
	public function nivelesNavegacionUsuarioDAO($usuario){
		return parent::selectQuery("select pa.id, pa.profile as titulo from ".$this->_prefix."perfiles pa, ".$this->_prefix."usuario_perfil up where pa.id=up.id_perfil and up.id_usuario=$usuario order by pa.id", true);
	}
	public function nivelesNavegacionPosiblesDAO($niveles){
		$sql = "select id, profile as titulo from ".$this->_prefix."perfiles where alta='S' ";
		if(!empty($niveles)){
			$sql .= "and id not in ($niveles) ";
		}
		$sql .= "order by id";
		return parent::selectQuery($sql, true);
	}
	
	public function perfilesAccesoDAO($usuario){
		return parent::selectQuery("select pa.* from ".$this->_prefix."perfiles pa, ".$this->_prefix."usuario_perfil up where pa.id = up.id_perfil and up.id_usuario = $usuario order by pa.id", true);
	}
	
	public function modulosPosiblesDAO(){
		return parent::selectQuery("select id, modulo titulo from ".$this->_prefix."modulos where id not in (select id from ".$this->_prefix."modulo_perfil)", true);
	}
	public function modulosPerfilDAO($perfil){
		return parent::selectQuery("select m.id, m.modulo titulo from ".$this->_prefix."modulos m, ".$this->_prefix."modulo_perfil mp where m.id = mp.id_modulo and mp.id_perfil = $perfil", true);
	}
	
	/* modulos */
	public function adminModulosDAO(){ 
		return parent::selectQuery("select am.id, am.modulo as titulo, am.alta from ".$this->_prefix."modulos am", true);
	}
	public function adminModuloNombreDAO($modulo, $perfiles){
		return parent::selectQuery("select m.*, mp.id_perfil from ".$this->_prefix."modulos m, ".$this->_prefix."modulo_perfil mp where m.id=mp.id_modulo and m.modulo='$modulo' and mp.id_perfil in ($perfiles)");
	}
	public function modulosNivelAccesoDAO($nivelAcceso){ 
		$sql = "select id, modulo from ".$this->_prefix."modulos where alta='S'";
		if ($nivelAcceso>0){
			$sql = "select m.id, m.modulo from ".$this->_prefix."modulos m, ".$this->_prefix."modulo_perfil mp where m.id=mp.id_modulo and m.alta='S' and mp.id_perfil=$nivelAcceso";
		}
		return parent::selectQuery($sql, true);
	}
	
	/**
	 * TODO check padre e hijo mismo perfil
	 */
	public function modulosPadreDAO($id){ 
		$sql = "select id, modulo as titulo from ".$this->_prefix."modulos where alta='S'";
		if(!empty($id)){
			$sql .= " and id<>$id";
		}
		return parent::selectQuery($sql, true);
	}
	
	//listado
	public function webModulosDAO(){
		$sql = "select id, modulo titulo, alta from ".$this->_prefix."web_modulos order by titulo";
		return parent::selectQuery($sql, true);
	}
	
	/* edicion muestras */
	public function edicionDAO($id){
		return parent::selectQuery("select e.id, e.nombre, e.descripcion, e.cartel, e.fecha_inicio, e.fecha_fin from ".$this->_prefix."ediciones e where e.id='$id'", false, __FUNCTION__.$id);
	}
	public function edicionesDAO(){
		return parent::selectQuery("select id, nombre as titulo, alta, cartel from ".$this->_prefix."ediciones order by id", true);
	}
	
	/* convocatorias */
	public function convocatoriaDAO($anyo){
		return parent::selectQuery("select e.id, e.nombre, DATE_FORMAT(e.fecha_inicio, '%d/%c') as inicio, DATE_FORMAT(e.fecha_fin, '%d/%c') as fin, c.url, c.cartel, c.descripcion from ".$this->_prefix."convocatorias c, ".$this->_prefix."ediciones e where e.id=c.id and c.id='$anyo' order by e.id desc");
	}
	public function convocatoriasDAO(){
		return parent::selectQuery("select e.id, e.nombre as titulo, c.alta from ".$this->_prefix."convocatorias c, ".$this->_prefix."ediciones e where e.id=c.id", true);
	}
	
	
	/**
	 *  paginas web 
	 */
	//listado
	public function paginasDAO($anyo){ 
		return parent::selectQuery("select id, url as titulo, alta from ".$this->_prefix."pagina where muestra='$anyo' order by alta desc, url", true);
	}
	public function urlPaginasDAO($anyo){ 
		return parent::selectQuery("select url as id, url as titulo from ".$this->_prefix."pagina where muestra='$anyo' order by url", true);
	}
	public function webMenuDAO($id){ 
		return parent::selectQuery("select * from ".$this->_prefix."menu where id_pagina=$id");
	}
	public function paginasPadreDAO($id, $anyo){
		$sql = "select id, url as titulo from ".$this->_prefix."pagina where muestra='$anyo' and alta='S'";
	  	if(!empty($id)){
			$sql .= " and id<>$id";
		}
		return parent::selectQuery($sql, true);
	}
	public function webModulosSelectDAO(){
		$sql = "select id, modulo as titulo, alta from ".$this->_prefix."web_modulos where alta='S' order by titulo";
		return parent::selectQuery($sql, true);
	}
	/* textos */ 
	public function textosDAO($anyo, $alta){
		$sql = "select * from ".$this->_prefix."textos where muestra='$anyo'";
		if($alta){
			$sql .= " and alta='S'";
		}
		$sql .= " order by titulo";
		return parent::selectQuery($sql, true);
	}
	public function textoDAO($id){
		return parent::selectQuery("select t.*, gt.id_galeria from ".$this->_prefix."textos t LEFT JOIN ".$this->_prefix."galeria_texto gt ON t.id=gt.id_texto where t.id=$id", false, __FUNCTION__.$id);
	}
	

	public function textosPosiblesPaginaDAO($anyo){ 
		return parent::selectQuery("select * from ".$this->_prefix."textos where alta='S' and muestra='$anyo' and id not in (select id_texto from ".$this->_prefix."pagina_texto) order by titulo", true);
	}
	public function textosPaginaDAO($idPagina){ 
		return parent::selectQuery("select * from ".$this->_prefix."textos t, ".$this->_prefix."pagina_texto pt where t.id=pt.id_texto and pt.id_pagina=$idPagina and t.alta='S' order by titulo", true);
	}
	
	
    /* agradecimientos */
	public function agradecimientosDAO(){ 
		return parent::selectQuery("select id, donante as titulo, alta from ".$this->_prefix."donantes where id>0 order by donante", true); 
	}
	
	/* espacios de proyeccion */
	public function espaciosDAO(){ 
		return parent::selectQuery("select id, espacio as titulo, alta from ".$this->_prefix."espacios where id>0 order by lower(espacio)", true);
	}

	/* documentos descarga */
	public function documentosDAO($anyo){ 
		return parent::selectQuery("select id, archivo as titulo, alta from ".$this->_prefix."docs where id>0 and muestra='$anyo'", true);
	}

	/* imagen y galerias */
	public function imagenesDAO(){ 
		return parent::selectQuery("select i.id as id, concat(i.descripcion, ' / ', g.galeria) as titulo, i.alta as alta from ".$this->_prefix."imagenes i, ".$this->_prefix."galerias g where i.id_galeria=g.id order by g.id", true);
	}
	public function galeriasDAO(){ 
		return parent::selectQuery("select * from ".$this->_prefix."galerias where id > 0", true);
	}
	public function galeriaCarpetaDAO($carpeta){
		return parent::selectQuery("select * from ".$this->_prefix."galerias where galeria='$carpeta'", false);
	}
	
	public function imagenDAO($id){ 
		return parent::selectQuery("select i.*, g.galeria from ".$this->_prefix."imagenes i, ".$this->_prefix."galerias g where i.id_galeria=g.id and i.id=$id");
	}
	
	/* pelicula */
	public function peliculasDAO($anyo){ 
		return parent::selectQuery("select id, titulo, alta from ".$this->_prefix."peliculas where muestra ='$anyo' and id not in (select id from ".$this->_prefix."convocatoria) order by titulo", true);
	}
	public function proyeccionesPeliculaDAO($anyo){
		return parent::selectQuery("select pr.id, concat(DATE_FORMAT(pr.dia, '%d/%c/%Y'),' ',TIME_FORMAT(pr.hora,'%H:%i'),' ', e.espacio) as titulo from ".$this->_prefix."espacios e, ".$this->_prefix."proyecciones pr where e.id=pr.id_espacio and pr.anyo='$anyo' and pr.alta='S' order by pr.dia, pr.hora", true);
	}
	public function proyeccionPDFDAO($id){
		return parent::selectQuery("select DATE_FORMAT(pr.dia, '%d-%c-%Y') as dia, TIME_FORMAT(pr.hora,'%H:%i') as hora, e.espacio from ".$this->_prefix."espacios e, ".$this->_prefix."proyecciones pr where e.id=pr.id_espacio and pr.id=$id");
	}
	public function agradecimientosPeliculaDAO(){ 
		return parent::selectQuery("select id, donante as titulo from ".$this->_prefix."donantes where alta='S' and id>0 order by donante", true);
	}
	/*
	public function fichaPeliculaDAO($id){
		return parent::selectQuery("SELECT p.*, l.nombre AS nombre_licencia, a.autor as nombre_contacto, a.email, a.telefono,
							i.id as id_imagen, i.imagen AS cartel, IF(i.imagen is null, 'upload-img', '') as class_upload, pp.id_proyeccion, dp.id_donante
							FROM ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula,
							".$this->_prefix."peliculas p1 LEFT JOIN ".$this->_prefix."autores a ON pe.id = a.id_pelicula, 
							".$this->_prefix."peliculas p2 LEFT JOIN ".$this->_prefix."proyeccion_pelicula pp  ON p2.id = pp.id_pelicula,
							".$this->_prefix."peliculas p3 LEFT JOIN ".$this->_prefix."donante_pelicula dp  ON p3.id = dp.id_pelicula,
							".$this->_prefix."licencias l
							WHERE p.id = p1.id AND p.id=p2.id AND p.id=p3.id
							AND p.licencia = l.id 
							AND p.id=$id");
	}*/
	
	public function fichaPeliculaDAO($id){
		return parent::selectQuery("SELECT p.*, l.nombre AS nombre_licencia,
							i.id as id_imagen, i.imagen AS cartel, IF(i.imagen is null, 'upload-img', '') as class_upload, 
							pp.id_proyeccion, dp.id_donante
							FROM ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula,
							".$this->_prefix."peliculas p1 LEFT JOIN ".$this->_prefix."proyeccion_pelicula pp  ON p1.id = pp.id_pelicula,
							".$this->_prefix."peliculas p2 LEFT JOIN ".$this->_prefix."donante_pelicula dp  ON p2.id = dp.id_pelicula,
							".$this->_prefix."licencias l
				WHERE p.id = p1.id AND p.id=p2.id
				AND p.id_licencia = l.id
				AND p.id=$id");
	}
	
	public function fichaPeliculaConvocatoriaDAO($id){
		return parent::selectQuery("SELECT p.*, l.nombre AS nombre_licencia, a.autor as nombre_contacto, a.email, a.telefono,
							i.id as id_imagen, i.imagen AS cartel, IF(i.imagen is null, 'upload-img', '') as class_upload,
							c.recursos, c.comentarios, c.coste, p.material_propio
							FROM ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula,
							".$this->_prefix."peliculas p1 LEFT JOIN ".$this->_prefix."autores a ON p1.id = a.id_pelicula,  
							".$this->_prefix."peliculas p2 LEFT JOIN ".$this->_prefix."proyeccion_pelicula pp  ON p2.id = pp.id_pelicula,
							".$this->_prefix."convocatoria c,
							".$this->_prefix."licencias l
							WHERE p.id = p1.id AND p.id=p2.id  
							AND p.id= c.id 
							AND p.id_licencia = l.id 
							AND p.id=$id");
	}
	
	public function fichasPeliculaProyeccionDAO($idProyeccion){
		return parent::selectQuery("SELECT p.*, l.nombre AS nombre_licencia,
							i.id as id_imagen, i.imagen AS cartel, IF(i.imagen is null, 'upload-img', '') as class_upload,
							pp.id_proyeccion, dp.id_donante
							FROM ".$this->_prefix."peliculas p LEFT JOIN ".$this->_prefix."imagenes_pelicula i ON p.id = i.id_pelicula,
							".$this->_prefix."peliculas p1 LEFT JOIN ".$this->_prefix."proyeccion_pelicula pp  ON p2.id = pp.id_pelicula,
							".$this->_prefix."peliculas p2 LEFT JOIN ".$this->_prefix."donante_pelicula dp  ON p3.id = dp.id_pelicula,
							".$this->_prefix."licencias l
				WHERE p.id = p1.id AND p.id=p2.id
				AND p.id_licencia = l.id
				AND pp.id_proyeccion=$idProyeccion", true);
	}
	public function licenciasDAO(){
		return parent::selectQuery("select id, nombre as titulo, alta from ".$this->_prefix."licencias where alta='S'", true);
	}
	public function peliculasConvocatoriaDAO($anyo){ 
		return parent::selectQuery("select c.id, p.titulo, c.alta from ".$this->_prefix."peliculas p, ".$this->_prefix."convocatoria c where p.id=c.id_pelicula and p.muestra ='$anyo' order by id", true); 
	}	
	public function imgPeliculaDAO($idPelicula){ 
		return parent::selectQuery("select * from ".$this->_prefix."imagenes_pelicula where id_pelicula=$idPelicula");
	}
	
	/* proyecciones */
	public function proyeccionesDAO($anyo){
		return parent::selectQuery("select pr.id as id, CONCAT(DATE_FORMAT(pr.dia, '%d/%c'),' ',TIME_FORMAT(pr.hora,'%H:%i'),' ',e.espacio) as titulo, pr.alta from ".$this->_prefix."espacios e, ".$this->_prefix."proyecciones pr where e.id=pr.id_espacio and pr.anyo='$anyo' and pr.id > 0 order by pr.dia, pr.hora", true);
	}
	public function espaciosProyeccionDAO(){ 
		return parent::selectQuery("select id, espacio as titulo from ".$this->_prefix."espacios where id > 0 and alta='S' order by espacio", true);
	}	
	public function proyeccionDAO($id){	
		return parent::selectQuery("select pr.id, pr.id_espacio, e.espacio, pr.dia, TIME_FORMAT(pr.hora,'%H:%i') as hora, pr.titulo, pr.descripcion from ".$this->_prefix."espacios e, ".$this->_prefix."proyecciones pr where e.id=pr.id_espacio and pr.id=$id");
	}
	
	/* idiomas */
	public function langsDAO(){
		return parent::selectQuery("select id, nombre as titulo, alta from ".$this->_prefix."langs", true);
	}
	public function langDAO($lang){
		return parent::selectQuery("select * from ".$this->_prefix."langs where lang='$lang'", true);
	}
	public function langsEdicionDAO($anyo){
		return parent::selectQuery("select l.lang id, l.nombre titulo from ".$this->_prefix."langs l, ".$this->_prefix."lang_edicion e where l.lang=e.lang and id_edicion='$anyo'", true);
	}
	public function langsDisponiblesEdicionDAO($anyo){
		return parent::selectQuery("select l.lang id, l.nombre titulo from ".$this->_prefix."langs l where l.lang not in (select lang from ".$this->_prefix."lang_edicion where id_edicion='$anyo')", true);
	}
    
    public function deleteTextoPagina($idPagina){
    	return parent::deleteQuery("delete from ".$this->_prefix."pagina_texto where id_pagina=$idPagina");
	}
    public function deleteMenu($idPagina){
    	return parent::deleteQuery("delete from ".$this->_prefix."menu where id_pagina=$idPagina");
    }
    public function deleteLangEdicion($edicion){
    	return parent::deleteQuery("delete from ".$this->_prefix."lang_edicion where id_edicion='$edicion'");
	}
    public function deleteImagenPelicula($id){
    	return parent::deleteQuery("delete from ".$this->_prefix."imagenes_pelicula where id_pelicula=$id");
	}
	public function deletePerfilesUsuario($idUsuario){
		return parent::deleteQuery("delete from ".$this->_prefix."usuario_perfil where id_usuario=$idUsuario");
	}
	public function deleteGaleriaTexto($idTexto){
		return parent::deleteQuery("delete from ".$this->_prefix."galeria_texto where id_texto=$idTexto");
	}
	public function deleteDonantePelicula($idPelicula){
		return parent::deleteQuery("delete from ".$this->_prefix."donante_pelicula where id_pelicula=$idPelicula");
	}
	public function deleteProyeccionPelicula($idPelicula){
		return parent::deleteQuery("delete from ".$this->_prefix."proyeccion_pelicula where id_pelicula=$idPelicula");
	}
	
	
}
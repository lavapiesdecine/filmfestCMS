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
		$sql = sprintf("SELECT u.id, pass, u.usuario, u.email, u.logo FROM usuarios u WHERE u.usuario='%s'", parent::sanitize($nombre));
		return parent::selectQuery($sql);
	}
	  
    /* menu */
    public function menuDAO($nivelAcceso){
    	return parent::selectQuery("SELECT m.id, m.modulo, mp.id_perfil, m.modulo_padre, m.modulo as url, 'N' as portada FROM modulos m, modulo_perfil mp WHERE m.id=mp.id_modulo and mp.id_perfil=$nivelAcceso and modulo_padre=0 and alta='S'", true);
	}
	
	public function submenuDAO($moduloMenu){
		return parent::selectQuery("SELECT m.id, m.modulo, mp.id_perfil, m.modulo_padre, m.modulo as url, 'N' as portada FROM modulos m, modulo_perfil mp WHERE m.id=mp.id_modulo and m.modulo_padre=$moduloMenu and alta='S'", true);
	}
	 
    /*usuario*/
	public function usuariosDAO(){
		return parent::selectQuery("select id, usuario as titulo, alta from usuarios order by id", true);
	}
	public function nivelesAccesoDAO(){
		return parent::selectQuery("select id, profile as titulo, alta from perfiles order by id", true);
	}
	public function nivelesNavegacionDAO(){
		return parent::selectQuery("select id, profile as titulo from perfiles where alta='S' order by id", true);
	}
	
	public function nivelesNavegacionUsuarioDAO($usuario){
		return parent::selectQuery("select pa.id, pa.profile as titulo from perfiles pa, usuario_perfil up where pa.id=up.id_perfil and up.id_usuario=$usuario order by pa.id", true);
	}
	public function nivelesNavegacionPosiblesDAO($niveles){
		$sql = "select id, profile as titulo from perfiles where alta='S' ";
		if(!empty($niveles)){
			$sql .= "and id not in ($niveles) ";
		}
		$sql .= "order by id";
		return parent::selectQuery($sql, true);
	}
	
	public function perfilesAccesoDAO($usuario){
		return parent::selectQuery("select pa.* from perfiles pa, usuario_perfil up where pa.id = up.id_perfil and up.id_usuario = $usuario order by pa.id", true);
	}
	
	public function modulosPosiblesDAO(){
		return parent::selectQuery("select id, modulo titulo from modulos where id not in (select id from modulo_perfil)", true);
	}
	public function modulosPerfilDAO($perfil){
		return parent::selectQuery("select m.id, m.modulo titulo from modulos m, modulo_perfil mp where m.id = mp.id_modulo and mp.id_perfil = $perfil", true);
	}
	
	/* modulos */
	public function adminModulosDAO(){ 
		return parent::selectQuery("select am.id, am.modulo as titulo, am.alta from modulos am", true);
	}
	public function adminModuloNombreDAO($modulo, $perfiles){
		return parent::selectQuery("select m.*, mp.id_perfil from modulos m, modulo_perfil mp where m.id=mp.id_modulo and m.modulo='$modulo' and mp.id_perfil in ($perfiles)");
	}
	/*
  	public function adminModuloDefaultDAO($id_nivelacceso){
		return parent::selectQuery("SELECT am.* FROM modulos am, perfil_acceso pa WHERE am.id = pa.id_modulodefault and pa.nivel=$id_nivelacceso");
    }*/
	public function modulosNivelAccesoDAO($nivelAcceso){ 
		$sql = "select id, modulo from modulos where alta='S'";
		if ($nivelAcceso>0){
			$sql = "select m.id, m.modulo from modulos m, modulo_perfil mp where m.id=mp.id_modulo and m.alta='S' and mp.id_perfil=$nivelAcceso";
		}
		return parent::selectQuery($sql, true);
	}
	/*
	public function numAdminModulo($modulo){ 
		return parent::selectCount("select * from admin_modulos where modulo='$modulo'");
	}
	*/
	/**
	 * TODO check padre e hijo mismo perfil
	 */
	public function modulosPadreDAO($id){ 
		$sql = "select id, modulo as titulo from modulos where alta='S'";
		if(!empty($id)){
			$sql .= " and id<>$id";
		}
		return parent::selectQuery($sql, true);
	}
	
	//listado
	public function webModulosDAO(){
		$sql = "select id, modulo titulo, alta from web_modulos order by titulo";
		return parent::selectQuery($sql, true);
	}
	
	/* edicion muestras */
	public function edicionDAO($id){
		return parent::selectQuery("select e.id, e.nombre, e.descripcion, e.cartel, e.fecha_inicio, e.fecha_fin from ediciones e where e.id='$id'", false, __FUNCTION__.$id);
	}
	public function edicionesDAO(){
		return parent::selectQuery("select id, nombre as titulo, alta, cartel from ediciones order by id", true);
	}
	
	/* convocatorias */
	public function convocatoriaDAO($anyo){
		return parent::selectQuery("select e.id, e.nombre, DATE_FORMAT(e.fecha_inicio, '%d/%c') as inicio, DATE_FORMAT(e.fecha_fin, '%d/%c') as fin, c.url, c.cartel, c.descripcion from convocatorias c, ediciones e where e.id=c.id and c.id='$anyo' order by e.id desc");
	}
	public function convocatoriasDAO(){
		return parent::selectQuery("select e.id, e.nombre as titulo, c.alta from convocatorias c, ediciones e where e.id=c.id", true);
	}
	
	
	/**
	 *  paginas web 
	 */
	//listado
	public function paginasDAO($anyo){ 
		return parent::selectQuery("select id, url as titulo, alta from pagina where muestra='$anyo' order by alta desc, url", true);
	}
	public function urlPaginasDAO($anyo){ 
		return parent::selectQuery("select url as id, url as titulo from pagina where muestra='$anyo' order by url", true);
	}
	public function webMenuDAO($id){ 
		return parent::selectQuery("select * from menu where id_pagina=$id");
	}
	public function paginasPadreDAO($id, $anyo){
		$sql = "select id, url as titulo from pagina where muestra='$anyo' and alta='S'";
	  	if(!empty($id)){
			$sql .= " and id<>$id";
		}
		return parent::selectQuery($sql, true);
	}
	public function webModulosSelectDAO(){
		$sql = "select id, modulo as titulo, alta from web_modulos where alta='S' order by titulo";
		return parent::selectQuery($sql, true);
	}
	/* textos */ 
	public function textosDAO($anyo, $alta){
		$sql = "select * from textos where muestra='$anyo'";
		if($alta){
			$sql .= " and alta='S'";
		}
		$sql .= " order by titulo";
		return parent::selectQuery($sql, true);
	}

	public function textosPosiblesPaginaDAO($anyo){ 
		return parent::selectQuery("select * from textos where alta='S' and muestra='$anyo' and id not in (select id_texto from pagina_texto) order by titulo", true);
	}
	public function textosPaginaDAO($idPagina){ 
		return parent::selectQuery("select * from textos t, pagina_texto pt where t.id=pt.id_texto and pt.id_pagina=$idPagina and t.alta='S' order by titulo", true);
	}
	
	
    /* agradecimientos */
	public function agradecimientosDAO(){ 
		return parent::selectQuery("select id, donante as titulo, alta from donantes where id>0 order by donante", true); 
	}
	
	/* espacios de proyeccion */
	public function espaciosDAO(){ 
		return parent::selectQuery("select id, espacio as titulo, alta from espacios where id>0 order by lower(espacio)", true);
	}

	/* documentos descarga */
	public function documentosDAO($anyo){ 
		return parent::selectQuery("select id, archivo as titulo, alta from docs where id>0 and muestra='$anyo'", true);
	}

	/* imagen y galerias */
	public function imagenesDAO(){ 
		return parent::selectQuery("select i.id as id, concat(i.descripcion, ' / ', g.galeria) as titulo, i.alta as alta from imagenes i, galerias g where i.id_galeria=g.id order by g.id", true);
	}
	public function galeriasDAO(){ 
		return parent::selectQuery("select * from galerias where id > 0", true);
	}
	public function galeriaCarpetaDAO($carpeta){
		return parent::selectQuery("select * from galerias where galeria='$carpeta'", false);
	}
	
	public function imagenDAO($id){ 
		return parent::selectQuery("select i.*, g.galeria from imagenes i, galerias g where i.id_galeria=g.id and i.id=$id");
	}
	
	/* pelicula */
	public function peliculasDAO($anyo){ 
		return parent::selectQuery("select id, titulo, alta from peliculas where muestra ='$anyo' and id not in (select id from convocatoria) order by titulo", true);
	}
	public function proyeccionesPeliculaDAO($anyo){
		return parent::selectQuery("select pr.id, concat(DATE_FORMAT(pr.dia, '%d/%c/%Y'),' ',TIME_FORMAT(pr.hora,'%H:%i'),' ', e.espacio) as titulo from espacios e, proyecciones pr where e.id=pr.id_espacio and pr.anyo='$anyo' and pr.alta='S' order by pr.dia, pr.hora", true);
	}
	public function proyeccionPDFDAO($id){
		return parent::selectQuery("select DATE_FORMAT(pr.dia, '%d-%c-%Y') as dia, TIME_FORMAT(pr.hora,'%H:%i') as hora, e.espacio from espacios e, proyecciones pr where e.id=pr.id_espacio and pr.id=$id");
	}
	public function agradecimientosPeliculaDAO(){ 
		return parent::selectQuery("select id, donante as titulo from donantes where alta='S' and id>0 order by donante", true);
	}
	public function fichaPeliculaDAO($id){
		return parent::selectQuery("SELECT p.*, l.id as id_licencia, l.nombre AS nombre_licencia, a.autor as nombre_contacto, a.email, a.telefono,
							i.id as id_imagen, i.imagen AS cartel, IF(i.imagen is null, 'upload-img', '') as class_upload, p.muestra, p.utf8
							FROM peliculas p LEFT JOIN imagenes_pelicula i ON p.id = i.id_pelicula,
							peliculas pe LEFT JOIN autores a ON pe.id = a.id_pelicula, licencias l
							WHERE p.id = pe.id 
							AND p.licencia = l.id 
							AND p.id=$id");
	}
	public function fichaPeliculaConvocatoriaDAO($id){
		return parent::selectQuery("SELECT p.*, l.nombre AS nombre_licencia, a.autor as nombre_contacto, a.email, a.telefono,
							i.id as id_imagen, i.imagen AS cartel, IF(i.imagen is null, 'upload-img', '') as class_upload,
							c.recursos, c.comentarios, c.coste, p.material_propio
							FROM peliculas p LEFT JOIN imagenes_pelicula i ON p.id = i.id_pelicula,
							peliculas pe LEFT JOIN autores a ON pe.id = a.id_pelicula, convocatoria c, licencias l
							WHERE p.id = pe.id 
							AND p.id= c.id
							AND p.licencia = l.id 
							AND p.id=$id");
	}
	
	public function fichasPeliculaProyeccionDAO($idProyeccion){
		return parent::selectQuery("SELECT p.*, l.nombre AS nombre_licencia, a.autor as nombre_contacto, a.email, a.telefono,
							i.id as id_imagen, i.imagen AS cartel, IF(i.imagen is null, 'upload-img', '') as class_upload, p.muestra, p.utf8
							FROM peliculas p LEFT JOIN imagenes_pelicula i ON p.id = i.id_pelicula,
							peliculas pe LEFT JOIN autores a ON pe.id = a.id_pelicula, licencias l
							WHERE p.id = pe.id 
							AND p.licencia = l.id 
							AND p.id_proyeccion=$idProyeccion", true);
	}
	public function licenciasDAO(){
		return parent::selectQuery("select id, nombre as titulo, alta from licencias where alta='S'", true);
	}
	public function peliculasConvocatoriaDAO($anyo){ 
		return parent::selectQuery("select c.id, p.titulo, c.alta from peliculas p, convocatoria c where p.id=c.id_pelicula and p.muestra ='$anyo' order by id", true); 
	}	
	public function imgPeliculaDAO($idPelicula){ 
		return parent::selectQuery("select * from imagenes_pelicula where id_pelicula=$idPelicula");
	}
	
	/* proyecciones */
	public function proyeccionesDAO($anyo){
		return parent::selectQuery("select pr.id as id, CONCAT(DATE_FORMAT(pr.dia, '%d/%c'),' ',TIME_FORMAT(pr.hora,'%H:%i'),' ',e.espacio) as titulo, pr.alta from espacios e, proyecciones pr where e.id=pr.id_espacio and pr.anyo='$anyo' and pr.id > 0 order by pr.dia, pr.hora", true);
	}
	public function espaciosProyeccionDAO(){ 
		return parent::selectQuery("select id, espacio as titulo from espacios where id > 0 and alta='S' order by espacio", true);
	}	
	public function proyeccionDAO($id){	
		return parent::selectQuery("select pr.id, pr.id_espacio, e.espacio, pr.dia, TIME_FORMAT(pr.hora,'%H:%i') as hora, pr.titulo, pr.descripcion from espacios e, proyecciones pr where e.id=pr.id_espacio and pr.id=$id");
	}
	
	/* idiomas */
	public function langsDAO(){
		return parent::selectQuery("select id, nombre as titulo, alta from langs", true);
	}
	public function langDAO($lang){
		return parent::selectQuery("select * from langs where lang='$lang'", true);
	}
	public function langsEdicionDAO($anyo){
		return parent::selectQuery("select l.lang id, l.nombre titulo from langs l, lang_edicion e where l.lang=e.lang and id_edicion='$anyo'", true);
	}
	public function langsDisponiblesEdicionDAO($anyo){
		return parent::selectQuery("select l.lang id, l.nombre titulo from langs l where l.lang not in (select lang from lang_edicion where id_edicion='$anyo')", true);
	}
    
    public function deleteTextoPagina($idPagina){
    	return parent::deleteQuery("delete from pagina_texto where id_pagina=$idPagina");
	}
    public function deleteMenu($idPagina){
    	return parent::deleteQuery("delete from menu where id_pagina=$idPagina");
    }
    public function deleteLangEdicion($edicion){
    	return parent::deleteQuery("delete from lang_edicion where id_edicion='$edicion'");
	}
    public function deleteImagenPelicula($id){
    	return parent::deleteQuery("delete from imagenes_pelicula where id_pelicula=$id");
	}
	public function deletePerfilesUsuario($idUsuario){
		return parent::deleteQuery("delete from usuario_perfil where id_usuario=$idUsuario");
	}
	
}
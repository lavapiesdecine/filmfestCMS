<?php

	namespace core\util;

	class UtilPagina {
	
	
		/**
		 * metodo para paginar
		 * @param $paginas  
		 * @param $urlActual 
		 * @param $url $baseUrl
		 * @param $idControllerMenu
		 * @return string
		*/
		public static function menu($paginas, $urlActual, $baseUrl, $idControllerMenu){
			$html = "";
			if(isset($paginas) && count($paginas)>0){
				$html .="<ul>";
				foreach ($paginas as $pagina) {
					$urlMenu = $baseUrl;
					if ($pagina->portada == 'N'){
						$urlMenu .= $pagina->url;
					} 
					
					$class =  ($pagina->url == $urlActual || 
							  ($pagina->modulo_padre == 0  && $idControllerMenu == $pagina->id))? 'activa' : 'spacing';
								  
					if ($pagina->url == $urlActual){
						$html .="<li class='$class'>"._($pagina->url.".menu")."</li>";
					}
					else{
						$html .="<li class='$class'><a href='$urlMenu' title='"._($pagina->url.".description")."'>"._($pagina->url.".menu")."</a></li>";
					}
				}
				$html .="</ul>";
			}	
			return  $html;
		}
		
		
		
		public static function paginacion($pag, $total, $url){
			$html = "";
			$NUMERO_ENLACES = 9;
			
			if ($pag > 1){			 
				 $html .= "<a id='paginacionAnterior' rel='nofollow' href='$url/".($pag-1)."'>&laquo; Anterior</a>  "; 
			}
				
			$intervalo = ceil($NUMERO_ENLACES/2) - 1;
							
			$desde = $pag - $intervalo;
			$hasta = $pag + $intervalo;
							
			if($desde < 1){
				$hasta -= ($desde - 1);
				$desde = 1;
			}
				
			if($hasta > $total){
				$desde -= ($hasta - $total);
				$hasta = $total;
				if($desde < 1){
					$desde = 1;
				}
			}
						
			for ($i = $desde; $i<=$hasta; $i++){
				if ($i != $pag) {
					 $html .= " <a href='$url/$i' rel='nofollow' >$i</a>";
				} else {
					 $html .= " <span id='selec'>$pag</span>";
				}
			}				
							
			if ($pag < $total){
				 $html .= "<a id='paginacionSiguiente' rel='nofollow' href='$url/".($pag+1)."'>Siguiente &raquo;</a>"; 
			}
							
			return $html;			
				
		}
			
		/**
		 * funcion para pintar un calendario. basado en: http://blog.outbook.es/desarrollo-web/xhtml/php-funcion-para-crear-calendario-accesible-y-semantico
		 * @param $anyo
		 * @param $fechaInicio
		 * @param $fechaFin
		 * @return string
		 */
		public static function calendario($anyo, $fechaInicio, $fechaFin, $url){
			
			$CABECERA_DIAS = array('L','M','X','J','V','S','D');
			
			//TODO: pasarlo a constants.php
			$diaInicial = gmmktime(0,0,0,6,20,$anyo);
			$diaFin = gmmktime(0,0,0,7,5,$anyo);
			$diasAdicionalesInicio = date("N", $diaInicial)-1;
			$diasAdicionalesFin = 7-date("N", $diaFin);
			$inicioCalendario = strtotime("$anyo-06-20") - ($diasAdicionalesInicio*86400);
			$finCalendario = strtotime("$anyo-07-05") + ($diasAdicionalesFin*86400);
			
			$html = "<table>";
			
			//cabecera
			$html .= '<thead><tr>';
			for ($d=0; $d < 7; $d++) {
				$html .= '<th>'.$CABECERA_DIAS[$d].'</th>';
			}
			$html .= '</tr></thead>';
			$html .= '<tbody>';
			
			$x = 0;
			$ds = 0;
			for ($i = $inicioCalendario; $i <= $finCalendario; $i+=86400) {
				if($x%7==0 and $x!=0) {
					$html .= '</tr>';
				}
				if($x%7==0) {
					$html .= '<tr>';
					$ds=1;
				}
				
				$diaExtendido = date("dmY", $i);
				$dia = date("j", $i);
				
				$hoy = date("dmY") == date("dmY", $i) ? "hoy" : "";
				
				if ($i >= $fechaInicio && $i <= $fechaFin) {
					$html .= "<td class='celda $hoy diaProyeccion'><a href='$url/$diaExtendido'>$dia</a></td>";
				}
				else{
					$html .= "<td class='celda $hoy'>$dia</td>";
				}
				$x++;
				$ds++;
			}
			$html .= "</tbody></table>";
			return $html;
		}
		
		public static function getEmbedVideo($video){
			$html ="";
			if (!empty($video)){
				$regex = "/http:\/\/(:?www.)?(\w*)/";
				preg_match($regex, $video, $match);
				$stream = $match[2];
					
				switch ($stream){
					case "vimeo":
						$codigo = explode("http://vimeo.com/", $video);
						$html = "<iframe class='video' src='http://player.vimeo.com/video/$codigo[1]' ></iframe>";
						break;
					case "youtube":
						$codigo = explode("http://www.youtube.com/v/", $video);
						$html = "<iframe class='video' src='http://www.youtube.com/embed/$codigo[1]' ></iframe>";
						break;
					case "blip":
						$html = "<iframe class='video' src='$video' ></iframe>";
						break;
					case "dailymotion":
						$codigo = explode("http://www.dailymotion.com/video/", $video);
						$html = "<iframe class='video' src='http://www.dailymotion.com/embed/video/$codigo[1]' ></iframe>";
						break;
				}
			}
			return $html;
		}
				
	
		/**
		 * pintar options para una select
		 * @param $array
		 * @param $idSelected
		 * @param $default
		 * @return string
		 */
		public static function getOptions($array, $idSelected, $default=true){
			$html = "";
			if($default){
				$html .= "<option value=0>"._("form.select")."</option>";
			}
			if(!empty($array)){
				foreach ($array as $item) {
					$html .= "<option value='".$item->id."'";
					if($item->id==$idSelected){ 
						$html .= " selected='selected' ";
					}
					$html .= "> ".$item->titulo." </option>";
				}
			}
			return $html;
		}
		
		
		/**
		 * devuelve los metas para generar un twitter card
		 * @param $info
		 */
		public static function getTwitterCard($info){
			
			$account = TWITTER_ACCOUNT;
			if(!empty($account)){
				$card = new \core\lib\Twitter_Card($info->type);
				$card->setTitle($info->title);
				$card->setDescription($info->description);
				$card->setURL($info->url);
				if(!empty($info->image)){
					$card->setImage($info->image, 480, 360);
				}
				if(!empty($info->video)){
					$card->setVideo(str_replace("http://", "https://", $info->video), 435, 251);
				}
				$card->setSiteAccount(TWITTER_ACCOUNT);
				$card->setCreatorAccount(TWITTER_ACCOUNT);
				return $card->asHTML();
			} else {
				return '';
			}
		}
		
	}
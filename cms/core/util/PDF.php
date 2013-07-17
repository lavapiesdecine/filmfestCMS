<?php
	namespace core\util;
	
	class PDF extends \core\lib\PDF_HTML{
		
		private $_anchuraImagen = 50;
		private $_anchuraPagina = 210;
		private $_margin = 10;
		private $_alturaInicial = 50; 
		private $_edicion;
		private $_fraseFooter = "un mundo muchos barrios un barrio muchos mundos";
		private $_imgCabecera = "cabecera.jpg";
		private $_fontFooter = 23;
		private $_fontTitle = 25;
		private $_fontSubTitle = 15;
		private $_fontText = 12;
		private $_fontTextMini = 10;
		private $_fontFamily = "Arial";
		
		public function __construct() {
			parent::FPDF();
		}
		
		public function Header(){
			$urlImagen = URL_SKINS. $this->_edicion->id . "/" . IMG . "/" . $this->_imgCabecera;
			$pathImagen = SKINS_PATH . $this->_edicion->id . DS . IMG . DS . $this->_imgCabecera;
			$img = new \core\classes\Imagen($pathImagen);
			$w = $img->getWidth();
			$h = $img->getHeight();
			$ratio = $w / $this->_anchuraPagina;
			$this->Image($urlImagen, 0, 0, $this->_anchuraPagina, round($h/$ratio));
			$this->Ln(20);
		}
	
		public function Footer(){
		 	$this->SetFont($this->_fontFamily,'',$this->_fontFooter);
		 	$this->SetY(-10);
		 	$this->Cell(90,10,$this->_fraseFooter,0,0,'C');
		}
		
		/**
		 * @param $pelicula
		 * @param $utf8
		 */
		public function fichaPelicula($pelicula){
			$this->SetFont($this->_fontFamily,'',$this->_fontTitle);
			$this->Cell(0,20,utf8_decode($pelicula->titulo),0,2,'L');
			
			if(!empty($pelicula->cartel)){
				$this->SetCol(0);
				$urlImagen = URL_IMG."peliculas/".$pelicula->muestra."/".$pelicula->cartel;
				$pathImagen = IMG_PATH . "peliculas". DS . $pelicula->muestra. DS . $pelicula->cartel;
				$img = new \core\classes\Imagen($pathImagen);
				$w = $img->getWidth();
				$h = $img->getHeight();
				$ratio = $w / $this->_anchuraImagen;
				$this->Image($urlImagen, $this->_margin, $this->_alturaInicial, $this->_anchuraImagen, round($h/$ratio));
				$this->SetCol(1);
			}
			$this->SetFont($this->_fontFamily,'',$this->_fontTextMini);
			$this->Cell(0,0,$this->WriteHTML($pelicula->ficha_tecnica, $pelicula->utf8),0,2,'L');
			$this->Ln(10);
			$this->SetFont($this->_fontFamily,'',$this->_fontText);
			$this->Cell(0,0,$this->WriteHTML($pelicula->sinopsis, $pelicula->utf8),0,2,'L');
		}
		
		public function proyeccion($proyeccion, $peliculas){
			$this->SetFont($this->_fontFamily,'',$this->_fontTitle);
			$this->Cell(0,20,utf8_decode($proyeccion),0,0,'C',0,0);
			$this->Ln(20);
			
			for ($i = 0; $i < count($peliculas); $i++) {
				if(count($peliculas)==1){
					$this->fichaPelicula($peliculas[0]);
				} else {
					if ($i==0){ 
						$posicion = $this->_alturaInicial;
					}	
					$posicion = $this->fichaProyeccion($peliculas[$i], $posicion);
				}
			}
		}
		
		
		public function fichaProyeccion($pelicula, $posicion){
			$alturaImagen = 0;			
			if(!empty($pelicula->cartel)){
				$this->SetCol(0);
				$urlImagen = URL_IMG."peliculas/".$pelicula->muestra."/".$pelicula->cartel;
				$pathImagen = IMG_PATH . "peliculas". DS . $pelicula->muestra. DS . $pelicula->cartel;
				$img = new \core\classes\Imagen($pathImagen);
				$w = $img->getWidth();
				$h = $img->getHeight();
				$ratio = $w / $this->_anchuraImagen;
				$this->Image($urlImagen, $this->_margin, $posicion, $this->_anchuraImagen, round($h/$ratio));
				$this->SetCol(1);
			}
			$this->SetFont($this->_fontFamily, '', $this->_fontSubTitle);
			$this->Cell(0,10,utf8_decode($pelicula->titulo),0,2,'L');
			$this->SetFont($this->_fontFamily,'', $this->_fontTextMini);
			$this->Cell(0,0,$this->WriteHTML($pelicula->ficha_tecnica, $pelicula->utf8),0,2,'L');
			$this->Ln(10);
			$this->SetFont($this->_fontFamily,'',$this->_fontText);
			$this->Cell(0,0,$this->WriteHTML($pelicula->sinopsis, $pelicula->utf8),0,2,'L');
			$posicion = (($posicion + $alturaImagen) > $this->GetY() ? $posicion + $alturaImagen : $this->GetY())+10;
			$this->Ln($posicion-$this->GetY());
			return $posicion;
		}
		
		private function SetCol($col){
			$this->col=$col;
			$x = $this->_margin + $col * $this->_anchuraImagen;
			$this->SetLeftMargin($x);
			$this->SetX($x);
		}
		
		public function setEdicion($edicion){
			$this->_edicion = $edicion;
		}
	}
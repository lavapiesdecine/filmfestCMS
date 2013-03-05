<?php

	namespace core\lib;
	
	class PDF_HTML extends FPDF {
		
		var $B = 0;
		var $I = 0;
		var $U = 0;
		var $HREF = '';
		var $ALIGN = '';
		
		function WriteHTML($html, $utf8){
			
			$html =	$utf8=='S' ? utf8_decode($html) : html_entity_decode($html);
			
			//HTML parser
			$html = str_replace("\n",' ',$html);
			$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
			foreach($a as $i=>$e){
				if($i%2==0){
					//Text
					if($this->HREF)
						$this->PutLink($this->HREF,$e);
					elseif($this->ALIGN=='center')
						$this->Cell(0,5,$e,0,1,'C');
					else
						$this->Write(5,$e);
				}
				else{
					//Tag
					if($e[0]=='/'){
						$this->CloseTag(strtoupper(substr($e,1)));
					} else {
						//Extract properties
						$a2=explode(' ',$e);
						if(!empty($a2)){
							$tag = strtoupper(array_shift($a2));
							$prop = array();
							foreach($a2 as $v){
								if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3)){
									$prop[strtoupper($a3[1])]=$a3[2];
								}
							}
							$this->OpenTag($tag, $prop);
						}
					}
				}
			}
		}
	
		function OpenTag($tag, $prop){
			if($tag=='B' || $tag=='I' || $tag=='U'){
				$this->SetStyle($tag,true);
			}
			if($tag=='A'){
				$this->HREF=$prop['HREF'];
			}
			if($tag=='BR'){
				$this->Ln(5);
			}
			if($tag=='STRONG'){
				$this->SetStyle('B',true);
			}
		}
	
		function CloseTag($tag){
			//Closing tag
			if($tag=='B' || $tag=='I' || $tag=='U'){
				$this->SetStyle($tag,false);
			}
			if($tag=='A'){
				$this->HREF='';
			}
			if($tag=='BR'){
				$this->Ln(5);
			}
			if($tag=='P'){
				$this->Ln(5);
			}
			if($tag=='STRONG'){
				$this->SetStyle('B', false);
			}
		}
	
		function SetStyle($tag,$enable){
			//Modify style and select corresponding font
			$this->$tag+=($enable ? 1 : -1);
			$style='';
			foreach(array('B','I','U') as $s)
				if($this->$s>0)
					$style.=$s;
			$this->SetFont('',$style);
		}
	
		function PutLink($URL,$txt){
			//Put a hyperlink
			$this->SetTextColor(0,0,255);
			$this->SetStyle('U',true);
			$this->Write(5,$txt,$URL);
			$this->SetStyle('U',false);
			$this->SetTextColor(0);
		}
	}
$(document).ready(function() {
	/**
	 * formulario login
	*/ 
	$('#b_login').click(function(){
		if($('#form_login').validate()){
			$('#form_login').submit();
		}
	});
	
	if ($('#b_login').length){
		$(document).keypress(function(e) {
			if(e.which == 13) {
	            jQuery(this).blur();
	            jQuery('#b_login').focus().click();
	        }
		});
	}	
	
	
	/**
	 *  upload imagenes
	 */
	$('#imagen').change(function(){
		if($('#form_imagen').validate()){
			if ($('#nombre').length){
				$('#id_nombre').val($('#nombre').val());
			} else if ($('#titulo').length){
				$('#id_nombre').val($('#titulo').val());
			}
			$('#form_imagen').submit();
			$('#div-img').addClass('oculto');
			$('#frame_imagen').removeClass('oculto');
			$("#frame_imagen").contents().find("body").html("<img height='90px' src='" + urlImgAdmin + "loading.gif' />");
		}
	});
	
	
	/**
	 *  upload doc
	 */
	$('#doc').change(function(){
		if ($('#nombre').val()!=''){
			$("#frame_doc").contents().find("body").html("<img height='20px' src='" + urlImgAdmin + "loading.gif' />");
			$('#id_nombreDoc').val($('#nombre').val());
			$('#form_uploaddoc').submit();
			$('#div-doc').addClass('oculto');
			$('#feedback-doc').removeClass('oculto');
	 	}
		else {
			$('#msg').html("<strong>" + gcMsg_revisa + ":</strong> <br>" + gcMsg_docname);
			$('#msg').removeClass('oculto');
			$('#nombre').prev('label').addClass('error');
			$('#nombre').addClass('error');
		}
	});
	
	
	/**
	 *  usuario
	 */ 
	$('#b_usuario').click(function(){
		if($('#form_usuario').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input').val());
			}
			if($('#perfiles').text()!=''){
				var values = "";
				$("#perfiles>option").map(function() {
					values = values + "," + $(this).val();
				});
				$('#perfilesSelected').val(values);
			}
			$('#form_usuario').alta();
		}
	});
	$('#b_perfil').click(function(){
		if($('#form_perfil').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input').val());
			}
			$('#form_perfil').alta();
		}
	});
	
	/**
	 * modulos 
	 */
	$('#b_moduloweb').click(function(){
		if($('#form_moduloweb').validate()){
			$('#form_moduloweb').alta();
		}
	});
	$('#b_modulo').click(function(){
		if($('#form_modulo').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input').val());
			}
			$('#form_modulo').alta();
		}
	});
	
	/** 
	 * idiomas
	 */
	$('#b_lang').click(function(){
		if($('#form_lang').validate()){
			$('#form_lang').alta();
		}
	});
	
	/**
	 * paginas
	 */
	$('#b_pagina').click(function(){
		if($('#form_pagina').validate()){
			if($('#textos').text()!=''){
				var values = "";
				$("#textos>option").map(function() {
					values = values + "," + $(this).val();
				});
				$('#textosSelected').val(values);
			}
			$('#form_pagina').alta();
		}
	});
	
	if($('#id_modulo option:selected').text().trim()=='texto'){
		$('#div_texto').removeClass('oculto');
		$('#textos').addClass('selecteds');
	}
	
	$('#id_modulo').change(function(){
		if(this.options[this.selectedIndex].text=='texto'){
			$('#div_texto').removeClass('oculto');
			$('#textos').addClass('selecteds');
		} else {
			$('#div_texto').addClass('oculto');
			$('#textos').removeClass('selecteds');
		}
	});
	
	$('#id_skin').change(function(){
		$('#id_layout').html();
		$('#id_layout').load(urlApp + modulo + "/loadLayouts", {id_skin:$('#id_skin').val()});
	});
	
	
	/**
	 * textos
	 */
	$('#b_texto').click(function(){
		if($('#form_texto').validate()){
			$('#form_texto').alta();
		}
	});
	
	/**
	 * multimedia
	 */
	//carga valor de la carpeta  de la galeria
	$('#id_galeria').change(function(){		
		$.post(urlApp + modulo + "/load", {id_galeria:$('#id_galeria').val()}, function(data){
			$('#galeria').val(data);
			$('#id_galeria_img').val($('#id_galeria').val());
        });
	});
	
	$('#b_imagen').click(function(){
		if($('#frame_imagen').length>0){
			$('#file_imagen').val($('#frame_imagen').contents().find('input#nombre_imagen').val());
			$('#id').val($('#frame_imagen').contents().find('input#id').val());
		}
		if($('#form_img').validate()){
			$('#form_img').alta();
		}
	});
	$('#b_galeria').click(function(){
		if($('#form_galeria').validate()){
			$('#form_galeria').alta();
		}
	});
	
	$('#b_imagen').click(function(){
		if($('#frame_imagen').length>0){
			$('#file_imagen').val($('#frame_imagen').contents().find('input#nombre_imagen').val());
			$('#id').val($('#frame_imagen').contents().find('input#id').val());
		}
		if($('#form_img').validate()){
			$('#form_img').alta();
		}
	});
	$('#b_galeria').click(function(){
		if($('#form_galeria').validate()){
			$('#form_galeria').alta();
		}
	});
	
	/**
	 * documentos
	 */
	$('#b_doc').click(function(){
		if($('#frame_imagen').length>0){
			$('#file_imagen').val($('#frame_imagen').contents().find('input#nombre_imagen').val());
		}
		if($('#frame_doc').length>0){
			$('#file_doc').val($('#frame_doc').contents().find('input#nombre_doc').val());
			$('#id').val($('#frame_doc').contents().find('input#id').val());
		}
		if($('#form_doc').validate()){
			$('#form_doc').alta();
		}
	});
	
	/**
	 * agradecimientos
	 */
	$('#b_donante').click(function(){
		if($('#form_donante_alta').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input').val());
			}
			$('#form_donante_alta').alta();
		}
	});
	/**
	 * espacios
	 */
	$('#b_espacio').click(function(){
		if($('#form_espacio').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input').val());
			}
			$('#form_espacio').alta();
		}
	});
	
	/**
	 * proyecciones
	 */
	$('#b_proyeccion').click(function(){
		if($('#formProyeccion').validate()){
			$('#formProyeccion').alta();
		}
	});
	
	/**
	 * peliculas
	 * 
	 */
	$('#b_pelicula').click(function(){
		if($('#formPelicula').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input#nombre_imagen').val());
				$('#id').val($('#frame_imagen').contents().find('input#id').val());
			}
			$('#formPelicula').alta();
		}
	});
	
	/**
	 * ediciones
	 */
	$('#b_edicion').click(function(){
		if($('#form_edicion').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input#nombre_imagen').val());
			}
			if($('#langs').text()!=''){
				var values = "";
				$("#langs>option").map(function() {
					values = values + "," + $(this).val();
				});
				$('#langsSelected').val(values);
			}
			
			if ($.datepicker.parseDate('dd/mm/yy', $('#id_dia_inicio').val()) > $.datepicker.parseDate('dd/mm/yy', $('#id_dia_fin').val()) ){
				$('#msg').html("<strong>" + gcMsg_revisa + ":</strong> <br>" + gcMsg_date);
				$('#msg').removeClass('oculto');
				$('#id_dia_inicio').prev('label').addClass('error');
				$('#id_dia_fin').prev('label').addClass('error');
				$('#id_dia_inicio').addClass('error');
				$('#id_dia_fin').addClass('error');
			} else {
				$('#form_edicion').alta();
			}
		}
	});
	$('#id_dia_inicio').change(function(){
		$("#id_edicion").val($('#id_dia_inicio').val().split("/")[2]);
	});
	
	
	$('#b_convocatorias').click(function(){
		if($('#form_convocatorias').validate()){
			if($('#frame_imagen').contents().find('input').size()>0){
				$('#file_imagen').val($('#frame_imagen').contents().find('input#nombre_imagen').val());
			}
			$('#form_convocatorias').alta();
		}
	});
	
});

jQuery.fn.alta = function() {
	$('#msg').html("<h3 class='loading'>" + gcMsg_loading + "</h3>");
	$('#msg').removeClass('oculto');
	$.ajax({
        type: 'POST',
        url: urlApp + modulo + '/alta',
        data: $(this).serialize(),
        success: function(data) {
        	$('#msg').html(feedback(data));
            setTimeout("window.location='" + urlAdmin + modulo + "'", 2000);
        }
    })
}

function feedback(ok) {
	var msg = '<h3 class="ko">' + gcMsg_ko + '</h3>';
	if (ok){
		msg = '<h3 class="ok">' + gcMsg_ok + '</h3>';
	}
	return msg;
};


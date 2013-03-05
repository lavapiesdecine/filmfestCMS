	$(document).ready(function() {
		
		/*inscripcion*/
		$('.boton-inscripcion').click(function(){
			$('#msg').addClass('oculto');
			var accion = this.id.split('-')[1];
			if($('#form_'+accion).validate()){		
				$.ajax({
			        type: 'POST',
			        url: urlApp + 'inscripcion/' + accion,
			        data: $('#form_'+accion).serialize(),
			        success: function(data) {
			        	if($('#div-'+accion).next('div').children().length>0){
			        		$('#resumen-'+accion).html(data);
				            $('#resumen-'+accion).removeClass('oculto');
				            $('#div-'+accion).addClass('oculto');
							$('#div-'+accion).next('div').removeClass('oculto');
			        	} else {
			        		$('#div-'+accion).addClass('oculto');
			        		$('#resumen').addClass('oculto');
			        		$('#div-confirmacion').html(data);
			        		$('#div-confirmacion').removeClass('oculto');
			        	}
			        }
			    })
			}
		});
		
		
		$('#div-licencia img').click(function(){
			var idLicencia = this.id.split('_')[1];
			$('input#lic_'+idLicencia).attr('checked', true);
		});
		
		/**
		 *  upload imagenes
		 */
		$('input#imagen').change(function(){
			$('#form_imagen').addClass('oculto');
			$('#div-cargando').removeClass('oculto');
			$('#form_imagen').submit();
			$('#div-cargando').addClass('oculto');
			$("#frame_imagen").removeClass('oculto');
		});
		
		$('div#resumen div').click(function(){
			var accion = this.id.split('-')[1];
			$('div#formularios div').addClass('oculto');
			$('#div-'+accion).removeClass('oculto');
		});
		
	});
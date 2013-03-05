$(document).ready(function() {
	
	$(".fancybox").fancybox();
	$(".calendario").datepicker({ minDate: fechaInicio, maxDate: fechaFin });
	$(".date").datepicker();
	
	/**
	 * acciones columna derecha 
	 */
	$('aside#rightmenu span.titulo, aside#rightmenu a.edit').click(function(){
		var item = this.id.split('-');
		window.location = urlApp +  modulo + '/' +  item[1];
	});
	//eliminar
	$('aside#rightmenu a.delete').click(function(){
		var item = this.id.split('-');
		if (confirm(gcMsg_confirmdelete + ' "' + $('#item-'+item[1]).attr('title') + '" ?' )){
			$.post(urlApp + modulo + "/delete", {id:item[1]}, function(data){
                $('#msg').html(feedback(data));
                $('#msg').removeClass("oculto");
			});
			setTimeout("window.location='" + urlApp + modulo + "'", 2000);
		}
	});
	
	$('aside#rightmenu a img, div.botonera a img, p.arrow img').hover(
		 function () {
			this.src = this.src.replace('_off', '');
	     }, 
	     function () {
	    	this.src = this.src.replace('.png', '_off.png');
	     }
	);
	$('header, aside#rightmenu li').hover(
		function(){
			$(this).find('img').each(function(){
				this.src = this.src.replace('_off', '');
			})
		},
		function(){
			$(this).find('img').each(function(){
				if(this.src.indexOf("_off") == -1){
					this.src = this.src.replace('.png', '_off.png');
				}
			})
		}
	);
	
	//imprimir
	$('aside#rightmenu a.print').click(function(){
		var item = this.id.split('-');
		window.open(urlApp + modulo + "/imprimir/" + item[1]);
	});
	//alta baja
	$('aside#rightmenu a.visible').click(function(){
			var item = this.id.split('-');
			var id = item[1];
			$.post(urlApp + modulo + "/view", {id:item[1], accion:"N"}, function(data){
	                $('#msg').html(feedback(data));
	                $('#msg').removeClass("oculto");
	        });
	        setTimeout("window.location='" + urlApp + modulo + "'", 2000);
	});
	$('aside#rightmenu a.no-visible').click(function(){
		var item = this.id.split('-');
		var id = item[1];
		$.post(urlApp + modulo + "/view", {id:item[1], accion:"S"}, function(data){
                $('#msg').html(feedback(data));
                $('#msg').removeClass("oculto");
        });
        setTimeout("window.location='" + urlApp + modulo + "'", 2000);
	});
	
	/**
	 * cambiar imagenes
	 */
	$('#change-img').click(function() {
		var item =  $(this).attr("class").split('-');
		if (confirm(gcMsg_confirmdeleteimg + " " + item[0] + " " + item[1])){
			$.post(urlApp + item[0] + '/deleteImagen', {id:item[1]}, function(data){
				$("#imagen_uploaded").attr("src", urlImgAdmin + 'loading.gif');
				$('#change-img').addClass("oculto");
				setTimeout("location.reload(true)", 5000);
			});
		}
	});
	
	/** 
	 * panel control
	 */
	
	$('div#inicio nav ul.menu > li').click(function() {
		var item =  $(this).attr("id").split('-');
		setTimeout("window.location='" + urlApp + item[1] + "'", 0);
	});
	
	/**
	 * estilos inputs vacios 
	 */
	$('input, select, textarea').each(function() {
		if(this.value=='' ||  this.value == '0'){
			$(this).addClass('empty');
		}
	});
	$('select').each(function() {
		if($(this).attr('size') > 1 && $(this).children().size()>0 ){
			$(this).removeClass('empty');
		}
	});
	
	$('input, select, textarea').blur(function(){
		if(this.value != '' && this.value != '0'){
			$(this).removeClass('empty');
		} else { 
			$(this).addClass('empty');
		}
	});
	$('input, select, textarea').click(function(){
		$(this).removeClass('error');
		$(this).prev('label').removeClass('error');
		$('#msg').addClass('oculto');
		$('#msg').html();
	});
	
	$("select").blur(function(){
		if(this.value==0){
			$(this).addClass('empty');
		} else { 
			$(this).removeClass('empty');
		}
	});
	
	/**
	 * carga de mapas y puntos geograficos
	 */
	if ($('#map').length){
		
		if(osm){
			//$("#map").osm(parseFloat($('#id_longitud').val()), parseFloat($('#id_latitud').val()), 20);	
			/* open layer */  
			map = new OpenLayers.Map("map");
			map.addLayer(new OpenLayers.Layer.OSM());
			var zoom = 15;
			 
			var lonLat = new OpenLayers.LonLat(parseFloat($('#id_longitud').val()), parseFloat($('#id_latitud').val()) )
			          .transform(
			            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			            map.getProjectionObject() // to Spherical Mercator Projection
			          );
			 
			map.setCenter (lonLat, zoom);
			
			var markers = new OpenLayers.Layer.Markers("Markers");
   			map.addLayer(markers);
   			var feature = new OpenLayers.Feature(markers, lonLat); 
            var marker = feature.createMarker();
            markers.addMarker(marker);
			
			$("#b_checkgeo").click(function(){
	    		var address = $('#direccion').val();
	    		var geoCodeURL = "http://nominatim.openstreetmap.org/search";
	    		$.ajax({
	    			 url: geoCodeURL,
	    			 data: {
	    				  format: "json",
	                      q: address
	                 },
	                 success: function(data) {
	                	data = $.browser.webkit ? data : jQuery.parseJSON(data);
	                	if(data.length>0){
		                	$('#id_latitud').val(data[0].lat);
			    	        $('#id_longitud').val(data[0].lon);
			    	        $('#id_ciudad').html(data[0].display_name);
			    	        
			    	        map.destroy();
			    	        map = new OpenLayers.Map("map");
				   			map.addLayer(new OpenLayers.Layer.OSM());
				   			 
				   			var lonLat = new OpenLayers.LonLat(data[0].lon, data[0].lat)
				   			          .transform(
				   			            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
				   			            map.getProjectionObject() // to Spherical Mercator Projection
				   			          );
				   			 
				   			var zoom = 16;
				   			map.setCenter (lonLat, zoom);
				   			var markers = new OpenLayers.Layer.Markers("Markers");
				   			map.addLayer(markers);
				   			var feature = new OpenLayers.Feature(markers, lonLat); 
				            var marker = feature.createMarker();
				            markers.addMarker(marker);
			    	        
				   			$('#b_geo').removeClass('oculto');
	                	} else {
	                		alert(gcMsg_geoerror);
	                	}
	                 }
	    		});
	    		
			});
			
		} else {
		
			var latlng = new google.maps.LatLng(parseFloat($('#id_latitud').val()), parseFloat($('#id_longitud').val()));
			var myOptions = {
				  zoom: 16,
				  center: latlng,
				  disableDefaultUI: true,
				  mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById("map"), myOptions);
			
			if($("#direccion").val()!=''){
				var marker = new google.maps.Marker({
		    	    position: latlng
		    	});
		    	marker.setMap(map);
			}
			
			
			$("#b_checkgeo").click(function(){
	    		var address = $('#direccion').val() + ', ' + ciudad;
	    		var geocoder = new google.maps.Geocoder();
	    	    geocoder.geocode({ 'address': address}, function(results, status) {
	    	        if (status == google.maps.GeocoderStatus.OK) {
	    	        	$('#id_ciudad').html(results[0].formatted_address);
	    	        	$('#id_latitud').val(results[0].geometry.location.lat());
	    	        	$('#id_longitud').val(results[0].geometry.location.lng());
	    	        	var latlng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
	    	        	var myOptions = {
	    	      			  zoom: 17,
	    	      			  center: latlng,
	    	      			  disableDefaultUI: true,
	    	      			  mapTypeId: google.maps.MapTypeId.ROADMAP
	    	      		};
	    	        	var marker = new google.maps.Marker({
	    	        	    position: latlng
	    	        	});
	    	        	marker.setMap(new google.maps.Map(document.getElementById("map"), myOptions));
	    	        } else {
	    	        	$('#map').html('No se encuentra');
	    	        }
	    	    });
			});	
		}
	}
	
	/**
	 * select multiples
	 */
	
	$('#right').click(function(){
		if($('.availables').val().length > 0){
			var option = new Option($('.availables option:selected').text(),$('.availables').val());
			$('.availables option[value=' + $('.availables').val() + ']').remove();
			$('.selecteds').append(option);
		}
	});	
	
	$('#left').click(function(){
		if($('.selecteds').val().length > 0){
			var option = new Option($('.selecteds option:selected').text(),$('.selecteds').val());
			$('.selecteds option[value=' + $('.selecteds').val() + ']').remove();
			$('.availables').append(option);
		}
	});	
	
		
		
});
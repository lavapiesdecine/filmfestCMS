$(document).ready(function() {
	
	$(".fancybox").fancybox();
	
	$('#b_conf').click(function(){
		if($('#form_conf').validate()){
			$('#form_conf').alta();
		}
	});	
	 
	$('#b_bbdd').click(function(){
		if($('#form_bbdd').validate()){
			$('#form_bbdd').alta();
		}
	});	
	
	$('#b_geo').click(function(){
		if($('#form_geo').validate()){
			$('#form_geo').alta();
		}
	});	
	
	jQuery.fn.alta = function() {
		$('#msg').html("<h3 class='loading'>" + gcMsg_loading + "</h3>");
		$('#msg').removeClass('oculto');
		$.ajax({
	        type: 'POST',
	        url: urlApp + controller + '/alta',
	        data: $(this).serialize(),
	        success: function(data) {
	        	var result = jQuery.parseJSON(data);
	        	if(result.ok){
	        		$('#msg').html("<h3 class='ok'>" + gcMsg_ok + "</h3>" +  result.msg);
		        	$('#b_'+controller+'_next').removeClass('oculto');
	        	} else {
	        		$('#msg').html("<h3 class='ko'>" + gcMsg_ko + "</h3>" + result.msg);
	        	}
	        }
	    })
	}
	/*
	jQuery.fn.osm = function(lon, lat, zoom) {
		 map = new OpenLayers.Map(this);
		 map.addLayer(new OpenLayers.Layer.OSM());
		 
		 var lonLat = new OpenLayers.LonLat(lon, lat)
		          .transform(
		            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
		            map.getProjectionObject() // to Spherical Mercator Projection
		          );
		 
		 var markers = new OpenLayers.Layer.Markers( "Markers" );
		 map.addLayer(markers);
		 
		 markers.addMarker(new OpenLayers.Marker(lonLat));
		 
		 map.setCenter (lonLat, zoom);
	}
	*/
	
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
		if($('#direccion').val() == '') {
			$('#b_geo').addClass('oculto');
		} else {
			$('#b_geo').removeClass('oculto');
		}
		
		if(osm){
			//$("#map").osm(parseFloat($('#id_longitud').val()), parseFloat($('#id_latitud').val()), 20);	
			/* open layer */  
			map = new OpenLayers.Map("map");
			map.addLayer(new OpenLayers.Layer.OSM());
			var zoom = 15;
			 
			var lonLat = new OpenLayers.LonLat( parseFloat($('#id_longitud').val()), parseFloat($('#id_latitud').val()) )
			          .transform(
			            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			            map.getProjectionObject() // to Spherical Mercator Projection
			          );
			 
			map.setCenter (lonLat, zoom);
			
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
	                	var first = $.browser.webkit ? data[0] : jQuery.parseJSON(data)[0];
	                	$('#id_latitud').val(first.lat);
		    	        $('#id_longitud').val(first.lon);
		    	        $('#id_ciudad').html(first.display_name);
		    	        
		    	        map.destroy();
		    	        map = new OpenLayers.Map("map");
			   			map.addLayer(new OpenLayers.Layer.OSM());
			   			 
			   			var lonLat = new OpenLayers.LonLat(first.lon, first.lat)
			   			          .transform(
			   			            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			   			            map.getProjectionObject() // to Spherical Mercator Projection
			   			          );
			   			 
			   			var zoom = 15;
			   			
			   			var markers = new OpenLayers.Layer.Markers("Markers");
			   			map.addLayer(markers);
			   			
			   			AutoSizeAnchored = OpenLayers.Class(OpenLayers.Popup.Anchored, {
			   	            'autoSize': true,
			   	        });
			   			
			   			var feature = new OpenLayers.Feature(markers, lonLat); 
			            feature.closeBox = true;
			            feature.popupClass = AutoSizeAnchored;
			            feature.data.popupContentHTML = "<strong>" + gcMsg_ubicacion + "</strong><br>" + first.display_name;
			            feature.data.overflow = "auto";
			                    
			            var marker = feature.createMarker();
			            
			            var markerClick = function (evt) {
			                if (this.popup == null) {
			                    this.popup = this.createPopup(this.closeBox);
			                    map.addPopup(this.popup);
			                    this.popup.show();
			                } else {
			                    this.popup.toggle();
			                }
			                currentPopup = this.popup;
			                OpenLayers.Event.stop(evt);
			            };
			            marker.events.register("mousedown", feature, markerClick);
						
			            markers.addMarker(marker);
			   			
			   			map.setCenter (lonLat, zoom);
		    	        
			   			$('#b_geo').removeClass('oculto');
			   			
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
	    		var address = $('#direccion').val();
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
	    	        	$('#b_geo').removeClass('oculto');
	    	        } else {
	    	        	$('#map').html('No se encuentra');
	    	        }
	    	    });
			});	
		}	
	}
	
});
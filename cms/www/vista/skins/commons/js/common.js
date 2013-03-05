$(document).ready(function() {
	
	/*fancybox*/
	$(".fancybox").fancybox({
	    helpers : {
	     media : {}
	    }
	});

	/*contactar*/
	$('#b-mail').click(function(){
		if($('#form-contacta').validate()){
			$.ajax({
		        type: 'POST',
		        url: urlApp  + 'contacta/send',
		        data: $('#form-contacta').serialize(),
		        success: function(data) {
		        	$('#msg').html(data);
		            $('#msg').removeClass('oculto');
		            setTimeout("window.location='" + urlApp + "contacta'", 5000);
		        }
		    })
		}
	});
	
});
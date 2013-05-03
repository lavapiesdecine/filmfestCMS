jQuery.fn.validate = function() {
	var form = $(this);
	var required = new Array();
	var email = new Array();
	var url = new Array();
	var error = "";
	$.each($(':input:not(:button, :submit, :radio, :checkbox)', form), function (i) {
		if (this.getAttribute('required') != null) {
            required[i] = $(this);
        }
        if (this.getAttribute('type') == 'email') {
        	email[i] = $(this);
        }
        if (this.getAttribute('type') == 'url') {
            url[i] = $(this);
        }
    });
	
	$(required).each(function (key, value) {
		if (value != undefined && ($(this).val() == $(this).attr('placeholder') || $(this).val() == '' || $(this).val() == '0' )) {
        	$(this).addClass('error');
        	$(this).prev('label').addClass('error');
        	var name = $(this).prev('label').text();
        	if(name.length==0){
        		name = $(this).attr('name');
        	}
        	error = error + name + " " + gcMsg_required + "<br>";
        }
	});
	
    $(email).each(function (key, value) {
    	if (value != undefined && $(this).val()!='' && $(this).val().search(/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/i)) {
        	$(this).addClass('error');
        	$(this).prev('label').addClass('error');
        	error = error + gcMsg_email + "<br>";
        }
    });
	
    $(url).each(function (key, value) {
    	if (value != undefined && $(this).val()!='' && $(this).val().search(/^(((ht|f)tp(s?))\:\/\/)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/)) {
    		$(this).addClass('error');
        	$(this).prev('label').addClass('error');
    		error = error + gcMsg_urlFormat + "<br>";
        }
    });
	
    if($('.selecteds').size()>0 && $('.selecteds').children().size()==0){
    	$(this).addClass('error');
    	$('.selecteds').prev('label').addClass('error');
    	$('.selecteds').addClass('error');
    	error = error + gcMsg_selected + "<br>";
    }
    
    if (error.length>0){
		error = "<h3>" + gcMsg_revisa + ":</h3>" + error;
		//mostrar error
		$('#msg').html(error);
		$('#msg').removeClass('oculto');
		return false;
	} else {
		return true;
	}
}
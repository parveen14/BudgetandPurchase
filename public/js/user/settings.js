$(document).ready(function(){
	$.validator.addMethod("validEmail", function(value, element) {
        return this.optional(element) ||
            /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(value);
    }, "Please enter a valid email address.");

	$('.contactsformValidations').each( function(){
		$(this).validate({
	        rules: {
	            title: {
	                required: true,
	                maxlength: 50
	            },
	            email: {
	                required: true,
	                validEmail: true
	            }
	        },
	        messages: {
	        	title: {
	                required: "Please enter support type."
	            },
	            email: {
	                required: "Please enter email.",
	                validEmail: "Please enter valid email."
	            }
	        }
	    });
	});
	
	$(document).on('click', '.addContactBtn', function(){
		var e = $(this);
		var form = e.closest('form')[0];
		if($('#'+form.id).valid() == true){
	    	$.ajax({
	            url: "/admin/settings/save-contacts",
	            data: $('#'+form.id).serialize(),
	            type:'post',
	            dataType: 'json',
	            beforeSend: function () {
	            	e.html('<i class="fa fa-spinner fa-spin"></i>');
	            },
	            complete: function () {
	            	
	            },
	            success: function (json) {
	            	if(json.exception_message){
		        		bootbox.dialog({
		        			closeButton: true,
						    message: json.exception_message,
						    title: "Alert",
						    buttons: {
						    	main: {
						            label: "Ok",
						            className: "btn-danger",
						            callback: function() {
						                
						            }
						        }
						    }
						});
		        	}
		        	else if(json.success){
		        		if(json.type == 'insert'){
		        			clear_form_elements('#'+form.id);
		        			location = "/admin/settings";
		        		}
		        		else{
		        			
		        		}
		        	}
	            },
	            error: function (xhr, ajaxOptions, thrownError) {
	            	
	            }
	        }).done(function(json){
	        	e.html('<i class="fa fa-pencil"></i>');
	        });
		}
	});
	
	$(document).on('click','.deleteContact', function(){
		var e = jQuery(this);
        bootbox.confirm("Are you sure you want to delete this contact?", function(result) {
            if (result) {
            	jQuery.ajax({
                    url: "/admin/settings/delete-contact",
                    data: {
                        id: e.data('id'),
                    },
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function() {
                    	e.html('<i class="fa fa-spinner fa-spin"></i>');
                    },
                    complete: function() {

                    },
                    success: function(json) {
                        if (json.exception_message) {
                            bootbox.dialog({
                                closeButton: true,
                                message: json.exception_message,
                                title: "Alert",
                                buttons: {
                                    main: {
                                        label: "Ok",
                                        className: "btn-danger",
                                        callback: function() {

                                        }
                                    }
                                }
                            });
                        } else if (json.success) {
                            e.closest('form').fadeOut(function() {
                                e.remove();
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {

                    }
                });
            }
        });
	});
});
var host = window.location.host;
var ajax_url = '';
var proto = window.location.protocol;
	 
ajax_url = proto+"//"+host+"/budgetandpurchase/public";


$(document).ready(function() {
   
   /** jQuery.validator.addMethod("specialChars", function( value, element ) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var key = value;

        if (!regex.test(key)) {
           return false;
        }
        return true;
    }, "please use only alphanumeric or alphabetic characters"); **/
	
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[a-z\d\-_\s]+$/i.test(value);
	}, "Letters, numbers,space and underscores allowed");
	
	$.validator.addMethod('lessthan', function(value, element, param) {
          return this.optional(element) || parseFloat(value) < parseFloat($(param).val());
    }, 'Invalid value');
    $.validator.addMethod('greaterthan', function(value, element, param) {
          return this.optional(element) || parseFloat(value) >= parseFloat($(param).val());
    }, 'Invalid value');
	
	
   $('.activeDeactive').on('click', function(){
    	var e = $(this);
    	bootbox.confirm("Are you sure you want to change the status of this?", function(result) {
            if (result) {
				id=e.attr('data-id');
				calltype=e.attr('data-type');
				status=e.attr('data-status');
				
            	jQuery.ajax({
                    url: ajax_url+"/user/changestatus",
                    data: {
                        'id': e.attr('data-id'),
                        type: calltype,
                        status: e.attr('data-status'),
                        field: e.attr('data-field')
                    },
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function() {
                    	e.html('<img src="'+ajax_url+'/images/loaders/loader4.gif">'); 
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
                        	if(json.status == '1'){
                        		e.html('<img data-tooltip="tooltip" src="'+ajax_url+'/images/status_green.png" alt="Active" title="Active , click here to deactivate">');
								e.attr('data-status',json.status); 
								e.attr('data-type',calltype); 
								e.attr('data-id',id); 
                        	}
                        	else{
                        		e.html('<img data-tooltip="tooltip" src="'+ajax_url+'/images/status_red.png" alt="Inactive" title="Inactive , click here to activate">');
								e.attr('data-status',json.status);
								e.attr('data-type',calltype); 
								e.attr('data-id',id); 
                        	}
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {

                    }
                });
            }
    	});
    });
    
    $(".changecompany").on('click',function(){
	
		id=$(this).data('companyid');
		name=$(this).data('companyname');
		if(id!="" && name!="") {
			jQuery.ajax({
				url: ajax_url+"/user/changecompany",
				data: {
					'id': id,
					'name': name,
				},
				type: 'post',
				dataType: 'json',
				beforeSend: function() {
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
						location.reload();
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {

				}
			});
		}
	});
});
   
function changeLoadingText(e){
	var loadingText = e.data('loading-text');
	var afterLoadingText = e.data('after-loading-text');
	var disableEnable = (e.attr('disabled') ? false : true);
	e.val(loadingText).attr('disabled', disableEnable);
	e.data('loading-text',afterLoadingText);
	e.data('after-loading-text',loadingText);
}

function autoRefresh(fnc, dLay) {
	window[fnc]();
	setTimeout(function() {
		autoRefresh(fnc, dLay);
	}, parseInt(dLay));
}

function clear_form_elements(formm) {

    $(formm).find(':input').each(function () {
        switch (this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'hidden':
            case 'text':

            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
}

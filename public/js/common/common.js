$(document).ready(function() {
   
   $('.activeDeactive').on('click', function(){
    	var e = $(this);
    	bootbox.confirm("Are you sure you want to change the status of this?", function(result) {
            if (result) {
				id=e.attr('data-id');
				calltype=e.attr('data-type');
				status=e.attr('data-status');
				
            	jQuery.ajax({
                    url: "/user/changestatus",
                    data: {
                        'id': e.attr('data-id'),
                        type: calltype,
                        status: e.attr('data-status')
                    },
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function() {
                    	e.html('<img src="/images/loaders/loader4.gif">'); 
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
                        		e.html('<img data-tooltip="tooltip" src="/images/status_green.png" alt="Active" title="Active , click here to deactivate">');
								e.attr('data-status',json.status); 
								e.attr('data-type',calltype); 
								e.attr('data-id',id); 
                        	}
                        	else{
                        		e.html('<img data-tooltip="tooltip" src="/images/status_red.png" alt="Inactive" title="Inactive , click here to activate">');
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
});
   
   
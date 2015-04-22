jQuery(document).ready(function() {

	/** jQuery("select").chosen({
		'min-width' : '100px',
		'white-space' : 'nowrap',
		disable_search_threshold : 10
	}); **/

	$("#addUserForm").validate({
        rules: {
            email: {
                required: true,
                email:true,
				minlength: 5,
                maxlength: 50,
            },
            'business_unit[]': {
                required: true,
            },
            'department[]': {
                required: true,
            },
             'role[]': {
                required: true,
            },
        },
        messages: {
        	email: {
                required: "Please enter email.",
                email: "Please enter valid email.",
				minlength: "Minimum 5 and Maximum 50 characters required.",
                maxlength: "Minimum 5 and Maximum 50 characters required.",
            },
            'business_unit[]': {
            	required: "Please select business unit."
            },
            'department[]': {
            	required: "Please select department."
            },
            'role[]': {
            	required: "Please select role."
            },
        }
    });
	
	$("#addUserBtns").on('click',function(e){
			var e=$(this);
		
			if($("#addUserForm").valid() == true){
				 var formData = new FormData($("#addUserForm")[0]);
					$.ajax({
						url: ajax_url+"/user/adduser",
						data: formData,
						contentType: false,
						processData: false,
						type:'post',
						dataType: 'json',
						beforeSend: function () {
							changeLoadingText(e);
						},
						complete: function () {
							changeLoadingText(e);
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
								bootbox.dialog({
									closeButton: true,
									message: "DONE",
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
						},
						error: function (xhr, ajaxOptions, thrownError) {
							
						}
					});
			}
			return false;
	});	
	
	$(".addmore").on('click',function(e){ 
		//var newrow=$('#businessgroup .form-group:first').clone();
		//$('#businessgroup').append(newrow);
		
		$.ajax({
			url: ajax_url+"/user/addbusinessrow",
			data: {
				//id: selectedBusinessUnit,
			},
			type: 'post',
			//dataType: 'json',
			beforeSend: function () {
				//changeLoadingText(e);
			},
			complete: function () {
				//changeLoadingText(e);
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
				} else {
					$('#businessgroup').append(json);
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				
			}
		});
		
	});
	
	$(document).on('click','.removemore',function(){
			$(this).parent('div').parent('.form-group').remove();
	});
	
	$(document).on('change','.business_unit',function(){
			var e=$(this);
			if(e.val()=="") {
				e.parent('div').parent('div').find(".departments").html('<option value="">Select Business Unit First</option>');
				return false;
			}
			
			var selectedBusinessUnit=e.val();
					$.ajax({
						url: ajax_url+"/user/selectdata",
						data: {
							id: selectedBusinessUnit,
						},
						type: 'post',
						dataType: 'json',
						beforeSend: function () {
							//changeLoadingText(e);
						},
						complete: function () {
							//changeLoadingText(e);
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
							} else {
								
								selectValues="";
								if(json.results!="") {
								var n = json.results.length;
								var array = json.results;
									for(i=0;i<n;i++)
									{
										selectValues +="<option value="+array[i].i_dep_id+">"+array[i].vc_name+"</option>";
									} 
								} else {
									selectValues +="<option value=''>No department in this business unit</option>";
								}
								
								      e.parent('div').parent('div').find(".departments").html(selectValues)
							}
						},
						error: function (xhr, ajaxOptions, thrownError) {
							
						}
					});
	});
	
	
	$('.activeDeactiveCompanyUser').on('click', function(){
    	var e = $(this);
    	bootbox.confirm("Are you sure you want to change the status of this?", function(result) {
            if (result) {
				id=e.attr('data-id');
				calltype=e.attr('data-type');
				status=e.attr('data-status');
				
            	jQuery.ajax({
                    url: ajax_url+"/user/companyuserstatus",
                    data: {
                        'id': e.attr('data-id'),
                        'id1': e.attr('data-id-1'),
                        type: calltype,
                        status: e.attr('data-status'),
                        field: e.attr('data-field'),
                        field1: e.attr('data-field-1')
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
});

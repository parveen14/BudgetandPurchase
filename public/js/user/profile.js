jQuery(document).ready(function() {


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
                maxlength: "Minimum 3 and Maximum 50 characters required.",
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
	
	
	
	$(document).on('change','#i_ref_country_id',function(){
			var e=$(this);
			
			if(e.val()!="") {
			var selectedBusinessUnit=e.val();
					$.ajax({
						url: ajax_url+"/user/selectstates",
						data: {
							id: e.val(),
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
										selectValues +="<option value="+array[i].states_id+">"+array[i].name+"</option>";
									} 
								} else {
									selectValues +="<option value=''>No state found in this country</option>";
								}
								
								      $("#i_ref_state_id").html(selectValues)
							}
						},
						error: function (xhr, ajaxOptions, thrownError) {
							
						}
					});
		}
	});
});

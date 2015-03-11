jQuery(document).ready(function() {
	// CKEditor
	jQuery('#content').ckeditor();
	
	$("#addPageForm").validate({
        rules: {
            title: {
                required: true,
                maxlength: 50
            },
            route: {
                required: true
            }
        },
        messages: {
        	title: {
                required: "Please enter title."
            },
            route: {
                required: "Please enter route."
            }
        }
    });
	
	/**
	 * Create Campaign
	 * @author Vipul
	 */
	$('#addPageBtn').on('click', function(){
		
		var e = $(this);
		if($("#addPageForm").valid() == true){
			var formData = new FormData($('#addPageForm')[0]);
			formData.append('content', CKEDITOR.instances.content.getData())
	    	$.ajax({
	            url: "/admin/pages/save-page",
	            data: formData,
	            type:'post',
	            processData: false,
	            contentType: false,
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
		        		if(json.type == 'insert'){
		        			clear_form_elements('#addPageForm');
		        			location = '/admin/pages';
		        		}
		        		else{
		        			location = '/admin/pages';
		        		}
		        	}
	            },
	            error: function (xhr, ajaxOptions, thrownError) {
	            	
	            }
	        });
		}
    });
});
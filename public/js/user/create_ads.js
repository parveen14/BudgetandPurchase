jQuery(document).ready(function(){
	$("#createAdsForm").validate({
        rules: {
            title: {
                required: true,
                maxlength: 50
            },
            url: {
                required: true,
                url: true
            },
            type: {
                required: true
            }
        },
        messages: {
        	title: {
                required: "Please enter title."
            },
            url: {
                required: "Please enter url."
            },
            type: {
                required: "Please select ad type."
            }
        }
    });
	
	/**
	 * Create Campaign
	 * @author Vipul
	 */
	$('#createAdsBtn').on('click', function(){
		var e = $(this);
		if($("#createAdsForm").valid() == true){
			var formData = new FormData($('#createAdsForm')[0]);
	    	$.ajax({
	            url: "/admin/ads/save-ads",
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
		        			clear_form_elements('#createAdsForm');
		        			location = '/admin/ads';
		        		}
		        		else{
		        			//location = '/admin/ads/create/'+json.id;
		        			location = '/admin/ads';
		        		}
		        	}
	            },
	            error: function (xhr, ajaxOptions, thrownError) {
	            	
	            }
	        });
		}
    });
	
	$(document).on('click', '.fileupload-exists', function(){
		$('.imgPreview').html('');
	});
	
	$('.adtype').on('change', function(){	
		$('.type1').hide(); $('.type2').hide();
		$('.type'+$(this).val()).show('slow');
	});
});
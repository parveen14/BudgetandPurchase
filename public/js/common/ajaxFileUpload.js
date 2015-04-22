$(document).ready(function(){
	$("#uploadButton").ajaxUpload({
        url: "/sponsor/campaign/upload-images",
        responseType: 'json',
        name: "file",
        onSubmit: function() {
        	$('.progress').show();
        },
        onComplete: function(result) {
        	$('.progress').hide();
        	var json = eval('(' + result + ')');
        	if(!json.success){
        		var html = '<ul>';
        		for(var i in json.error_messages){
        			html += '<li>'+json.error_messages[i]+'</li>';
        		}
        		html += '</ul>';
        		bootbox.dialog({
    				closeButton: false,
    			    message: html,
    			    title: "Error Message(s)",
    			    buttons: {
    			    	main: {
    			            label: "Ok",
    			            className: "btn-primary",
    			            callback: function() {
    			                
    			            }
    			        }
    			    }
    			});
        	}
        	else{
        		$('#isImageUploaded').val(isImageUploaded++);
        		$('#createCampaignForm').valid();
        		var html = '<li>';
        		html += '<div class="CampangUploadImg">';
        		html += '<img src="/uploads/campaign/thumbnails/200x200/'+json.filename+'">';
        		html += '<input type="hidden" name="images[]" value="'+json.filename+'">';
        		html += '</div> <a href="javascript:void(0);" class="deleteCampaignImage CampangUploadImglink fa fa-trash" data-filename = "'+json.filename+'"></a>';
        		html += '</li>';
        		$('.CreateCampangImgList ul').append(html);
        	}
        }
    });
});
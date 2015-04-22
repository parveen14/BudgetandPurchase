
jQuery(document).ready(function(){

	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3,4 ] }
       ]
	});
	
	$("#addBusinessunitForm").validate({
        rules: {
            vc_short_name: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
			i_status: {
                required: true
            },
        },
        messages: {
        	vc_short_name: {
                required: "Please enter name.",
				minlength: "Minimum 3 and Maximum 32 characters required.",
                maxlength: "Minimum 3 and Maximum 32 characters required.",
				//alphanumeric : "Only alpha numeric characters allowed"
            },
			i_status: {
                required: "Please select status."
            }
        }
    });
});

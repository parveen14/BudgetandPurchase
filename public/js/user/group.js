jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3,4 ] }
       ]
	});
	
	$("#addGroupForm").validate({
        rules: {
            vc_name: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
			'group_role[]': {
                required: true
            },
			i_status: {
                required: true
            },
        },
        messages: {
        	vc_name: {
                required: "Please enter title.",
				minlength: "Minimum 3 and Maximum 32 characters required.",
                maxlength: "Minimum 3 and Maximum 32 characters required.",
            },
			'group_role[]': {
                required: "Please select roles."
            },
			i_status: {
                required: "Please select status."
            }
        }
    });
});

jQuery(document).ready(function(){

	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3,4 ] }
       ]
	}); 

	  
	$("#addRoleForm").validate({
	
        rules: {
            vc_name: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
            i_ref_limit_id: {
                required: true,
            },
			'roles_permission[]': {
                required: true
            },
			i_status: {
                required: true
            },
        },
        messages: {
        	vc_name: {
                required: "Please enter name.",
				minlength: "Minimum 3 and Maximum 32 characters required.",
                maxlength: "Minimum 3 and Maximum 32 characters required.",
            },
        	i_ref_limit_id: {
                required: "Please select level."
            },
			
			'roles_permission[]': {
                required: "Please select permissions."
            },
			i_status: {
                required: "Please select status."
            }
        }
    });
});

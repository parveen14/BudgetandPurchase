jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3,4 ] }
       ]
	});
	
	$("#addDepartmentForm").validate({
        rules: {
            vc_name: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
            'business_units[]': {
                required: true,
            },
			i_status: {
                required: true,
            },
        },
        messages: {
        	vc_name: {
                required: "Please enter name.",
				minlength: "Minimum 3 and Maximum 32 characters required.",
                maxlength: "Minimum 3 and Maximum 32 characters required.",
            },
            'business_units[]': {
                required: 'Please select business unit',
            },
			i_status: {
                required: "Please select status."
            }
        }
    });
});

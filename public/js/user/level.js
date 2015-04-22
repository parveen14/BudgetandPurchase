jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 3,4 ] }
       ]
	});

	
	$("#addLevelForm").validate({
	
        rules: {
            vc_name: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
            i_start_limit: {
                required: true,
                digits: true,
				lessthan: '#i_end_limit'
            },
            i_end_limit: {
                required: true,
                digits: true,
				greaterthan: '#i_start_limit',
				min: 1
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
            i_start_limit: {
                required: "Please enter minimum budget.",
                digits: "Please add integer value only.",
				lessthan: "This value should be less than maximum budget value."
            },
            i_end_limit: {
                required: "Please enter maximum budget.",
                digits: "Please add integer value only.",
				greaterthan: "This value should be greater than minimum budget value."
            },
			i_status: {
                required: "Please select status."
            }
        }
    });
});

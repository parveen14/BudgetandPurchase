jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3,4 ] }
       ]
	});
	
	
	
	$("#addCostcentergroupForm").validate({
	
        rules: {
            vc_account_group: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
            'costcenter_costgroup[]': {
                required: true
            },
			i_status: {
                required: true
            },
        },
        messages: {
        	vc_account_group: {
                required: "Please enter title.",
				minlength: "Minimum 3 and Maximum 32 characters required.",
                maxlength: "Minimum 3 and Maximum 32 characters required.",
            },
            'costcenter_costgroup[]': {
                required: "Please select cost center."
            },
			i_status: {
                required: "Please select status."
            }
        }
    });
});

jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 3,4,5 ] }
       ]
	});
	
	
	
	$("#addCostcenterForm").validate({
	
        rules: {
            vc_name: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
			vc_account_number: {
                required: true,
				minlength: 2,
                maxlength: 32,
            },
            i_budget: {
                required: true,
                digits:true
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
			vc_account_number: {
                required: "Please enter account number.",
				minlength: "Minimum 2 and Maximum 32 characters required.",
                maxlength: "Minimum 2 and Maximum 32 characters required.",
            },
            i_budget: {
                required: "Please enter budget.",
                digits:"Iunteger value allowed"
            },
			i_status: {
                required: "Please select status."
            }
        }
    });
});

jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3 ] }
       ]
	});
	
	$("#addLocationForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 3,
                maxlength: 32,
				alphanumeric: true
            },
			pStatus: {
                required: true,
            },
        },
        messages: {
        	title: {
                required: "Please enter title.",
				minlength: "Minimum 3 and Maximum 32 characters required.",
                maxlength: "Minimum 3 and Maximum 32 characters required.",
            },
			pStatus: {
                required: "Please select status."
            }
        }
    });
});
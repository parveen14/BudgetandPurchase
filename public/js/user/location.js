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
                maxlength: 50
            },
			pStatus: {
                required: true,
            },
        },
        messages: {
        	title: {
                required: "Please enter title."
            },
			pStatus: {
                required: "Please select status."
            }
        }
    });
});
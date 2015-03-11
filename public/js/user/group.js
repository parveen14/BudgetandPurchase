jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3 ] }
       ]
	});
	
	$("#addGroupForm").validate({
        rules: {
            title: {
                required: true,
                maxlength: 50
            },
			'level[]': {
                required: true
            },
			pStatus: {
                required: true
            },
        },
        messages: {
        	title: {
                required: "Please enter title."
            },
			'level[]': {
                required: "Please select levels."
            },
			pStatus: {
                required: "Please select status."
            }
        }
    });
});
jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 2,3 ] }
       ]
	});
	
	$("#addBusinessunitForm").validate({
        rules: {
            title: {
                required: true,
                maxlength: 50
            },
			'department[]': {
                required: true
            },
			'project[]': {
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
			'department[]': {
                required: "Please select department."
            },
			'project[]': {
                required: "Please select project."
            },
			pStatus: {
                required: "Please select status."
            }
        }
    });
});
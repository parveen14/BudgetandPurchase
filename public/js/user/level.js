jQuery(document).ready(function(){
	
	$('#tableUsers').dataTable({
		"sPaginationType" : "full_numbers",
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 3,4 ] }
       ]
	});
	
	$.validator.addMethod('lessthan', function(value, element, param) {
          return this.optional(element) || parseFloat(value) < parseFloat($(param).val());
    }, 'Invalid value');
    $.validator.addMethod('greaterthan', function(value, element, param) {
          return this.optional(element) || parseFloat(value) >= parseFloat($(param).val());
    }, 'Invalid value');
	
	$("#addLevelForm").validate({
	
        rules: {
            title: {
                required: true,
                maxlength: 50
            },
			level_id: {
                required: true,
            },
			'permission[]': {
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
			level_id: {
                required: "Please select level for role.",
            },
			'permission[]': {
                required: "Please select permissions."
            },
			pStatus: {
                required: "Please select status."
            }
        }
    });
});
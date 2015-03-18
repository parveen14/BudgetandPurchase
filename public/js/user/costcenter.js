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
	
	$("#addCostcenterForm").validate({
	
        rules: {
            title: {
                required: true,
                maxlength: 50
            },
			code: {
                required: true,
            },
			budget: {
                required: true,
				digits: true,
            },
			pStatus: {
                required: true
            },
        },
        messages: {
        	title: {
                required: "Please enter title."
            },
			code: {
                required: "Please enter code.",
            },
			budget: {
                required: "Budget Required.",
                digits: "Budget should be numeric only.",
            },
			
			pStatus: {
                required: "Please select status."
            }
        }
    });
});
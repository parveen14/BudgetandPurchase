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
			budget_min: {
                required: true,
				digits: true,
				lessthan: '#budget_max'
            },
			budget_max: {
                required: true,
				digits: true,
				greaterthan: '#budget_min',
				min: 1
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
			budget_min: {
                required: "Please add minimum budget.",
				digits: "Please add integer value only.",
				lessthan: "This value should be less than maximum budget value."
            },
			budget_max: {
                required: "Please add maximum budget.",
				digits: "Please add integer value only.",
				greaterthan: "This value should be greater than minimum budget value."
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
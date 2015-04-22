$(function() {
    $.validator.addMethod("validpassword", function(value, element) {
        return this.optional(element) ||
            /^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])(?=.*[\W_]).*$/.test(value);
    }, "The password must contain one lower case ,upper case ,digit and special character.");


    $.validator.addMethod("validEmail", function(value, element) {
        return this.optional(element) ||
            /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(value);
    }, "Please enter a valid email address.");
	
	$.validator.addMethod("notEqual", function(value, element, param) {
		return this.optional(element) || value != $(param).val();
	}, "Please specify a different (non-default) value");

	$(document).on('click','.popover-close-button', function(event){
		//$('input[name="'+$(this).data('elem')+'"],textarea[name="'+$(this).data('elem')+'"],select[name="'+$(this).data('elem')+'"]').next().popover('hide');
		
		$(this).parent().parent('div .popover-validations').hide('slow');
	});
});

(function($) {
    $.extend(true, $.validator, {
        prototype: {
            defaultShowErrors: function() {
                var self = this;
                $.each(this.successList, function(index, value) {
                    $(value).removeClass(self.settings.errorClass).addClass(self.settings.validClass).popover('destroy');
                    if (self.settings.unhighlight) {
                        self.settings.unhighlight.call(self, value, self.settings.errorClass, self.settings.validClass);
                    }
                });
                $.each(this.errorList, function(index, value) {
                    $(value.element).removeClass(self.settings.validClass).addClass(self.settings.errorClass).popover('destroy').popover(self.apply_popover_options(value.element, value.message)).popover('show');
                    if (self.settings.highlight) {
                        self.settings.highlight.call(self, value.element, self.settings.errorClass, self.settings.validClass);
                    }
                });
            },
            apply_popover_options: function(element, message) {
                var options = {

                    /*animation: $(element).data('animation') || true,
                    html: $(element).data('html') || false,
                    placement: $(element).data('placement') || 'top',
                    selector: $(element).data('animation') || false,
                    title: $(element).attr('title') || message,
                    trigger: $.trim('manual ' + ($(element).data('trigger') || '')),
                    delay: $(element).data('delay') || 0,
                    container: $(element).data('container') || false,*/
                    
                    trigger: $.trim('manual ' + ($(element).data('trigger') || '')),
                    placement: $(element).data('placement') || 'top',
                    content: function() {
                        return message;
                    },
                    template: '<div class="popover popover-validations"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div><button type="button" class="popover-close-button close" aria-hidden="true" data-elem="'+element.name+'">&times;</button></div></div>'
                };
                if (this.settings.popover_options && this.settings.popover_options[element.name]) {
                    $.extend(options, this.settings.popover_options[element.name]);
                }
                if (this.settings.popover_options && this.settings.popover_options['_all_']) {
                    $.extend(options, this.settings.popover_options['_all_']);
                }
                return options;
            }
        }
    });
}(jQuery));

$(function() {
	$("#loginForm").validate({
        rules: {
            email_id: {
                required: true,
                validEmail: true,
                maxlength: 50
            },
            password: {
                required: true
            }
        },
        messages: {
            email_id: {
                required: "Please enter email address."
            },
            password: {
                required: "Please enter password."
            }
        }
    });

    $("#forgotpasswordForm").validate({
        rules: {
            email_id: {
                required: true,
                validEmail: true,
                maxlength: 50
            }
        },
        messages: {
            email_id: {
                required: "Please enter email address."
            }
        }
    });

    $("#registerForm").validate({
        rules: {
            firstname: {
                required: true,
                maxlength: 30
            },
            lastname: {
                required: true,
                maxlength: 30
            },
            email_id: {
                required: true,
                validEmail: true,
                maxlength: 50
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 20,
                validpassword: true
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            firstname: {
                required: "Please enter first name."
            },
            lastname: {
                required: "Please enter last name."
            },
            email_id: {
                required: "Please enter email address."
            },
            password: {
                required: "Please enter password.",
            },
            confirm_password: {
                required: "Please enter confirm password.",
                equalTo: "Password does not match."
            }
        }
    });

    $("#registerFormSponsor").validate({
        rules: {
            company_name: {
                required: true,
                maxlength: 30
            },
            email_id: {
                required: true,
                validEmail: true,
                maxlength: 50
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 20,
                validpassword: true
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            company_name: {
                required: "Please enter company name."
            },
            email_id: {
                required: "Please enter email address."
            },
            password: {
                required: "Please enter password",
            },
            confirm_password: {
                required: "Please enter confirm password.",
                equalTo: "Password does not match."
            }
        }
    });

    $("#resetpasswordForm").validate({
        rules: {
            password: {
                required: true,
                minlength: 8,
                maxlength: 20,
                validpassword: true
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: "Please enter password.",
                equalTo: 'confirm_password.'
            },
            confirm_password: {
                required: "Please enter confirm password.",
                equalTo: "Password does not match."
            }
        }
    });
    
    
    $("#loginFormAdmin").validate({
        rules: {
            username: {
                required: true,
                maxlength: 50
            },
            password: {
                required: true
            }
        },
        messages: {
        	username: {
                required: "Please enter username."
            },
            password: {
                required: "Please enter password."
            }
        }
    });
});
/* Author: @JoeJiko */
$(document).ready(function(){

	/* FORM PROCESSING */
	// require fields, custom checking for email and phone
	$(".name, .required").addClass("validate[required]");
	$(".email").addClass("validate[required,custom[email]]");
	$(".phone").addClass("validate[required,custom[phone]]");

	// submit with ajax
	function beforeCall(form, options){

		// block form from being resubmitted
		$(form).mask("Please wait while we process your request..");
		function thanksmsg(form){
			$(form).unmask();
			if($(form).attr('id') == "contact")
			{
				$(form).load('pages/contact/thank-you.html');
				$.scrollTo(form);
			} else {
				$(form).empty().append('Thank you for subscribing');
			}
		}

		// remove error messages
		$(form).find(".error").remove();


		// submit form for processing
		$.ajax({

			url: "contact/contact.php",
			type: "POST",
			data: $(form).serialize(),
			dataType: "json",
			success: function(response){

				// processing complete (good)
				if(response.status!="error"){
					thanksmsg(form);
				}

				// processing complete (bad)
				else
				{

				// response error
				$(form).prepend('<span class="error">'+response.message+'</span>');

				}
			},

			error: function(request,status,error){ console.log(request.responseText); }

		});

		// stop form from validating again
		return false;

	}

	// validationEngine - binds form submission and fields to the validation engine
	$(".validate").validationEngine({
		ajaxFormValidation: true,
		onBeforeAjaxFormValidation: beforeCall
	});
});
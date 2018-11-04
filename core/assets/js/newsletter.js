(function($){
	$(document).on('click','.newsletterBtn',function(event){
		event.preventDefault();
		var button = $(this);
	    var name = $("#email").val();
		$.ajax({
			type: 'POST',
			url: NEWSLETTER_OBJECT.ajaxurl,
			data: {"action": "saveBasethemeNewsletter", "email": name, "name": name},
			success: function(data){
				if(data) {
					$('.statusNewsletter').html(data);
				}
				$('.statusNewsletter').addClass('subscribed');
			}
		});
	});
}) (jQuery);
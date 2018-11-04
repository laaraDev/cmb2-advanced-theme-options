(function($) {
	var windowHeight = $(window).height();
	if ($('.tab-content').height() < windowHeight) {
		$('.fitHeight').css('height', windowHeight-170);
	}

	$(document).on('click', '#basetheme_add_options_button', function(event) {
		event.preventDefault();
		var import_options = $('#basetheme_import');
		if (import_options.val() == '') {
			alert('No options to import');
			return;
		}
		var options = {'import_options': import_options.val()};
	    var parms = {'action': 'basetheme_import_options','security': BASETHEME_OBJECT.security,'param': options};
	    $.post(BASETHEME_OBJECT.ajaxurl,parms,function(response) {
			var data = $.parseJSON(response);
			if (data.error == 0) {
				$('#basetheme_import').next('p').after().append('<p class="alert alert-success">Options imported successfully<p>');
				import_options.val('');
				setTimeout(function () {
					$('.alert-success').fadeOut('slow').remove();
				}, 3000);
			}
		});
	});

	$(document).on('click', '.cmb-add-group-row', function(event) {
		event.preventDefault();
		var index = $('.cmb-repeatable-grouping').length;
		$('.cmb-repeatable-grouping').last().find('.element_index').val(index);
	});

	$(document).on('click', '.active-btn-save', function(event) {
		event.preventDefault();
		$('.cmb-form').find('input[type="submit"]').trigger('click');	
	});

	$(window).load(function() {
		$('.cmb-repeatable-grouping').each(function(index, el) {
			if ($(el).find('.element_index').val() == '') {
				$(el).find('.element_index').val(index+1);
			}
		});
	});

	// replace space in string with underscore
	$(document).on('keyup', '[data-keywords="1"]', function(event) {
		event.preventDefault();
		var newString;
		if (newString = $(this).val().replace(/\s/g, ",")) {
			$(this).val(newString);
		}
	});

}) (jQuery);
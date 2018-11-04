(function ($) {
	$('.repeat').each(function() {
		$(this).repeatable_fields({
			wrapper: 'table',
			container: 'tbody',
			is_sortable: true,
			before_add: function(container){
				// console.info('before_container', container);
			},
			after_add: function (container, new_row){
				$.fn.repeatable_fields().after_add(container, new_row);
				$(new_row).find('input,textarea,select').each(function(index, el) {
					$(el).attr('id', uniqid());
				});
				tinyMCE_render(new_row);
			},
			sortable_options : {
				revert: true,
				change: function( event, ui ) {
					// console.info('ui', ui);
					// console.info('event', event);
					// tinyMCE_render(ui.item);

				},
				update: function( event, ui ) {
					// console.info('ui', ui);
					// console.info('event', event);
				},
				stop: function( event, ui ) {
					// console.info('ui', ui);
					// console.info('event', event);
				},
			}
		});
	});

	$('textarea.editor').each(function(index, el) {
		if ($(el).parents('tr').hasClass('template') == false) {
			$(el).attr('id', uniqid());
			tinyMCE_init( $(el).attr('id'), $(el).text() );
		}
	});
	// editor render
	function tinyMCE_render (item_container) {
		var length = $(item_container).find('textarea.editor').length;
		if (length > 0) {
			$(item_container).find('textarea.editor').each(function(index, el) {
				// var id = $(el).attr('name');
				var id = $(el).parents('tr').attr('data-uniqid-id');
				$(el).attr('id', id);
				if ($(el).parents('tr').hasClass('template') == false) {
					tinyMCE_init( $(el).attr('id'), $(el).val() );
				}
			});
		}
	}

	// editor init
	function tinyMCE_init ( editor_id, content ) {
		var options = {'editor_id': editor_id, 'editor_name': $('#'+editor_id).attr('name')};
	    var parms = {'action': 'basetheme_wp_editor','security': BASETHEME_OBJECT.security,'param': options};
	    $.post(BASETHEME_OBJECT.ajaxurl,parms,function(response) {
			var data = $.parseJSON(response);
			$('#'+editor_id).parent().html(data.message.editor);
		    quicktags({id : editor_id,buttons:"strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,dfw"});
			tinymce.init({
		        mode : "specific_textareas",
		        // selector : 'textarea',
		        // content_css : "PLUGINS_URL/cm-core/both-ends/css/tinymce.css",
		        elements : 'pre-details',
		        editor_selector : "tinymce",
		        skin: "lightgray",

		        statusbar : false,
		        plugins: "textcolor colorpicker",
		        textcolor_cols: "5",
		        browser_spellcheck: true,
		        // toolbar: [
		        // 	// "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image| forecolor backcolor"
		        //     "bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | undo redo | forecolor backcolor"
		        // ],
		        setup: function(editor) {
			        editor.on('blur', function(e) {
			        	// $('#'+editor_id).html(tinymce.activeEditor.getContent( { format : 'html' } ));
						$('#'+editor_id).html(tinymce.get(editor_id).getContent());
						// $("#"+editor_id).trigger('focusout');
						// tinymce.get(editor_id).setContent(tinymce.activeEditor.getContent( { format : 'html' } ));
			        });
			        editor.on('init', function () {
						tinymce.get(editor_id).setContent(content);
	                });
			    },
		    	style_formats: [
			        { title: 'Bold text', inline: 'strong' },
			        { title: 'Red text', inline: 'span', styles: { color: '#ff0000' } },
			        { title: 'Red header', block: 'h1', styles: { color: '#ff0000' } },
			        { title: 'Badge', inline: 'span', styles: { display: 'inline-block', border: '1px solid #2276d2', 'border-radius': '5px', padding: '2px 5px', margin: '0 2px', color: '#2276d2' } },
			        { title: 'Table row 1', selector: 'tr', classes: 'tablerow1' }
		    	],
				// formats: {
				// 	alignleft: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'left' },
				// 	aligncenter: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'center' },
				// 	alignright: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'right' },
				// 	alignfull: { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'full' },
				// 	bold: { inline: 'span', 'classes': 'bold' },
				// 	italic: { inline: 'span', 'classes': 'italic' },
				// 	underline: { inline: 'span', 'classes': 'underline', exact: true },
				// 	strikethrough: { inline: 'del' },
				// 	customformat: { inline: 'span', styles: { color: '#00ff00', fontSize: '20px' }, attributes: { title: 'My custom format' }, classes: 'example1' },
				// },
		    	paste_auto_cleanup_on_paste : true,
		        paste_postprocess : function( pl, o ) {
		            o.node.innerHTML = o.node.innerHTML.replace( /&nbsp;+/ig, " " );
		        }
		    });
	        tinymce.execCommand('mceFocus', false, editor_id );
	        tinymce.execCommand('mceRemoveEditor',false, editor_id);
	        tinymce.execCommand('mceAddEditor',false, editor_id);
	        tinymce.execCommand("mceAddControl", false, editor_id);
		});
	}
	
	function uniqid () {
	  function s4 () {
	    return Math.floor((1 + Math.random()) * 0x10000)
	      .toString(16)
	      .substring(1);
	  }
	  return s4() + s4() + s4();
	}

	// replace space in string with underscore
	$(document).on('keyup', '[data-no-space="true"]', function(event) {
		event.preventDefault();
		var newString;
		if (newString = $(this).val().replace(/\s/g, "_")) {
			$(this).val(newString);
		}
	});

	$(document).on('change', '.select-type', function(event) {
		event.preventDefault();
		if ($(this).val() == 'repeatable_group') {
			if ($(this).parents('td').find('table.nested-group').is(':hidden')) {
				$(this).parents('td').find('table.nested-group').show();
			}
			// var contents = $(this).parents('td').parents('table').html();

			// if ($(this).parents('td').length) {
			// 	$(this).parents('td').append(contents);
			// }
		}
	});

}) (jQuery);
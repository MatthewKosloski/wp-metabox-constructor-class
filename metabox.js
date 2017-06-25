(function($){
	'use strict';

	$(document).ready(function(){

		var image_frame;

		var $uploadBtn = $('.js-mcc-box-image-upload-button');

		$uploadBtn.on('click', function(e){
			e.preventDefault();

			var id = $(this).data('hidden-input');

			var $hiddenInput = $('#' + id);
			var $previewImage = $('#js-mcc-box-image-preview-' + id);

			if(image_frame !== undefined) {
				image_frame.open();
				return;
			}

			image_frame = wp.media.frames.image_frame = wp.media({
				library: {type: 'image'}
			});

			image_frame.on('select', function() {
				var attachment = image_frame.state().get('selection').first().toJSON();
				$hiddenInput
					.val(attachment.url);
				$previewImage
					.removeClass('is-hidden')
					.attr('src', attachment.url);
				$uploadBtn.text('Change Image');
			});

			image_frame.open();

		});
	});

})(jQuery);
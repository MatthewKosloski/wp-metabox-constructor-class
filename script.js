(function($){
	'use strict';

	$(document).ready(function(){

		var image_frame;

		$('.mcc-box__field-container').on('click', '.js-mcc-box-image-upload-button', function(e){
			e.preventDefault();

			var id = $(this).data('hidden-input').replace(/(\[|\])/g, '\\$1');

			if(image_frame !== undefined) {
				image_frame.open();
				return;
			}

			image_frame = wp.media.frames.image_frame = wp.media({
				library: {type: 'image'}
			});

			image_frame.on('select', function() {
				var attachment = image_frame.state().get('selection').first().toJSON();
				$('#image-'+id).val(attachment.url);

				$('#js-'+id+'-image-preview').removeClass('is-hidden').attr('src', attachment.url);

				$('.js-mcc-box-image-upload-button').text('Change Image');

				$('#'+id).css({background: 'red'});
			});

			image_frame.open();

		});

		$('.mcc-box__field-container').on('click', '.mcc-box-repeated-header', function(){
			$(this).siblings('.mcc-box__repeated-content').toggleClass('is-hidden');
		});

		$('.mcc-box__repeated-blocks').on('click', '.mcc-box__remove', function() {
			$(this).siblings('.mcc-box__repeated').remove();
			return false;
		});

		$('.mcc-box__repeated-blocks').sortable({
			opacity: 0.6, 
			revert: true, 
			cursor: 'move', 
			handle: '.js-mcc-box-sort'
		});

	});

})(jQuery);
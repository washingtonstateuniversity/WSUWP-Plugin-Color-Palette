(function($){
	$(document ).ready(function() {
		$('.wsu-palettes' ).on('click', '.admin-palette-option', function() {
			var selected_palette = $(this);
			$('#wsu-palette' ).val( selected_palette.data('palette') );
			$('.admin-palette-current' ).removeClass('admin-palette-current');
			selected_palette.addClass( 'admin-palette-current');
		})
	} );
}(jQuery));
jQuery(document).ready(function($) {
	var kudos = $('.kudo-list');
	if ( kudos.find('li').length > 3 ) {
		function scroll_kudos() {
			var last = kudos.find('li:last-child');
			last.hide();
			kudos.prepend( last ).find('li:first-child').fadeIn(700);
		}
		setInterval(scroll_kudos, 7000);
	}
});
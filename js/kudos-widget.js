/*jQuery(document).ready(function($) {

	var kudos = $( '.kudo-list li' ),
			total_kudos = kudos.length,
			timer = 7000;

	if ( total_kudos > 3 ) {

		function scroll_kudos() {
			
			$( '.kudo-list li:last-child' ).fadeIn( 700 );
			$( '.kudo-list' ).prepend( $( '.kudo-list li:last-child' ) );
   		setTimeout( scroll_kudos, timer );

		}

		setTimeout( scroll_kudos, timer/2 );

	}

});*/

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
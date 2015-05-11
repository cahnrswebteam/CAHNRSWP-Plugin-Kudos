<?php
/**
 * Adds Kudos widget.
 */
class CAHNRS_Kudos_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'cahnrs_kudos', // Base ID
			'Kudos', // Name
			array( 'description' => 'A widget for CAHNRS Kudos', ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {

		wp_enqueue_style( 'cahnrswp-kudos-style', plugins_url( 'css/kudos.css', dirname(__FILE__) ) );
		wp_enqueue_script( 'cahnrswp-kudos-script', plugins_url( 'js/kudos-widget.js', dirname(__FILE__) ), array( 'jquery' ) );

		echo $args['before_widget'];

		echo '<img class="cahnrs-kudos-banner" src="' . plugins_url( 'images/banner.gif', dirname(__FILE__) ) . '" height="184" width="822" />';


		echo '<div class="kudos">';

		// The Loop
		$kudos = array(
			'post_type' => 'kudos',
			/*'date_query' => array(
				array(
					'year'	=> date('Y'),
					'month' => date('m'),
				),
			),*/
		);

		$kudos_query = new WP_Query( $kudos );

		if ( $kudos_query->have_posts() ) {
			//echo '<h2>' . date( 'F' ) . '</h2>';
			echo '<ul class="kudo-list">';
			while ( $kudos_query->have_posts() ) {
				$kudos_query->the_post();
				$category = wp_get_post_terms( $kudos_query->post->ID, 'kudo_categories', array( 'fields' => 'names' ) );
				$mark = wp_get_post_terms( $kudos_query->post->ID, 'kudo_marks', array( 'fields' => 'slugs' ) );
				echo '<li class="kudo-marks-' . $mark[0] . '"><strong><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></strong>, ' . get_the_date( 'F j' );
				if ( $category ) {
					echo ', ' . $category[0];
				}
				if ( 'yes' !== get_post_meta( $kudos_query->post->ID, '_cahnrswp_kudo_sub_anonymous', true ) ) {
					echo ' | ' . 'Submitted by ' . $sub_name = get_post_meta( $kudos_query->post->ID, '_cahnrswp_kudo_sub_name', true );
				}
				echo "<br />\n" . get_the_content() . '</li>';
			}
			echo '</ul>';
		}
		wp_reset_postdata();

		echo '</div>';

		if ( is_active_sidebar( 'kudos-sidebar' ) ) {
			dynamic_sidebar( 'kudos-sidebar' );
		}

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		echo '<p>There are no options for this widget.</p>';
	}

}

register_widget( 'CAHNRS_Kudos_Widget' );
<?php
/**
 * Adds Kudos widget.
 */
class CAHNRS_Kudos_Sidebar_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'cahnrs_kudos_sidebar', // Base ID
			'Kudos Sidebar', // Name
			array( 'description' => 'CAHNRS Kudos nomination link and archive listing', ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {

		wp_enqueue_style( 'cahnrswp-kudos-sidebar-style', plugins_url( 'css/kudos-sidebar.css', dirname(__FILE__) ) );

		echo $args['before_widget'];

		echo '<a href="' . $instance['link'] . '" class="give-kudos">Recognize a Colleague</a>';

		echo '<ul>';

		add_filter( 'get_archives_link', array( $this, 'get_archives_kudos_link' ), 10, 2 );

		wp_get_archives( array(
			'post_type' => 'kudos',
			)
		);

		remove_filter( 'get_archives_link', array( $this, 'get_archives_kudos_link' ), 10, 2 );

		echo '</ul>';

		echo $args['after_widget'];
	}

	/**
	 * Filter for Kudos archive.
	 */
	public function get_archives_kudos_link( $link ) {
		return str_replace( get_site_url(), get_site_url() . '/kudos', $link );
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$link = ! empty( $instance['link'] ) ? $instance['link'] : '';
		?>
		<p><label for="<?php echo $this->get_field_id( 'link' ); ?>">Submission form page URL</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['link'] = ( ! empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : '';
		return $instance;
	}

}

register_widget( 'CAHNRS_Kudos_Sidebar_Widget' );
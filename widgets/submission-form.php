<?php
/**
 * Adds Kudos widget.
 */
class CAHNRS_Kudos_Submission_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'cahnrs_kudos_submission_widget', // Base ID
			'Kudos Submission Form', // Name
			array( 'description' => 'A widget for the CAHNRS Kudos submission form', ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {

		wp_enqueue_style( 'cahnrswp-kudos-submission-style', plugins_url( 'css/kudos-submission.css', dirname(__FILE__) ) );

		echo '<img class="cahnrs-kudos-banner" src="' . plugins_url( 'images/banner.gif', dirname(__FILE__) ) . '" height="184" width="822" />';

		echo $args['before_widget'];

		if ( $_POST['submit'] ) {
			$this->process_kudos_submission( $instance['email'] );
		} else {
			$this->kudos_submission_form();
		}

		echo $args['after_widget'];

	}

	/**
	 * If the submit button is clicked, create a post and send an email.
	 */
	public function kudos_submission_form() {
		?>
		<form action="" method="post" id="kudos-submission-form">

			<h2>Nominee information</h2>

			<p><label for="kudo-nominee-name">Name <span class="required">*</span></label><br />
			<input type="text" pattern="[a-zA-Z ]+" id="kudo-nominee-name" name="kudo-nominee-name" value="<?php echo ( isset( $_POST['kudo-nominee-name'] ) ? esc_attr( $_POST['kudo-nominee-name'] ) : '' ); ?>" /></p>

			<p><label for="kudo-nominee-title">Title <span class="required">*</span></label><br />
			<input type="text" pattern="[a-zA-Z0-9 ]+" id="kudo-nominee-title" name="kudo-nominee-title" value="<?php echo ( isset( $_POST['kudo-nominee-title'] ) ? esc_attr( $_POST['kudo-nominee-title'] ) : '' ); ?>" /></p>

			<p><label for="kudo-nominee-dept">Department <span class="required">*</span></label><br />
			<input type="text" pattern="[a-zA-Z ]+" id="kudo-nominee-dept" name="kudo-nominee-dept" value="<?php echo ( isset( $_POST['kudo-nominee-dept'] ) ? esc_attr( $_POST['kudo-nominee-dept'] ) : '' ); ?>" /></p>

			<p><label for="kudo-nominee-email">Email <span class="required">*</span></label><br />
			<input type="email" id="kudo-nominee-email" name="kudo-nominee-email" value="<?php echo ( isset( $_POST['kudo-nominee-email'] ) ? esc_attr( $_POST['kudo-nominee-email'] ) : '' ); ?>" /></p>

			<p><label for="kudo-nominee-content">Please describe why you would like to recognize this person. <span class="required">*</span></label><br />
			<textarea id="kudo-nominee-content" name="kudo-nominee-content"><?php echo ( isset( $_POST['kudo-nominee-content'] ) ? stripslashes( wp_kses_post( $_POST['kudo-nominee-content'] ) ) : '' ); ?></textarea></p>

			<p class="kudos-marks">Select an icon to show with this kudo.<br />
			<?php
				$kudos_marks = get_terms( 'kudo_marks', 'hide_empty=0' );
				if ( ! empty( $kudos_marks ) && ! is_wp_error( $kudos_marks ) ) :
					foreach ( $kudos_marks as $kudos_mark ) :
					?>
          	<input type="radio" id="<?php echo $kudos_mark->slug; ?>-radio" name="kudo-nominee-mark" value="<?php echo esc_attr( $kudos_mark->term_id ); ?>"<?php echo ( isset( $_POST['kudo-nominee-mark'] ) && esc_attr( $kudos_mark->term_id ) === $_POST['kudo-nominee-mark'] ? ' checked="checked"' : '' ); ?>>
            <label for="<?php echo $kudos_mark->slug; ?>-radio" class="kudo-marks-<?php echo $kudos_mark->slug; ?>"></label>
					<?php
					endforeach;
				endif;
			?>
			</p>

			<h2>Submitter information (that's you!)</h2>

			<p><label for="kudo-submitter-name">Name <span class="required">*</span></label><br />
				<input type="text" pattern="[a-zA-Z ]+" id="kudo-submitter-name" name="kudo-submitter-name" value="<?php echo ( isset( $_POST['kudo-submitter-name'] ) ? esc_attr( $_POST['kudo-submitter-name'] ) : '' ) ?>" /></p>

			<p><label for="kudo-submitter-title">Title <span class="required">*</span></label><br />
				<input type="text" pattern="[a-zA-Z0-9 ]+" id="kudo-submitter-title" name="kudo-submitter-title" value="<?php echo ( isset( $_POST['kudo-submitter-title'] ) ? esc_attr( $_POST['kudo-submitter-title'] ) : '' ) ?>" /></p>

			<p><label for="kudo-submitter-dept">Department <span class="required">*</span></label><br />
			<input type="text" pattern="[a-zA-Z ]+" id="kudo-submitter-dept" name="kudo-submitter-dept" value="<?php echo ( isset( $_POST['kudo-submitter-dept'] ) ? esc_attr( $_POST['kudo-submitter-dept'] ) : '' ) ?>" /></p>

			<p><label for="kudo-submitter-email">Email <span class="required">*</span></label><br />
			<input type="email" id="kudo-submitter-email" name="kudo-submitter-email" value="<?php echo ( isset( $_POST['kudo-submitter-email'] ) ? esc_attr( $_POST['kudo-submitter-email'] ) : '' ) ?>" /></p>

			<p><label for="kudo-submitter-anonymous"><input type="checkbox" value="yes" id="kudo-submitter-anonymous" name="kudo-submitter-anonymous" /> Check this box if you would prefer this kudo to be anonymous.</label></p>

			<p class="submit"><input type="submit" id="submit-kudo" name="submit" value="Submit Kudo!" /></p>

		</form>
		<?php
	}

	/**
	 * If the submit button is clicked, create a post and send an email.
	 */
	public function process_kudos_submission( $receiver ) {

		if ( $_POST['kudo-nominee-name'] && $_POST['kudo-nominee-title'] && $_POST['kudo-nominee-dept'] && $_POST['kudo-nominee-email'] && $_POST['kudo-nominee-content'] && $_POST['kudo-submitter-name'] && $_POST['kudo-submitter-title'] && $_POST['kudo-submitter-dept'] && $_POST['kudo-submitter-email'] ) :

			// sanitize form values
			$nominee_name     = sanitize_text_field( $_POST['kudo-nominee-name'] );
			$nominee_title    = sanitize_text_field( $_POST['kudo-nominee-title'] );
			$nominee_dept     = sanitize_text_field( $_POST['kudo-nominee-dept'] );
			$nominee_email    = sanitize_email( $_POST['kudo-nominee-email'] );
			$content          = wp_kses_post( $_POST['kudo-nominee-content'] );
			//$nominee_category = sanitize_text_field( $_POST['kudo-nominee-category'] );
			$nominee_mark     = sanitize_text_field( $_POST['kudo-nominee-mark'] );
			$submitter_name   = sanitize_text_field( $_POST['kudo-submitter-name'] );
			$submitter_title  = sanitize_text_field( $_POST['kudo-submitter-title'] );
			$submitter_dept   = sanitize_text_field( $_POST['kudo-submitter-dept'] );
			$submitter_email  = sanitize_email( $_POST['kudo-submitter-email'] );

			// Create the post
			$new_post = array(
				'post_content'   => $content,
				'post_name'      => $nominee_name,
				'post_title'     => $nominee_name,
				'post_status'    => 'draft',
				'post_type'      => 'kudos',
				//'post_author'    => 8, // Probably hook into receiver email
				'ping_status'    => 'closed',
				'comment_status' => 'closed',
			);
			$post_id = wp_insert_post( $new_post, $wp_error );

			//wp_set_object_terms( $post_id, (int) ( $nominee_category ), 'kudo_categories' );
			wp_set_object_terms( $post_id, (int) ( $nominee_mark ), 'kudo_marks' );
			add_post_meta( $post_id, '_cahnrswp_kudo_nom_title', $nominee_title );
			add_post_meta( $post_id, '_cahnrswp_kudo_nom_dept', $nominee_dept );
			add_post_meta( $post_id, '_cahnrswp_kudo_nom_email', $nominee_email );
			add_post_meta( $post_id, '_cahnrswp_kudo_sub_name', $submitter_name );
			add_post_meta( $post_id, '_cahnrswp_kudo_sub_title', $submitter_title );
			add_post_meta( $post_id, '_cahnrswp_kudo_sub_dept', $submitter_dept );
			add_post_meta( $post_id, '_cahnrswp_kudo_sub_email', $submitter_email );
			if ( $_POST['kudo-submitter-anonymous'] ) { add_post_meta( $post_id, '_cahnrswp_kudo_sub_anonymous', 'yes' ); }

			$to = $receiver;
			$subject = $submitter_name . ' has submitted a kudo for ' . $nominee_name;
			$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
			$message = 'Please review this submission at <a href="' . $edit_link . '">' . $edit_link . '</a>.';
			//$headers = "From: $submitter_name <$submitter_email>" . "\r\n";

			// If email has been processed for sending, display a success message.
			add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
			if ( wp_mail( $to, $subject, $message, $headers ) ) {
				echo '<p><strong>Thank you for taking the time to recognize ' . esc_html( $nominee_name ) . '! Your submission will be reviewed and posted shortly.</strong></p>';
			} else {
				echo '<p class="error"><strong>An unexpected error occurred.<strong></p>';
			}
			remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		else :

			echo '<p class="error"><strong>Sorry, it looks like you forgot some required fields.</strong></p>';

			$this->kudos_submission_form();

		endif;

	}

	/**
	 * Filter for sending HTML emails.
	 */
	public function set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$email = ! empty( $instance['email'] ) ? $instance['email'] : '';
		?>
		<label for="<?php echo $this->get_field_id( 'email' ); ?>">Email address of Kudo moderator</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['email'] = ( ! empty( $new_instance['email'] ) ) ? strip_tags( $new_instance['email'] ) : '';
		return $instance;
	}

}

register_widget( 'CAHNRS_Kudos_Submission_Widget' );
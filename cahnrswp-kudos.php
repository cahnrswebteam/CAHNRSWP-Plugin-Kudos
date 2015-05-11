<?php
/*
Plugin Name: CAHNRS Kudos
Plugin URI:	http://cahnrs.wsu.edu/communications/
Description: A custom post type, taxonomy, and widgets for CAHNRS Kudos.
Author:	CAHNRS, philcable
Version: 0.1.0
*/

class CAHNRSWP_Kudos {

	/**
	 * @var string Plugin version number.
	 */
	var $cahnrs_kudos_version = '0.1.0';

	/**
	 * @var string "Kudos" post type slug.
	 */
	var $cahnrs_kudos_post_type = 'kudos';

	/**
	 * @var string Taxonomy slug for kudo cagetories.
	 */
	var $cahnrs_kudos_categories = 'kudo_categories';

	/**
	 * @var string Taxonomy slug for kudo marks.
	 */
	var $cahnrs_kudos_marks = 'kudo_marks';

	/**
	 * @var array Fields used to capture additional Kudo information.
	 */
	var $cahnrs_kudos_fields = array(
		'_cahnrswp_kudo_nom_title',
		'_cahnrswp_kudo_nom_dept',
		'_cahnrswp_kudo_nom_email',
		'_cahnrswp_kudo_sub_name',
		'_cahnrswp_kudo_sub_title',
		'_cahnrswp_kudo_sub_dept',
		'_cahnrswp_kudo_sub_email',
	);

	/**
	 * Fire necessary hooks when instantiated.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_kudos_post_type' ), 11 );
		add_action( 'init', array( $this, 'register_kudos_taxonomies' ), 11 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_filter( 'template_include', array( $this, 'template_include' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 11 );
		add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 ); // Can allegedly remove upon updating to 4.2.x
		add_filter( 'getarchives_where', array( $this, 'getarchives_where_filter' ), 10, 2 );
		add_filter( 'generate_rewrite_rules', array( $this, 'generate_kudos_rewrite_rules' ) );
	}

	/**
	 * Register the Kudos post type.
	 */
	public function register_kudos_post_type() {
		$args = array(
			'labels' => array(
				'name' => 'Kudos',
				'singular_name' => 'Kudo',
				'all_items' => 'All Kudos',
				'view_item' => 'View Kudo',
				'add_new_item' => 'Add New Kudo',
				'add_new' => 'Add New',
				'edit_item' => 'Edit Kudo',
				'update_item' => 'Update Kudo',
				'search_items' => 'Search Kudos',
				'not_found' => 'Not found',
				'not_found_in_trash' => 'Not found in Trash',
			),
			'description' => 'CAHNRS Kudos',
			'public' => true,
			'hierarchical' => false,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-awards',
			'supports' => array(
				'title',
				'editor',
				'author',
			),
			'taxonomies' => array(
				$this->cahnrs_kudos_categories,
				$this->cahnrs_kudos_marks,
			),
			'has_archive' => true,
			'rewrite' => true,
		);
		register_post_type( $this->cahnrs_kudos_post_type, $args );
	}

	/**
	 * Register Kudos taxonomies.
	 */
	public function register_kudos_taxonomies() {
		$category_args = array(
			'labels'       => array(
				'name'					=> 'Categories',
				'singular_name' => 'Category',
				'search_items'	=> 'Search Categories',
				'all_items'		  => 'All Categories',
				'edit_item'		  => 'Edit Category',
				'update_item'	  => 'Update Category',
				'add_new_item'	=> 'Add New Category',
				'new_item_name' => 'New Category Name',
				'menu_name'		  => 'Categories',
			),
			'description'	 => 'CAHNRS Kudos categories',
			'public'			 => true,
			'hierarchical' => true,
			'show_ui'			 => true,
			'show_in_menu' => true,
			'query_var'		 => $this->cahnrs_kudos_categories,
		);
		register_taxonomy( $this->cahnrs_kudos_categories, array( $this->cahnrs_kudos_post_type ), $category_args );
		$mark_args = array(
			'labels'       => array(
				'name'          => 'Marks',
				'singular_name' => 'Mark',
				'search_items'  => 'Search Marks',
				'all_items'     => 'All Marks',
				'edit_item'     => 'Edit Mark',
				'update_item'	  => 'Update Mark',
				'add_new_item'  => 'Add New Mark',
				'new_item_name' => 'New Mark Name',
				'menu_name'     => 'Marks',
			),
			'description'  => 'CAHNRS Kudos marks',
			'public'       => true,
			'hierarchical' => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'query_var'    => $this->cahnrs_kudos_marks,
		);
		register_taxonomy( $this->cahnrs_kudos_marks, array( $this->cahnrs_kudos_post_type ), $mark_args );
	}

	/**
	 * Fields to capture nominee and submitter information.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( $this->cahnrs_kudos_post_type !== $post_type ) {
			return;
		}
		add_meta_box(
			'cahnrswp_kudos_info',
			'Nominee and Submitter Information',
			array( $this, 'kudo_info_meta_box' ),
			$this->cahnrs_kudos_post_type,
			'advanced',
			'high'
		);
	}

	/**
	 * Display a meta box used to show a person's "card".
	 */
	public function kudo_info_meta_box( $post ) {
		wp_nonce_field( 'cahnrswp_kudo', 'cahnrswp_kudo_nonce' );
		$nom_title = get_post_meta( $post->ID, '_cahnrswp_kudo_nom_title', true );
		$nom_dept  = get_post_meta( $post->ID, '_cahnrswp_kudo_nom_dept', true );
		$nom_email = get_post_meta( $post->ID, '_cahnrswp_kudo_nom_email', true );
		$sub_name  = get_post_meta( $post->ID, '_cahnrswp_kudo_sub_name', true );
		$sub_title = get_post_meta( $post->ID, '_cahnrswp_kudo_sub_title', true );
		$sub_dept  = get_post_meta( $post->ID, '_cahnrswp_kudo_sub_dept', true );
		$sub_email = get_post_meta( $post->ID, '_cahnrswp_kudo_sub_email', true );
		$sub_anon  = get_post_meta( $post->ID, '_cahnrswp_kudo_sub_anonymous', true );
		
		?>
		<p style="margin-bottom: 0;"><strong>Nominee</strong></p>
		<p style="float: left; padding-right: 1%; width: 49%;"><label for="_cahnrswp_kudo_nom_title">Title</label><br />
		<input type="text" id="_cahnrswp_kudo_nom_title" name="_cahnrswp_kudo_nom_title" value="<?php echo esc_attr( $nom_title ); ?>" class="widefat" /></p>
		<p style="float: left; padding-left: 1%; width: 49%;"><label for="_cahnrswp_kudo_nom_dept">Department</label><br />
		<input type="text" id="_cahnrswp_kudo_nom_dept" name="_cahnrswp_kudo_nom_dept" value="<?php echo esc_attr( $nom_dept ); ?>" class="widefat" /></p>
		<p style="float: left; padding-right: 1%; width: 49%;"><label for="_cahnrswp_kudo_nom_email">Email</label><br />
		<input type="text" id="_cahnrswp_kudo_nom_email" name="_cahnrswp_kudo_nom_email" value="<?php echo esc_attr( $nom_email ); ?>" class="widefat" /></p>
		<br style="clear:both;" />
		<p style="margin-bottom: 0;"><strong>Submitter</strong></p>
		<p style="float: left; padding-right: 1%; width: 49%;"><label for="_cahnrswp_kudo_sub_name">Name</label><br />
		<input type="text" id="_cahnrswp_kudo_sub_name" name="_cahnrswp_kudo_sub_name" value="<?php echo esc_attr( $sub_name ); ?>" class="widefat" /></p>
		<p style="float: left; padding-left: 1%; width: 49%;"><label for="_cahnrswp_kudo_sub_title">Title</label><br />
		<input type="text" id="_cahnrswp_kudo_sub_title" name="_cahnrswp_kudo_sub_title" value="<?php echo esc_attr( $sub_title ); ?>" class="widefat" /></p>
		<p style="float: left; padding-right: 1%; width: 49%;"><label for="_cahnrswp_kudo_sub_dept">Department</label><br />
		<input type="text" id="_cahnrswp_kudo_sub_dept" name="_cahnrswp_kudo_sub_dept" value="<?php echo esc_attr( $sub_dept ); ?>" class="widefat" /></p>
		<p style="float: left; padding-left: 1%; width: 49%;"><label for="_cahnrswp_kudo_sub_email">Email</label><br />
		<input type="text" id="_cahnrswp_kudo_sub_email" name="_cahnrswp_kudo_sub_email" value="<?php echo esc_attr( $sub_email ); ?>" class="widefat" /></p>
		<br style="clear:both;" />
    <p><label for="_cahnrswp_kudo_sub_anonymous"><input type="checkbox" value="yes" id="_cahnrswp_kudo_sub_anonymous" name="_cahnrswp_kudo_sub_anonymous" <?php checked( $sub_anon, 'yes' ); ?> /> Anonymous Submission</label></p>
		<?php
	}

	/**
	 * Save post meta data.
	 */
	public function save_post( $post_id ) {
		if ( ! isset( $_POST['cahnrswp_kudo_nonce'] ) ) {
			return $post_id;
		}
		//$nonce = $_POST['cahnrswp_kudo_nonce'];
		if ( ! wp_verify_nonce( $_POST['cahnrswp_kudo_nonce'], 'cahnrswp_kudo' ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// Sanitize and save custom fields.
		foreach ( $this->cahnrs_kudos_fields as $field ) {
			if ( isset( $_POST[ $field ] ) && '' != $_POST[ $field ] ) {
				update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
			} else {
				delete_post_meta( $post_id, $field );
			}
		}

		if ( isset( $_POST['_cahnrswp_kudo_sub_anonymous'] ) ) {
			update_post_meta( $post_id, '_cahnrswp_kudo_sub_anonymous', 'yes' );
		} else {
			delete_post_meta( $post_id, '_cahnrswp_kudo_sub_anonymous' );
		}

	}

	/**
	 * Register widgets and a sidebar.
	 */
	public function widgets_init() {
		include plugin_dir_path( __FILE__ ) . 'widgets/kudos.php';
		include plugin_dir_path( __FILE__ ) . 'widgets/submission-form.php';
		include plugin_dir_path( __FILE__ ) . 'widgets/sidebar.php';
		register_sidebar( array(
			'name' => 'Kudos Sidebar',
			'id' => 'kudos-sidebar',
			'description' => 'Widgets in this area will be shown on Kudos archives.',
			'before_widget' => '<aside id="%1$s2" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<header>',
			'after_title'   => '</header>',
    ) );
	}

	/**
	 * Add archive templates for the Kudos post type.
	 */
	public function template_include( $template ) {
		if ( $this->cahnrs_kudos_post_type === get_post_type() ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/single.php';
		}
		if ( is_post_type_archive( $this->cahnrs_kudos_post_type ) ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/archive.php';
		}
		return $template;
	}

	/**
	 * Enqueue the stylesheet used on Kudos archives.
	 */
	public function wp_enqueue_scripts() {
		if ( $this->cahnrs_kudos_post_type === get_post_type() ) {
			wp_enqueue_style( 'cahnrswp-kudo-style', plugins_url( 'css/kudo.css', __FILE__ ) );
		}
		if ( is_post_type_archive( $this->cahnrs_kudos_post_type ) ) {
			wp_enqueue_style( 'cahnrswp-kudos-style', plugins_url( 'css/kudos.css', __FILE__ ) );
		}
	}

	/** 
	 * Add 'kudos-marks-{slug}" class to Kudos archive articles.
	 */
	public function post_class( $classes, $class, $ID ) {
		if ( $this->cahnrs_kudos_post_type == get_post_type( (int) $ID ) ) {
			$terms = get_the_terms( (int) $ID, $this->cahnrs_kudos_marks );
			if ( $terms ) {
				foreach( $terms as $term ) {
					$classes[] = 'kudo-marks-' . $term->slug;
				}
			}
		}
		return $classes;
	}

	/**
	 * Custom post type getarchives_where_filter.
	 */
	public function getarchives_where_filter( $where, $args ) {
		if ( isset( $args[ 'post_type' ] ) ) {
			$where = "WHERE post_type = '$args[post_type]' AND post_status = 'publish'";
		}
		return $where;
	}

	/**
	 * Rewrite rules for Kudos.
	 */
	public function generate_kudos_rewrite_rules( $wp_rewrite ) {
		$kudos_rules = array(
			//$this->cahnrs_kudos_post_type . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$' => 'index.php?post_type=' . $this->cahnrs_kudos_post_type . '&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]',
			$this->cahnrs_kudos_post_type . '/([0-9]{4})/([0-9]{1,2})/?$' => 'index.php?post_type=' . $this->cahnrs_kudos_post_type . '&year=$matches[1]&monthnum=$matches[2]',
			$this->cahnrs_kudos_post_type . '/([0-9]{4})/?$' => 'index.php?post_type=' . $this->cahnrs_kudos_post_type . '&year=$matches[1]'
		);
		$wp_rewrite->rules = $kudos_rules + $wp_rewrite->rules;
	}

}

new CAHNRSWP_Kudos();
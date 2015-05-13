<?php
/*
Plugin Name: WSU Color Palette
Version: 0.0.0
Description: Assign a color palette to individual pages for use in styling sections of a site.
Author: washingtonstateuniversity, jeremyfelt
Author URI: https://web.wsu.edu/
Plugin URI: https://web.wsu.edu/
*/

class WSU_Color_Palette {

	/**
	 * @var array List of color palettes available for pages.
	 */
	static $color_palettes = array(
		'crimson' => array( 'name' => 'Crimson', 'hex' => '#981e32' ),
		'gray'    => array( 'name' => 'Gray',    'hex' => '#5e6a71' ),
		'green'   => array( 'name' => 'Green',   'hex' => '#8f7e35' ),
		'yellow'  => array( 'name' => 'Yellow',  'hex' => '#c69214' ),
		'blue'    => array( 'name' => 'Blue',    'hex' => '#82a9af' ),
		'orange'  => array( 'name' => 'Orange',  'hex' => '#b67233' ),
	);

	/**
	 * @var string The meta key used to track a page's color palette.
	 */
	static $color_palette_meta_key = '_wsu_color_palette';

	/**
	 * Setup hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_filter( 'body_class', array( $this, 'add_body_class' ), 11 );
	}

	/**
	 * Return a filtered list of color palettes.
	 *
	 * @return array
	 */
	private static function get_color_palettes() {
		$palettes = apply_filters( 'wsu_color_palette_values', self::$color_palettes );

		$defaults = array( 'default' => array( 'name' => 'Default', 'hex' => '#ffffff' ) );

		$palettes = array_merge( $defaults, $palettes );

		return $palettes;
	}

	/**
	 * Configure the meta boxes to display for capturing palette.
	 *
	 * @param string $post_type The current post's post type.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( 'page' === $post_type ) {
			add_meta_box( 'wsu_color_palette', 'Select Color Palette', array( $this, 'display_color_palette_meta_box' ), null, 'normal', 'default' );
		}
	}

	/**
	 * Display the meta box to capture color palette information.
	 *
	 * @param WP_Post $post
	 */
	public function display_color_palette_meta_box( $post ) {
		$current_palette = get_post_meta( $post->ID, $this::$color_palette_meta_key, true );

		if ( ! array_key_exists( $current_palette, $this::get_color_palettes() ) ) {
			$current_palette = 'default';
		}

		?>
		<ul class="wsu-palettes">
			<?php

			foreach( $this::get_color_palettes() as $key => $palette ) {
				if ( $current_palette === $key ) {
					$class = ' admin-palette-current';
				} else {
					$class = '';
				}
				echo '<li data-palette="' . esc_attr( $key ) . '" class="admin-palette-option' . $class . '" style="background-color: ' . esc_attr( $palette['hex'] ) . ';"></li>';
			}

			?>
		</ul>
		<input type="hidden" name="wsu_palette" id="wsu-palette" value="<?php echo esc_attr( $current_palette ); ?>" />
	<?php
	}

	/**
	 * Enqueue styles to be used for the display of taxonomy terms.
	 */
	public function admin_enqueue_scripts() {
		if ( 'page' === get_current_screen()->id ) {
			wp_enqueue_style( 'wsu-palette-admin', plugins_url( '/css/admin.css', __FILE__ ), array(), wsuwp_global_version() );
			wp_enqueue_script( 'wsu-palette-admin-js', plugins_url( '/js/admin.js', __FILE__ ), array( 'jquery' ), wsuwp_global_version() );
		}

	}

	/**
	 * Assign the selected color palette to the page.
	 *
	 * @param int     $post_id The ID of the post being saved.
	 * @param WP_Post $post    The full post object being saved.
	 */
	public function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		if ( ! isset( $_POST['wsu_palette'] ) ) {
			return;
		}

		$this::assign_color_palette( $_POST['wsu_palette'], $post_id );
	}

	/**
	 * Assign a given valid palette to a page.
	 *
	 * @param string $palette
	 * @param int    $post_id
	 *
	 * @return bool
	 */
	static function assign_color_palette( $palette, $post_id ) {
		if ( ! array_key_exists( $palette, self::get_color_palettes() ) ) {
			return false;
		}

		$new_palette = sanitize_key( $palette );
		update_post_meta( $post_id, self::$color_palette_meta_key, $new_palette );

		return true;
	}

	/**
	 * Assign a color palette to a page's view.
	 *
	 * @param array $classes List of classes to assign to this view's body element.
	 *
	 * @return array Modified list of classes.
	 */
	public function add_body_class( $classes ) {
		if ( is_singular( 'page' ) ) {
			$palette = get_post_meta( get_the_ID(), $this::$color_palette_meta_key, true );
			if ( ! array_key_exists( $palette, $this::get_color_palettes() ) ) {
				$palette = 'default';
			}

			$classes[] = 'wsu-palette-' . $palette;
			$classes[] = 'wsu-palette-text-' . $palette;
		} else {
			$classes[] = 'wsu-palette-default';
			$classes[] = 'wsu-palette-text-default';
		}

		return $classes;
	}
}
new WSU_Color_Palette();
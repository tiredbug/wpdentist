<?php
/**
 * Handles custom post meta boxes for the 'wpdentist_item' post type.
 *
 * @package    WP_Dentist
 * @subpackage Admin
 * @since      1.0.0
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2013 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/plugins/wpdentist
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

final class RP_WP_Dentist_Settings {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Settings page name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $settings_page = '';

	/**
	 * Holds an array the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $settings = array();

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Sets up custom admin menus.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		$this->settings_page = add_submenu_page( 
			'edit.php?post_type=wpdentist_item',
			__( 'WP Dentist Settings', 'wpdentist' ),
			__( 'Settings',            'wpdentist' ),
			apply_filters( 'wpdentist_settings_capability', 'manage_options' ),
			'wpdentist-settings',
			array( $this, 'settings_page' )
		);

		if ( !empty( $this->settings_page ) ) {

			/* Register the plugin settings. */
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			/* Add media for the settings page. */
		//	add_action( 'admin_enqueue_scripts',             array( $this, 'enqueue_scripts' ) );
		//	add_action( "admin_head-{$this->settings_page}", array( $this, 'print_scripts'   ) );

			/* Load the meta boxes. */
		//	add_action( 'add_meta_boxes_wpdentist', array( $this, 'add_meta_boxes' ) );
		}
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function register_settings() {

		$this->settings = get_option( 'wpdentist_settings', rp_get_default_settings() );

		register_setting( 'wpdentist_settings', 'wpdentist_settings', array( $this, 'validate_settings' ) );

		add_settings_section( 
			'rp_section_menu', 
			__( 'Menu Settings', 'wpdentist' ), 
			array( $this, 'section_menu' ),
			$this->settings_page
		);

		add_settings_field(
			'rp_field_menu_title',
			__( 'Menu Archive Title', 'wpdentist' ),
			array( $this, 'field_menu_title' ),
			$this->settings_page,
			'rp_section_menu'
		);

		add_settings_field(
			'rp_field_menu_description',
			__( 'Menu Archive Description', 'wpdentist' ),
			array( $this, 'field_menu_description' ),
			$this->settings_page,
			'rp_section_menu'
		);
	}

	/**
	 * Validates the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function validate_settings( $settings ) {

		$settings['wpdentist_item_archive_title'] = strip_tags( $settings['wpdentist_item_archive_title'] );

		/* Kill evil scripts. */
		if ( !current_user_can( 'unfiltered_html' ) )
			$settings['wpdentist_item_description'] = stripslashes( wp_filter_post_kses( addslashes( $settings['wpdentist_item_description'] ) ) );

		/* Return the validated/sanitized settings. */
		return $settings;
	}

	/**
	 * Displays the menu settings section.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function section_menu() { ?>

		<p class="description">
			<?php printf( __( "Your wpdentist's menu is located at %s.", 'wpdentist' ), '<a href="' . get_post_type_archive_link( 'wpdentist' ) . '"><code>' . get_post_type_archive_link( 'wpdentist_item' ) . '</code></a>' ); ?>
		</p>
	<?php }

	/**
	 * Displays the menu title field.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function field_menu_title() { ?>

		<p>
			<input type="text" class="regular-text" name="wpdentist_settings[wpdentist_item_archive_title]" id="wpdentist_settings-wpdentist_item_archive_title" value="<?php echo esc_attr( $this->settings['wpdentist_item_archive_title'] ); ?>" />
		</p>
	<?php }

	/**
	 * Displays the menu description field.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function field_menu_description() { ?>

		<p>
			<textarea class="large-text" name="wpdentist_settings[wpdentist_item_description]" id="wpdentist_settings-wpdentist_item_description" rows="4"><?php echo esc_textarea( $this->settings['wpdentist_item_description'] ); ?></textarea>
			<span class="description"><?php _e( "Custom description for your wpdentist's menu. You may use <abbr title='Hypertext Markup Language'>HTML</abbr>. Your theme may or may not display this description.", 'wpdentist' ); ?></span>
		</p>
	<?php }

	/**
	 * Renders the settings page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function settings_page() { ?>

		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e( 'WP Dentist Settings', 'wpdentist' ); ?></h2>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php settings_fields( 'wpdentist_settings' ); ?>
				<?php do_settings_sections( $this->settings_page ); ?>
				<?php submit_button( esc_attr__( 'Update Settings', 'wpdentist' ), 'primary' ); ?>
			</form>

		</div><!-- wrap -->
	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

RP_WP_Dentist_Settings::get_instance();

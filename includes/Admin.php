<?php
namespace SCRNSA;

use SCRNSA\Vendor\WOAdminFramework\WOAdmin;
use SCRNSA\Vendor\WOAdminFramework\WOSettings;

/**
 * Admin class that sets up settings page, menu items, misc features.
 * Extends WOAdmin framework class.
 */
class Admin extends WOAdmin {
	/**
	 * WOSettings framework instance.
	 *
	 * @var WOSettings
	 */
	protected $sf;

	/**
	 * The custom admin menu hooks created during the admin_menu hook.
	 *
	 * @var array
	 */
	public $admin_menu_hooks;

	/**
	 * __construct
	 */
	public function __construct() {
		$this->admin_menu_hooks = array();
	}

	/**
	 * Create hooks.
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'check_if_enabled' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );

		add_filter( 'plugin_action_links_' . SCRNSA_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Add links to the plugin screen.
	 *
	 * @param array $links Existing links passed into this function.
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$action_links = array(
			array(
				'title' => __( 'Settings', 'ad-commander' ),
				'url'   => self::settings_admin_url(),
			),
		);

		$plugin_links = array();

		foreach ( $action_links as $action_link ) {
			$plugin_links[ Util::ns( sanitize_title( $action_link['title'] ), '_' ) ] = '<a href="' . esc_url( $action_link['url'] ) . '">' . esc_html( $action_link['title'] ) . '</a>';
		}

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Create a new WOSettings instance if necessary.
	 *
	 * @return WOSettings
	 */
	protected function sf() {
		if ( ! $this->sf ) {
			$this->sf = new WOSettings( Plugin::ns() );
		}

		return $this->sf;
	}

	public function check_if_enabled() {
		if ( ! Options::instance()->get( 'enable_stay_awake', 'general', true ) ) :
			?>
			<div class="notice notice-warning">
				<p>
					<?php
					/* translators: %1$s open anchor tag, %2$s close anchor tag. */
					printf( esc_html__( 'Screen Stay Awake script is not currently enabled. %1$sUpdate settings &gt;%2$s', 'screen-stay-awake' ), '<a href="' . esc_url( self::settings_admin_url() ) . '">', '</a>' );
					?>
				</p>
			</div>
			<?php
		endif;
	}

	/**
	 * The master settings array.
	 * This array will be parsed by other functions to implement various functionality.
	 * We shouldn't have to edit any existing functions to add new options.
	 * Instead, we just modify this array.
	 *
	 * @return array
	 */
	public static function settings() {
		return array(
			'general' => array(
				'title'      => __( 'General', 'screen-stay-awake' ),
				'initialize' => array(
					'disable_404'      => 1,
					'disable_archives' => array( 'callback' => array( '\SCRNSA\Util', 'disable_all_archive_types' ) ),
				),
				'sections'   => array(
					'general' => array(
						'title'  => __( 'General', 'screen-stay-awake' ),
						'fields' => array(
							'enable_stay_awake'  => __( 'Enable Stay Awake', 'screen-stay-awake' ),
							'disable_post_types' => __( 'Disable Post Types', 'screen-stay-awake' ),
							'disable_archives'   => __( 'Disable Archives', 'screen-stay-awake' ),
							'disable_404'        => __( 'Disable on 404', 'screen-stay-awake' ),
						),
					),
				),
			),
		);
	}

	private function admin_title() {
		return __( 'Screen Stay Awake', 'screen-stay-awake' );
	}

	/**
	 * Create all of the admin pages.
	 * Also adds the admin page hooks to an array for later use.
	 *
	 * @return void
	 */
	public function create_admin_menu() {

		$hook = add_options_page(
			self::admin_title(),
			self::admin_title(),
			Plugin::capability(),
			self::admin_slug(),
			array( $this, 'settings_page' ),
		);

		$this->admin_menu_hooks['screen-stay-awake'] = $hook;
	}

	/**
	 * Register settings from settings array.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->sf()->add_sections_and_settings(
			$this->settings(),
			$this
		);
	}

	/**
	 * Creates an admin slug using the text domain and an optional sub string.
	 *
	 * @param null|string $sub Optional sub page slug.
	 *
	 * @return string
	 */
	public static function admin_slug( $sub = null ) {
		$slug = 'screen-stay-awake';

		if ( $sub ) {
			$slug .= '-' . sanitize_title( $sub );
		}

		return $slug;
	}


	/**
	 * The path to the admin page.
	 *
	 * @param null $sub Optional sub page.
	 *
	 * @return string
	 */
	public static function admin_path( $sub = null ) {
		return 'options-general.php?page=' . self::admin_slug( $sub );
	}

	/**
	 * The admin_url for the settings page.
	 *
	 * @return string
	 */
	public static function settings_admin_url( $tab = false ) {
		if ( ! $tab ) {
			return admin_url( self::admin_path() );
		}

		$sf = new WOSettings( Plugin::ns() );
		return $sf->get_tab_url( $sf->key( $tab ), self::settings_admin_url() );
	}

	/**
	 * Display the settings page.
	 *
	 * @return void
	 */
	public function settings_page() {
		$this->sf()->settings_page(
			__( 'Screen Stay Awake', 'screen-stay-awake' ),
			self::settings_admin_url(),
			self::settings(),
			false
		);
	}

	/**
	 * Call back for settings section.
	 *
	 * @return void
	 */
	public function settings_callback_scrnsa_general() {}

	/**
	 * Callback for settings field.
	 *
	 * @return void
	 */
	public function field_scrnsa_enable_stay_awake() {
		$id = array( $this->sf()->key( 'general' ) => 'enable_stay_awake' );

		$this->sf()->checkbox( $id, $this->sf()->get( 'enable_stay_awake', 'general' ) );
		$this->sf()->label( $id, esc_html__( 'Enable Stay Awake Script for all visitors', 'screen-stay-awake' ) );
		$this->sf()->message( '<em>' . esc_html__( 'Be sure to clear any page caching and javascript minification after enabling or disabling the script.', 'screen-stay-awake' ) . '</em>' );
	}

	/**
	 * Callback for settings field.
	 *
	 * @return void
	 */
	public function field_scrnsa_disable_post_types() {
		$id = array( $this->sf()->key( 'general' ) => 'disable_post_types' );

		$post_type_arr = array();
		$post_types    = get_post_types( array( 'public' => true ), 'objects' );
		$post_type_arr = Util::object_to_array( $post_types, 'name', 'label' );

		// print_r( $post_types );
		$current_options = $this->sf()->get( 'disable_post_types', 'general' );

		$this->sf()->checkgroup( $id, $post_type_arr, $current_options );
		$this->sf()->message( '<em>' . esc_html__( 'Do not load the Screen Stay Awake script on the selected post types.', 'screen-stay-awake' ) . '</em>' );
	}

	/**
	 * Callback for settings field.
	 *
	 * @return void
	 */
	public function field_scrnsa_disable_archives() {
		$id = array( $this->sf()->key( 'general' ) => 'disable_archives' );

		$archive_arr = Util::all_archive_types();

		// print_r( $post_types );
		$current_options = $this->sf()->get( 'disable_archives', 'general' );

		$this->sf()->checkgroup( $id, $archive_arr, $current_options );
		$this->sf()->message( '<em>' . esc_html__( 'Do not load the Screen Stay Awake script on the selected archives.', 'screen-stay-awake' ) . '</em>' );
	}

	/**
	 * Callback for settings field.
	 *
	 * @return void
	 */
	public function field_scrnsa_disable_404() {
		$id = array( $this->sf()->key( 'general' ) => 'disable_404' );

		$this->sf()->checkbox( $id, $this->sf()->get( 'disable_404', 'general' ) );
		$this->sf()->label( $id, esc_html__( 'Disable Screen Stay Awake script on 404 page', 'screen-stay-awake' ) );
	}



	/**
	 * Sanitize input by type.
	 *
	 * @param array $input The input to sanitize.
	 *
	 * @return array
	 */
	private function sanitize_by_key_type( $input ) {
		$output = array();

		if ( $input ) {
			foreach ( $input as $key => $value ) {
				switch ( $key ) {
					case 'disable_post_types':
					case 'disable_archives':
						$type = 'str';
						break;

					default:
						$type = 'bool';
						break;
				}

				$output[ $key ] = WOAdmin::sanitize_by_type( $value, $type );
			}
		}

		return $output;
	}

	/**
	 * Sanitize general group.
	 *
	 * @param array $input The input to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_scrnsa_general( $input ) {
		return $this->sanitize_by_key_type( $input );
	}

	/**
	 * Check the current screen against an array of screen IDs.
	 *
	 * @param array $screen_ids Screen IDs to compare against.
	 *
	 * @return bool
	 */
	public function is_screen( $screen_ids = array() ) {

		$screen = get_current_screen();

		if ( ! isset( $screen->id ) ) {
			return false;
		}

		if ( ! $screen_ids ) {

			$screen_ids = apply_filters(
				'scrnsa_admin_all_screen_ids',
				array()
			);

			$screen_ids = array_merge( $screen_ids, apply_filters( 'scrnsa_admin_menu_hooks', array_values( $this->admin_menu_hooks ) ) );
		}

		$screen_ids = Util::arrayify( $screen_ids );

		return in_array( $screen->id, $screen_ids );
	}

	/**
	 * Enqueue styles if we're on a screen that needs them.
	 *
	 * @return void
	 */
	public function admin_print_styles() {

		if ( $this->is_screen() ) {
			$this->enqueue_woadmin_styles();
		}
	}
}

<?php
namespace SCRNSA;

/**
 * Misc data used throughout this plugin.
 */
class Plugin {

	/**
	 * The plugin version.
	 *
	 * @return string
	 */
	public static function version() {
		return '1.0.3';
	}

	/**
	 * The plugin title.
	 *
	 * @return string
	 */
	public static function title() {
		return __( 'Screen Stay Awake', 'screen-stay-awake' );
	}

	/**
	 * The menu title.
	 *
	 * @return string
	 */
	public static function menu_title() {
		return self::title();
	}

	/**
	 * The plugin namespace.
	 *
	 * @return string
	 */
	public static function ns() {
		return 'scrnsa';
	}

	/**
	 * The path to the assets directory.
	 *
	 * @return string
	 */
	public static function assets_path() {
		return SCRNSA_PLUGIN_DIR . 'dist/';
	}

	/**
	 * The URL to the assets directory.
	 *
	 * @return string
	 */
	public static function assets_url() {
		return SCRNSA_PLUGIN_URL . 'dist/';
	}

	/**
	 * The required capability for managing this plugin.
	 *
	 * @return string
	 */
	public static function capability() {
		return 'manage_options';
	}

	/**
	 * The URL to the plugin support page
	 */
	public static function support_url() {
		return 'https://wordpress.org/support/plugin/screen-stay-awake/';
	}
}

<?php
/**
 * Plugin Name:     Screen Stay Awake
 * Plugin URI:      https://wordpress.org/plugins/screen-stay-awake/
 * Description:     Request a visitor's screen stay active while viewing your website. Implements Screen Wake Lock API.
 * Version:         1.0.3
 * Author:          Wild Operation
 * Author URI:      https://wildoperation.com
 * License:         GPL-3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:     screen-stay-awake
 *
 * @package WordPress
 * @subpackage Screen Stay Awake
 * @since 1.0.0
 * @version 1.0.3
 */

/* Abort! */
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SCRNSA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCRNSA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SCRNSA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Load
 */
require SCRNSA_PLUGIN_DIR . 'vendor/autoload.php';

/**
 * Review request framework
 */
new SCRNSA\Vendor\WOWPRB\WPPluginReviewBug(
	__FILE__,
	'screen-stay-awake',
	array(
		'intro'            => __( 'Your Screen Stay Awake reviews are invaluable to us and help us maintain a free version of this plugin. We appreciate your support!', 'screen-stay-awake' ),
		'rate_link_text'   => __( 'Leave ★★★★★ rating', 'screen-stay-awake' ),
		'need_help_text'   => __( 'I need help', 'screen-stay-awake' ),
		'remind_link_text' => __( 'Remind me later', 'screen-stay-awake' ),
		'nobug_link_text'  => __( 'Don\'t ask again', 'screen-stay-awake' ),
	),
	array(
		'need_help_url' => SCRNSA\Plugin::support_url(),
	)
);

/**
 * Initialize; plugins_loaded
 */
add_action(
	'plugins_loaded',
	function () {
		/**
		 * Initiate classes and their hooks.
		 */
		$classes = array(
			'SCRNSA\Admin',
			'SCRNSA\Front',
		);

		foreach ( $classes as $class ) {
			$instance = new $class();

			if ( method_exists( $instance, 'hooks' ) ) {
				$instance->hooks();
			}
		}
	},
	10
);

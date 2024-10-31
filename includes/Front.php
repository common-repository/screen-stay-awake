<?php
namespace SCRNSA;

/**
 * Frontend scripts, styles, ajax handling, etc.
 */
class Front {
	/**
	 * Create hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue front-end scripts.
	 */
	public function enqueue() {
		/**
		* Use the scrnsa_stayawake_should_enqueue filter to limit where this script should load.
		*/
		if ( Options::instance()->get( 'enable_stay_awake', 'general', true ) && apply_filters( 'scrnsa_stayawake_should_enqueue', true ) ) {

			if ( is_404() && Options::instance()->get( 'disable_404', 'general', true ) ) {
				return false;
			} elseif ( is_singular() ) {
				$disable_post_types = Options::instance()->get( 'disable_post_types', 'general' );

				if ( $disable_post_types && ! empty( $disable_post_types ) ) {
					$current_post_type = get_post_type();

					if ( $current_post_type && is_singular( $disable_post_types ) ) {
						return false;
					}
				}
			} elseif ( ! is_404() ) {
				$disabled_archives = Options::instance()->get( 'disable_archives', 'general' );
				print_r( $disabled_archives );

				if ( $disabled_archives && ! empty( $disabled_archives ) ) {
					if ( is_post_type_archive( $disabled_archives ) ) {
						return false;
					}

					if ( in_array( 'category', $disabled_archives, true ) && is_category() ) {
						return false;
					}

					if ( in_array( 'post_tag', $disabled_archives, true ) && is_tag() ) {
						return false;
					}

					if ( in_array( 'author', $disabled_archives, true ) && is_author() ) {
						return false;
					}

					if ( in_array( 'archive_date', $disabled_archives, true ) && is_date() ) {
						return false;
					}
				}
			}

			wp_register_script(
				'scrnsa_stayawake',
				Plugin::assets_url() . 'js/stayawake.js',
				array(),
				Plugin::version(),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
			wp_enqueue_script( 'scrnsa_stayawake' );
		}
	}
}

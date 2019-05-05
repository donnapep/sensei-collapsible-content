<?php
/**
 * File containing the class \Sensei_Collapsible_Content_Dependency_Checker.
 *
 * @package sensei-collapsible-content
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks that dependencies are met.
 *
 * @since 1.0.0
 */
class Sensei_Collapsible_Content_Dependency_Checker {
	const MINIMUM_PHP_VERSION         = '5.6';
	const MINIMUM_SENSEI_VERSION      = '2.0.0';

	/**
	 * Checks that system dependencies are met.
	 *
	 * @return bool
	 */
	public static function are_system_dependencies_met() {
		$are_met = true;

		if ( ! self::check_php() ) {
			add_action( 'admin_notices', array( __CLASS__, 'add_php_notice' ) );
			$are_met = false;
		}

		if ( ! $are_met ) {
			add_action( 'admin_init', array( __CLASS__, 'deactivate_self' ) );
		}

		return $are_met;
	}

	/**
	 * Checks that plugin dependencies are met.
	 *
	 * @return bool
	 */
	public static function are_plugin_dependencies_met() {
		$are_met = true;

		if ( ! self::check_sensei() ) {
			add_action( 'admin_notices', array( __CLASS__, 'add_sensei_notice' ) );
			$are_met = false;
		}

		return $are_met;
	}

	/**
	 * Deactivates self.
	 */
	public static function deactivate_self() {
		deactivate_plugins( SENSEI_COLLAPSIBLE_CONTENT_PLUGIN_BASENAME );
	}

	/**
	 * Adds a notice in WP Admin if the minimum version of PHP is not installed.
	 *
	 * @access private
	 */
	public static function add_php_notice() {
		$screen        = get_current_screen();
		$valid_screens = array( 'dashboard', 'plugins', 'plugins-network' );

		if ( ! current_user_can( 'activate_plugins' ) || ! in_array( $screen->id, $valid_screens, true ) ) {
			return;
		}

		// translators: %1$s is the minimum PHP version that this plugin requires; %2$s is the actual PHP version.
		$message = sprintf( __( '<strong>Collapsible Content for Sensei LMS</strong> requires PHP %1$s+, but you\'re running PHP %2$s.', 'sensei-collapsible-content' ), self::MINIMUM_PHP_VERSION, phpversion() );
		$php_update_url = 'https://wordpress.org/support/update-php/';

		if ( function_exists( 'wp_get_update_php_url' ) ) {
			$php_update_url = wp_get_update_php_url();
		}

		// Output the notice.
		echo '<div class="error"><p>';
		echo wp_kses( $message, array( 'strong' => array() ) );
		printf(
			'<p><a class="button button-primary" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
			esc_url( $php_update_url ),
			esc_html__( 'Learn more about updating PHP', 'sensei-collapsible-content' ),
			esc_html__( '(opens in a new tab)', 'sensei-collapsible-content' )
		);
		echo '</p></div>';
	}

	/**
	 * Adds a notice in WP Admin if the minimum version of Sensei LMS is not installed.
	 *
	 * @access private
	 */
	public static function add_sensei_notice() {
		$screen        = get_current_screen();
		$valid_screens = array( 'dashboard', 'plugins', 'plugins-network' );

		if ( ! current_user_can( 'activate_plugins' ) || ! in_array( $screen->id, $valid_screens, true ) ) {
			return;
		}

		// translators: %1$s is the minimum Sensei LMS version that this plugin requires.
		$message = sprintf( __( '<strong>Collapsible Content for Sensei LMS</strong> requires <strong>Sensei LMS %1$s+</strong> to be installed and activated.', 'sensei-collapsible-content' ), self::MINIMUM_SENSEI_VERSION );

		echo '<div class="error"><p>';
		echo wp_kses( $message, array( 'strong' => array() ) );
		echo '</p></div>';
	}

	/**
	 * Checks that the minimum version of PHP is installed.
	 *
	 * @return bool
	 */
	private static function check_php() {
		return version_compare( phpversion(), self::MINIMUM_PHP_VERSION, '>=' );
	}

	/**
	 * Checks that the minimum version of Sensei LMS is installed.
	 *
	 * @return bool
	 */
	private static function check_sensei() {
		if ( ! class_exists( 'Sensei_Main' ) ) {
			return false;
		}

		return version_compare( self::MINIMUM_SENSEI_VERSION, get_option( 'sensei-version' ), '<=' );
	}
}

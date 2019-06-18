<?php
/**
 * File containing the class \Sensei_Collapsible_Content\Sensei_Collapsible_Content.
 *
 * @package sensei-collapsible-content
 * @since   1.0.0
 */

namespace Sensei_Collapsible_Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Sensei Collapsible Content class.
 *
 * @class Sensei_Collapsible_Content
 */
final class Sensei_Collapsible_Content {
	/**
	 * Instance of class.
	 *
	 * @var Sensei_Collapsible_Content
	 */
	private static $instance;

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Initializes the singleton instance.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->plugin_url = untrailingslashit( plugins_url( '', SENSEI_COLLAPSIBLE_CONTENT_PLUGIN_BASENAME ) );
	}

	/**
	 * Fetches an instance of the class.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initializes the class and adds all filters and actions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$instance = self::instance();

		add_action( 'init', [ $instance, 'load_plugin_textdomain' ], 0 );

		if ( ! \Sensei_Collapsible_Content_Dependency_Checker::are_plugin_dependencies_met() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $instance, 'load_scripts' ] );
		add_filter( 'sensei_locate_template', [ $instance, 'locate_template' ], 10, 3 );
	}

	/**
	 * Loads textdomain for plugin.
	 */
	public function load_plugin_textdomain() {
		$domain = 'sensei-collapsible-content';
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, $domain );

		unload_textdomain( $domain );
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Registers/queues frontend scripts and styles.
	 */
	public function load_scripts() {
		// CSS
		wp_enqueue_style( 'sensei-collapsible-content', $this->plugin_url . '/assets/css/sensei-collapsible-content.css', [], SENSEI_COLLAPSIBLE_CONTENT_VERSION );

		// Load Dashicons on the single course page.
		// TODO: Does Sensei already use/load FontAwesome? If so, probably best to use it.
		if ( is_single() && 'course' === get_post_type() ) {
			wp_enqueue_style( 'dashicons' );
		}

		// JavaScript
		wp_enqueue_script( 'sensei-collapsible-content', $this->plugin_url . '/assets/js/sensei-collapsible-content.js', array( 'jquery' ), SENSEI_COLLAPSIBLE_CONTENT_VERSION, true );
	}

	/**
	 * Gets the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( SENSEI_COLLAPSIBLE_CONTENT_PLUGIN_FILE ) );
	}

	/**
	 * Locates a template and returns the path for inclusion.
	 *
	 * This is the load order:
	 *
	 * yourtheme/$template_path/$template_name
	 * yourtheme/$template_name
	 * sensei-collapsible-content/sensei/$template_name
	 * $default_path/$template_name
	 *
	 * @since 1.0.0
	 *
	 * @param string $template      Default template.
	 * @param string $template_name Template name.
	 * @param string $template_path Template path.
	 * @return string Template path to load.
	 */
	public function locate_template( $template, $template_name, $template_path ) {
		$_template = $template;

		if ( ! $template_path ) {
			$template_path = Sensei()->template_url;
		}

		$plugin_path  = $this->plugin_path() . '/templates/';

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name
			)
		);

		// If theme template was not found, get the template from this plugin, if it exists.
		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		// Use default template.
		if ( ! $template ) {
			$template = $_template;
		}

		return $template;
	}
}

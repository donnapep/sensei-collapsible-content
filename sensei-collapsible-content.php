<?php
/**
 * Plugin Name: Collapsible Content for Sensei LMS
 * Description: Collapse and expand content in Sensei LMS courses.
 * Version: 1.0.1
 * Tested up to: 5.4
 * Requires PHP: 5.6
 * Author: Donna Peplinskie
 * Author URI: https://donnapeplinskie.com
 * Text Domain: sensei-collapsible-content
 * Domain Path: /languages/
 *
 * @package sensei-collapsible-content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SENSEI_COLLAPSIBLE_CONTENT_VERSION', '1.0.1' );
define( 'SENSEI_COLLAPSIBLE_CONTENT_PLUGIN_FILE', __FILE__ );
define( 'SENSEI_COLLAPSIBLE_CONTENT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once dirname( __FILE__ ) . '/includes/class-sensei-collapsible-content-dependency-checker.php';

if ( ! Sensei_Collapsible_Content_Dependency_Checker::are_system_dependencies_met() ) {
	return;
}

// Requires the main Sensei_Collapsible_Content class.
require_once dirname( __FILE__ ) . '/includes/class-sensei-collapsible-content.php';

// Loads the plugin after all other plugins have loaded.
add_action( 'plugins_loaded', array( 'Sensei_Collapsible_Content\Sensei_Collapsible_Content', 'init' ), 5 );

Sensei_Collapsible_Content\Sensei_Collapsible_Content::instance();

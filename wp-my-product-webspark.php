<?php
/**
 * Plugin Name: WP My Product WebSpark
 * Description: Extends WooCommerce functionality with custom product management in My Account
 * Version: 1.0.0
 * Author: WebSpark Team
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 */

namespace WebSpark\MyProduct;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	register_activation_hook(
		__FILE__,
		function () {
			if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				wp_die( 'WooCommerce must be installed and activated for WP My Product WebSpark plugin to work.', 'Plugin Activation Error', array( 'back_link' => true ) );
			}
		}
	);

	\deactivate_plugins( plugin_basename( __FILE__ ) );

	return;
}


// Plugin constants
define( 'WOOCOMMERCE_PATH', plugin_dir_path( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) );

// Require the Woocommerce classes from the WooCommerce plugin
if ( ! class_exists( 'WC_Email' ) ) {
	require_once WOOCOMMERCE_PATH . 'includes/emails/class-wc-email.php';
}

// Require necessary files
require_once plugin_dir_path( __FILE__ ) . 'includes/class-singleton-trait.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-product-handler.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-email-notification.php';

class MyProductPlugin {

	use SingletonTrait;

	private $version = '1.0.0';


	public function __construct() {
		$this->define_constants();
		$this->load_inc_files();
		add_action( 'init', array( $this, 'init_plugin' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		register_activation_hook( plugin_basename( __FILE__ ) . 'wp-my-product-webspark.php', array( $this, 'activate_plugin' ) );
	}

	private function define_constants() {
		define( 'WEBSPARK_MY_PRODUCT_VERSION', $this->version );
		define( 'WEBSPARK_MY_PRODUCT_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WEBSPARK_MY_PRODUCT_URL', plugin_dir_url( __FILE__ ) );
	}
	public function init_plugin() {
		MyProductHandler::get_instance();
		EmailNotification::get_instance();
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp-my-product-webspark', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function activate_plugin() {
		// Update site links if needed during activation
		$this->update_site_links();
	}

	private function update_site_links() {
		// Implement any necessary link updates
		// This could involve updating permalinks or other site-wide links
		flush_rewrite_rules();
	}

	public static function deactivate() {
		// Cleanup operations if needed
		flush_rewrite_rules();
	}

	/**
	 * Load all files from inc directory
	 */
	private function load_inc_files() {
		$inc_dir = WEBSPARK_MY_PRODUCT_PATH . 'inc';

		if ( is_dir( $inc_dir ) ) {
			foreach ( new \DirectoryIterator( $inc_dir ) as $fileInfo ) {
				if ( $fileInfo->isFile() && $fileInfo->getExtension() === 'php' ) {
					require_once $fileInfo->getPathname();
				}
			}
		}
	}
}

// Initialize the plugin
MyProductPlugin::get_instance();

// Register deactivation hook
register_deactivation_hook( __FILE__, array( MyProductPlugin::class, 'deactivate' ) );

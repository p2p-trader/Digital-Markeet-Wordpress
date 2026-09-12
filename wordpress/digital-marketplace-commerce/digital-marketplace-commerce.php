<?php
/**
 * Plugin Name:       Digital Marketplace Commerce
 * Plugin URI:        https://example.com/digital-marketplace-commerce
 * Description:       Self-contained, lightweight e-commerce and peer-to-peer cryptocurrency payment processing engine for digital assets, templates, and developer kits.
 * Version:           1.0.0
 * Author:            Digital Marketplace Team
 * Author URI:        https://example.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       digital-marketplace-commerce
 * Domain Path:       /languages
 *
 * @package           Digital_Marketplace_Commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DMC_VERSION', '1.0.0' );
define( 'DMC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DMC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load Core Components
require_once DMC_PLUGIN_DIR . 'includes/class-dmc-cart.php';
require_once DMC_PLUGIN_DIR . 'includes/class-dmc-post-type.php';
require_once DMC_PLUGIN_DIR . 'includes/class-dmc-downloads.php';
require_once DMC_PLUGIN_DIR . 'includes/class-dmc-ajax.php';
require_once DMC_PLUGIN_DIR . 'includes/class-dmc-checkout.php';
require_once DMC_PLUGIN_DIR . 'includes/class-dmc-settings.php';

/**
 * Initialize Plugin Hooks and Subsystems
 */
function dmc_commerce_init() {
    DMC_Post_Type::init();
    DMC_Downloads::init();
    DMC_Ajax::init();
    DMC_Checkout::init();
    DMC_Settings::init();
}
add_action( 'plugins_loaded', 'dmc_commerce_init' );

/**
 * Enqueue Front-End Assets for Cart & Checkout
 */
function dmc_enqueue_scripts() {
    wp_enqueue_style(
        'dmc-commerce-style',
        DMC_PLUGIN_URL . 'assets/css/dmc-commerce.css',
        array(),
        DMC_VERSION
    );

    wp_enqueue_script(
        'dmc-commerce-script',
        DMC_PLUGIN_URL . 'assets/js/dmc-commerce.js',
        array(),
        DMC_VERSION,
        true
    );

    wp_localize_script( 'dmc-commerce-script', 'dmcCommerceData', array(
        'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
        'cartNonce'   => wp_create_nonce( 'dmc_cart_nonce' ),
        'cartUrl'     => home_url( '/cart' ),
        'checkoutUrl' => home_url( '/checkout' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'dmc_enqueue_scripts' );

/**
 * Helper: Get current live cart item count
 */
function dmc_get_cart_item_count() {
    if ( class_exists( 'DMC_Cart' ) ) {
        return DMC_Cart::get_item_count();
    }
    return 0;
}

/**
 * Activation / Deactivation Handlers
 */
function dmc_commerce_activate() {
    DMC_Post_Type::register_order_cpt();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dmc_commerce_activate' );

function dmc_commerce_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dmc_commerce_deactivate' );

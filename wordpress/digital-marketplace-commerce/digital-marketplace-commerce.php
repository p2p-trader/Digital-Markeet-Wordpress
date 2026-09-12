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
 * Automatic Page Creation Routine & Store Setup on Plugin Activation
 *
 * Checks for existing pages using exact template meta query (_wp_page_template)
 * to avoid duplicates even if pages are renamed. Safely supports re-activation and partial setups.
 *
 * @return array Associative array of created page titles and homepage setup status.
 */
function dmc_auto_create_required_pages() {
    $created_page_names = array();
    $homepage_set       = false;

    // Retrieve previously stored setup page IDs
    $setup_page_ids = get_option( 'dmc_setup_page_ids', array() );
    if ( ! is_array( $setup_page_ids ) ) {
        $setup_page_ids = array();
    }

    // Required digital marketplace pages specification
    $required_pages = array(
        'login' => array(
            'title'    => __( 'Login', 'digital-marketplace-commerce' ),
            'slug'     => 'login',
            'template' => 'page-login.php',
            'content'  => '',
        ),
        'cart' => array(
            'title'    => __( 'Cart', 'digital-marketplace-commerce' ),
            'slug'     => 'cart',
            'template' => 'page-cart.php',
            'content'  => '<!-- wp:shortcode -->[dmc_cart]<!-- /wp:shortcode -->',
        ),
        'checkout' => array(
            'title'    => __( 'Checkout', 'digital-marketplace-commerce' ),
            'slug'     => 'checkout',
            'template' => 'page-checkout.php',
            'content'  => '<!-- wp:shortcode -->[dmc_checkout]<!-- /wp:shortcode -->',
        ),
        'account' => array(
            'title'    => __( 'My Account', 'digital-marketplace-commerce' ),
            'slug'     => 'account',
            'template' => 'page-account.php',
            'content'  => '',
        ),
        'legal' => array(
            'title'    => __( 'Terms & Refund Policy', 'digital-marketplace-commerce' ),
            'slug'     => 'terms',
            'template' => 'page-legal.php',
            'content'  => '',
        ),
    );

    foreach ( $required_pages as $key => $page_def ) {
        $existing_page_id = 0;

        // 1. Fast check: Stored ID from previous activations
        if ( ! empty( $setup_page_ids[ $key ] ) ) {
            $stored_post = get_post( absint( $setup_page_ids[ $key ] ) );
            if ( $stored_post && 'publish' === $stored_post->post_status && 'page' === $stored_post->post_type ) {
                $stored_tpl = get_post_meta( $stored_post->ID, '_wp_page_template', true );
                if ( $stored_tpl === $page_def['template'] ) {
                    $existing_page_id = $stored_post->ID;
                }
            }
        }

        // 2. Safe Template Query: Check by exact _wp_page_template (not by title, preventing duplicates if renamed)
        if ( ! $existing_page_id ) {
            $matched_pages = get_posts( array(
                'post_type'              => 'page',
                'post_status'            => 'publish',
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
                'meta_query'             => array(
                    array(
                        'key'     => '_wp_page_template',
                        'value'   => $page_def['template'],
                        'compare' => '=',
                    ),
                ),
            ) );

            if ( ! empty( $matched_pages ) ) {
                $existing_page_id = $matched_pages[0]->ID;
                $setup_page_ids[ $key ] = $existing_page_id;
            }
        }

        // 3. Create page if missing
        if ( ! $existing_page_id ) {
            $new_page_id = wp_insert_post( array(
                'post_title'     => $page_def['title'],
                'post_name'      => $page_def['slug'],
                'post_content'   => $page_def['content'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            ) );

            if ( ! is_wp_error( $new_page_id ) && $new_page_id ) {
                update_post_meta( $new_page_id, '_wp_page_template', $page_def['template'] );
                $setup_page_ids[ $key ] = $new_page_id;
                $created_page_names[]   = $page_def['title'];
            }
        }
    }

    // Static Front Page Configuration
    // Check if the site currently has a static front page set
    $show_on_front         = get_option( 'show_on_front' );
    $page_on_front         = absint( get_option( 'page_on_front' ) );
    $has_static_front_page = ( 'page' === $show_on_front && $page_on_front > 0 && 'publish' === get_post_status( $page_on_front ) );

    if ( ! $has_static_front_page ) {
        $home_id = 0;

        // Check if a Home page was previously recorded in setup page IDs
        if ( ! empty( $setup_page_ids['home'] ) ) {
            $stored_home = get_post( absint( $setup_page_ids['home'] ) );
            if ( $stored_home && 'publish' === $stored_home->post_status && 'page' === $stored_home->post_type ) {
                $home_id = $stored_home->ID;
            }
        }

        // Check if an existing published page with slug 'home' exists
        if ( ! $home_id ) {
            $existing_home_posts = get_posts( array(
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'name'           => 'home',
                'posts_per_page' => 1,
                'no_found_rows'  => true,
            ) );

            if ( ! empty( $existing_home_posts ) ) {
                $home_id = $existing_home_posts[0]->ID;
            }
        }

        // If no home page exists, create one
        if ( ! $home_id ) {
            $home_id = wp_insert_post( array(
                'post_title'     => __( 'Home', 'digital-marketplace-commerce' ),
                'post_name'      => 'home',
                'post_content'   => '',
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            ) );

            if ( ! is_wp_error( $home_id ) && $home_id ) {
                $created_page_names[] = __( 'Home', 'digital-marketplace-commerce' );
            }
        }

        // Assign static front page options in WordPress core
        if ( $home_id && ! is_wp_error( $home_id ) ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $home_id );
            $setup_page_ids['home'] = $home_id;
            $homepage_set           = true;
        }
    }

    // Persist all setup page IDs for instant checks on future activations
    update_option( 'dmc_setup_page_ids', $setup_page_ids );

    return array(
        'created_pages' => $created_page_names,
        'homepage_set'  => $homepage_set,
    );
}

/**
 * Display one-time admin notice after activation confirming created pages.
 * Stored via transient so it does not repeat on every page load.
 */
function dmc_commerce_activation_notice() {
    $notice_data = get_transient( 'dmc_activation_setup_notice' );
    if ( ! $notice_data || ! is_array( $notice_data ) ) {
        return;
    }

    // Delete transient immediately so it only shows once
    delete_transient( 'dmc_activation_setup_notice' );

    // Only display to administrators
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $created_pages = ! empty( $notice_data['created_pages'] ) ? (array) $notice_data['created_pages'] : array();
    $homepage_set  = ! empty( $notice_data['homepage_set'] );
    $count         = count( $created_pages );

    $settings_url = admin_url( 'options-general.php?page=digital-marketplace' );

    // Build the confirmation message matching user instructions:
    // "Digital Marketplace Commerce: created N required pages, set your homepage, and configured [X]. Please review Settings > Digital Marketplace to add your crypto wallet address before going live."
    // Only mention pages actually created, not ones that already existed.
    $parts = array();

    if ( $count > 0 ) {
        $parts[] = sprintf(
            /* translators: 1: number of pages, 2: comma-separated list of page names */
            _n(
                'created %1$d required page (%2$s)',
                'created %1$d required pages (%2$s)',
                $count,
                'digital-marketplace-commerce'
            ),
            $count,
            esc_html( implode( ', ', $created_pages ) )
        );
    }

    if ( $homepage_set ) {
        $parts[] = esc_html__( 'set your homepage', 'digital-marketplace-commerce' );
    }

    $parts[] = esc_html__( 'configured digital store endpoints', 'digital-marketplace-commerce' );

    if ( count( $parts ) === 1 ) {
        $status_sentence = $parts[0];
    } elseif ( count( $parts ) === 2 ) {
        $status_sentence = $parts[0] . ' ' . esc_html__( 'and', 'digital-marketplace-commerce' ) . ' ' . $parts[1];
    } else {
        $last = array_pop( $parts );
        $status_sentence = implode( ', ', $parts ) . ', ' . esc_html__( 'and', 'digital-marketplace-commerce' ) . ' ' . $last;
    }
    ?>
    <div class="notice notice-success is-dismissible" style="border-left-color: #2563eb; padding: 12px 16px;">
        <p style="font-size: 14px; line-height: 1.5; margin: 0;">
            <strong><?php esc_html_e( 'Digital Marketplace Commerce:', 'digital-marketplace-commerce' ); ?></strong>
            <?php echo esc_html( $status_sentence ); ?>.
            <?php 
            printf(
                /* translators: %s: URL to plugin settings */
                __( 'Please review <a href="%s" style="font-weight: 700; text-decoration: underline; color: #1d4ed8;">Settings &gt; Digital Marketplace</a> to add your crypto wallet address before going live.', 'digital-marketplace-commerce' ),
                esc_url( $settings_url )
            );
            ?>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'dmc_commerce_activation_notice' );

/**
 * Activation / Deactivation Handlers
 */
function dmc_commerce_activate() {
    DMC_Post_Type::register_order_cpt();

    // Automatic page creation routine and front page configuration
    $setup_result = dmc_auto_create_required_pages();

    // Store transient for one-time dismissible admin notice
    set_transient( 'dmc_activation_setup_notice', array(
        'created_pages' => $setup_result['created_pages'],
        'homepage_set'  => $setup_result['homepage_set'],
    ), HOUR_IN_SECONDS );

    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dmc_commerce_activate' );

function dmc_commerce_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dmc_commerce_deactivate' );

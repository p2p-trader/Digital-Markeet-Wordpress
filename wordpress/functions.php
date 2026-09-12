<?php
/**
 * Digital Marketplace Theme functions and definitions
 *
 * @package Digital_Marketplace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'DIGITAL_MARKETPLACE_VERSION', '1.0.0' );

/**
 * Theme Setup
 */
function digital_marketplace_setup() {
    // Make theme available for translation.
    load_theme_textdomain( 'digital-marketplace', get_template_directory() . '/languages' );

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts, pages, and custom post types.
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 800, 500, true );
    add_image_size( 'marketplace-card', 640, 400, true );
    add_image_size( 'marketplace-gallery', 1200, 750, true );

    // Register navigation menus.
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'digital-marketplace' ),
        'footer'  => esc_html__( 'Footer Menu', 'digital-marketplace' ),
    ) );

    // Switch default core markup for search form, comment form, etc. to HTML5.
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Add theme support for WooCommerce if installed.
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'digital_marketplace_setup' );

/**
 * Enqueue scripts and styles.
 * Strictly no hardcoded <link> or <script> tags.
 */
function digital_marketplace_scripts() {
    // Theme core stylesheet (contains standard WordPress header).
    wp_enqueue_style(
        'digital-marketplace-style',
        get_stylesheet_uri(),
        array(),
        DIGITAL_MARKETPLACE_VERSION
    );

    // Design layout stylesheet matching React Tailwind aesthetic.
    wp_enqueue_style(
        'digital-marketplace-theme',
        get_template_directory_uri() . '/assets/css/theme.css',
        array( 'digital-marketplace-style' ),
        DIGITAL_MARKETPLACE_VERSION
    );

    // Main interactive JavaScript.
    wp_enqueue_script(
        'digital-marketplace-js',
        get_template_directory_uri() . '/assets/js/marketplace.js',
        array(),
        DIGITAL_MARKETPLACE_VERSION,
        true // in footer
    );

    // Pass dynamic localized parameters to frontend JS.
    wp_localize_script( 'digital-marketplace-js', 'digitalMarketplaceData', array(
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'digital_marketplace_nonce' ),
        'isUserLoggedIn' => is_user_logged_in(),
    ) );
}
add_action( 'wp_enqueue_scripts', 'digital_marketplace_scripts' );

/**
 * Register Custom Post Type: "Products"
 * Note: If WooCommerce is installed, its built-in 'product' CPT will be utilized.
 * This standalone CPT allows the theme to function dynamically out-of-the-box
 * even before WooCommerce or ACF is activated.
 */
function digital_marketplace_register_cpt() {
    if ( ! post_type_exists( 'product' ) ) {
        $labels = array(
            'name'                  => _x( 'Products', 'Post Type General Name', 'digital-marketplace' ),
            'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'digital-marketplace' ),
            'menu_name'             => __( 'Products', 'digital-marketplace' ),
            'archives'              => __( 'Product Archives', 'digital-marketplace' ),
            'all_items'             => __( 'All Products', 'digital-marketplace' ),
            'add_new_item'          => __( 'Add New Product', 'digital-marketplace' ),
            'add_new'               => __( 'Add New', 'digital-marketplace' ),
            'new_item'              => __( 'New Product', 'digital-marketplace' ),
            'edit_item'             => __( 'Edit Product', 'digital-marketplace' ),
            'update_item'           => __( 'Update Product', 'digital-marketplace' ),
            'view_item'             => __( 'View Product', 'digital-marketplace' ),
            'search_items'          => __( 'Search Product', 'digital-marketplace' ),
        );

        $args = array(
            'label'                 => __( 'Product', 'digital-marketplace' ),
            'description'           => __( 'Digital marketplace assets and tools', 'digital-marketplace' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'taxonomies'            => array( 'product_cat' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-cart',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'rewrite'               => array( 'slug' => 'products' ),
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );
        register_post_type( 'product', $args );

        // Register Product Categories Taxonomy
        register_taxonomy( 'product_cat', array( 'product' ), array(
            'hierarchical'      => true,
            'labels'            => array(
                'name'              => _x( 'Product Categories', 'taxonomy general name', 'digital-marketplace' ),
                'singular_name'     => _x( 'Product Category', 'taxonomy singular name', 'digital-marketplace' ),
                'search_items'      => __( 'Search Categories', 'digital-marketplace' ),
                'all_items'         => __( 'All Categories', 'digital-marketplace' ),
                'edit_item'         => __( 'Edit Category', 'digital-marketplace' ),
                'update_item'       => __( 'Update Category', 'digital-marketplace' ),
                'add_new_item'      => __( 'Add New Category', 'digital-marketplace' ),
                'new_item_name'     => __( 'New Category Name', 'digital-marketplace' ),
                'menu_name'         => __( 'Categories', 'digital-marketplace' ),
            ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'product-category' ),
            'show_in_rest'      => true,
        ) );
    }
}
add_action( 'init', 'digital_marketplace_register_cpt' );

/**
 * Register Meta Boxes for Product Details (Price, Format, Features, Rating)
 * Can also be managed via Advanced Custom Fields (ACF) or WooCommerce.
 */
function digital_marketplace_add_meta_boxes() {
    add_meta_box(
        'marketplace_product_meta',
        __( 'Product Marketplace Details', 'digital-marketplace' ),
        'digital_marketplace_render_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'digital_marketplace_add_meta_boxes' );

function digital_marketplace_render_meta_box( $post ) {
    wp_nonce_field( 'digital_marketplace_save_meta', 'digital_marketplace_meta_nonce' );

    $price          = get_post_meta( $post->ID, '_product_price', true );
    $original_price = get_post_meta( $post->ID, '_product_original_price', true );
    $rating         = get_post_meta( $post->ID, '_product_rating', true );
    $review_count   = get_post_meta( $post->ID, '_product_review_count', true );
    $file_format    = get_post_meta( $post->ID, '_product_file_format', true );
    $file_size      = get_post_meta( $post->ID, '_product_file_size', true );
    $is_featured    = get_post_meta( $post->ID, '_product_is_featured', true );
    $features       = get_post_meta( $post->ID, '_product_features', true );
    ?>
    <p>
        <label><strong><?php esc_html_e( 'Price ($):', 'digital-marketplace' ); ?></strong></label><br>
        <input type="number" step="0.01" name="product_price" value="<?php echo esc_attr( $price ); ?>" style="width: 100%; max-width: 200px;">
    </p>
    <p>
        <label><strong><?php esc_html_e( 'Original Strikethrough Price ($):', 'digital-marketplace' ); ?></strong></label><br>
        <input type="number" step="0.01" name="product_original_price" value="<?php echo esc_attr( $original_price ); ?>" style="width: 100%; max-width: 200px;">
    </p>
    <p>
        <label><strong><?php esc_html_e( 'Rating (e.g. 4.9):', 'digital-marketplace' ); ?></strong></label><br>
        <input type="text" name="product_rating" value="<?php echo esc_attr( $rating ? $rating : '4.9' ); ?>" style="width: 100%; max-width: 200px;">
    </p>
    <p>
        <label><strong><?php esc_html_e( 'Review Count:', 'digital-marketplace' ); ?></strong></label><br>
        <input type="number" name="product_review_count" value="<?php echo esc_attr( $review_count ? $review_count : '85' ); ?>" style="width: 100%; max-width: 200px;">
    </p>
    <p>
        <label><strong><?php esc_html_e( 'File Format (e.g. FIG, TSX, CSS):', 'digital-marketplace' ); ?></strong></label><br>
        <input type="text" name="product_file_format" value="<?php echo esc_attr( $file_format ? $file_format : 'ZIP / Sources' ); ?>" style="width: 100%; max-width: 300px;">
    </p>
    <p>
        <label><strong><?php esc_html_e( 'File Size (e.g. 142 MB):', 'digital-marketplace' ); ?></strong></label><br>
        <input type="text" name="product_file_size" value="<?php echo esc_attr( $file_size ? $file_size : '45 MB' ); ?>" style="width: 100%; max-width: 200px;">
    </p>
    <p>
        <label>
            <input type="checkbox" name="product_is_featured" value="1" <?php checked( $is_featured, '1' ); ?>>
            <strong><?php esc_html_e( 'Mark as Featured / Staff Pick', 'digital-marketplace' ); ?></strong>
        </label>
    </p>
    <p>
        <label><strong><?php esc_html_e( 'Key Features (one per line):', 'digital-marketplace' ); ?></strong></label><br>
        <textarea name="product_features" rows="4" style="width: 100%;"><?php echo esc_textarea( $features ); ?></textarea>
    </p>
    <?php
}

function digital_marketplace_save_product_meta( $post_id ) {
    if ( ! isset( $_POST['digital_marketplace_meta_nonce'] ) || ! wp_verify_nonce( $_POST['digital_marketplace_meta_nonce'], 'digital_marketplace_save_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array(
        'product_price'          => '_product_price',
        'product_original_price' => '_product_original_price',
        'product_rating'         => '_product_rating',
        'product_review_count'   => '_product_review_count',
        'product_file_format'    => '_product_file_format',
        'product_file_size'      => '_product_file_size',
        'product_features'       => '_product_features',
    );

    foreach ( $fields as $input_key => $meta_key ) {
        if ( isset( $_POST[ $input_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $input_key ] ) );
        }
    }

    $featured = isset( $_POST['product_is_featured'] ) ? '1' : '0';
    update_post_meta( $post_id, '_product_is_featured', $featured );
}
add_action( 'save_post_product', 'digital_marketplace_save_product_meta' );

/**
 * Helper function: Retrieve product price (compatible with WooCommerce if active)
 */
function digital_marketplace_get_price( $post_id ) {
    if ( function_exists( 'wc_get_product' ) ) {
        $product = wc_get_product( $post_id );
        if ( $product ) {
            return $product->get_price();
        }
    }
    $price = get_post_meta( $post_id, '_product_price', true );
    return $price ? $price : '49.00';
}

/**
 * =========================================================================
 * BUNDLED COMMERCE PLUGIN AUTO-INSTALLER & PERSISTENCE
 * =========================================================================
 */

/**
 * Automatically copies and activates the bundled Digital Marketplace Commerce
 * plugin when this theme is activated.
 */
function digital_marketplace_auto_install_bundled_plugin() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_slug = 'digital-marketplace-commerce';
    $plugin_file = 'digital-marketplace-commerce/digital-marketplace-commerce.php';

    // 1. If plugin is already active, nothing to do.
    if ( is_plugin_active( $plugin_file ) ) {
        return;
    }

    $source_dir = get_template_directory() . '/' . $plugin_slug;
    $source_file = $source_dir . '/digital-marketplace-commerce.php';

    // Verify bundled plugin exists inside the theme
    if ( ! file_exists( $source_file ) ) {
        set_transient( 'dmc_install_error', __( 'Bundled plugin directory not found in theme.', 'digital-marketplace' ), DAY_IN_SECONDS );
        return;
    }

    $dest_dir = WP_PLUGIN_DIR . '/' . $plugin_slug;
    $dest_file = WP_PLUGIN_DIR . '/' . $plugin_file;

    // 2. If not already present in wp-content/plugins/, copy using WP_Filesystem API
    if ( ! file_exists( $dest_file ) ) {
        if ( ! function_exists( 'request_filesystem_credentials' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $url   = wp_nonce_url( admin_url( 'themes.php' ) );
        $creds = request_filesystem_credentials( $url, '', false, false, array() );

        // If credentials cannot be acquired or filesystem initialization fails, gracefully bail
        if ( false === $creds || ! WP_Filesystem( $creds ) ) {
            set_transient( 'dmc_install_error', __( 'WordPress Filesystem credentials unavailable or access denied.', 'digital-marketplace' ), DAY_IN_SECONDS );
            return;
        }

        global $wp_filesystem;

        if ( ! $wp_filesystem ) {
            set_transient( 'dmc_install_error', __( 'WordPress Filesystem API could not be initialized.', 'digital-marketplace' ), DAY_IN_SECONDS );
            return;
        }

        // Ensure destination folder exists
        if ( ! $wp_filesystem->is_dir( $dest_dir ) ) {
            $created = $wp_filesystem->mkdir( $dest_dir, FS_CHMOD_DIR );
            if ( ! $created ) {
                set_transient( 'dmc_install_error', __( 'Unable to create plugin destination folder in wp-content/plugins/.', 'digital-marketplace' ), DAY_IN_SECONDS );
                return;
            }
        }

        // Copy all plugin files recursively using core copy_dir()
        $copy_result = copy_dir( $source_dir, $dest_dir );
        if ( is_wp_error( $copy_result ) ) {
            set_transient( 'dmc_install_error', $copy_result->get_error_message(), DAY_IN_SECONDS );
            return;
        }
    }

    // 3. Activate the plugin
    $activate_result = activate_plugin( $plugin_file );
    if ( is_wp_error( $activate_result ) ) {
        set_transient( 'dmc_install_error', $activate_result->get_error_message(), DAY_IN_SECONDS );
        return;
    }

    // Clear any past failure notices on successful activation
    delete_transient( 'dmc_install_error' );
    update_option( 'dmc_plugin_auto_installed', 1 );
}
add_action( 'after_switch_theme', 'digital_marketplace_auto_install_bundled_plugin' );

/**
 * Handle manual dismissal of the plugin installation admin notice.
 */
function digital_marketplace_handle_plugin_notice_dismissal() {
    if ( isset( $_GET['dmc_dismiss_notice'] ) && check_admin_referer( 'dmc_dismiss_notice' ) ) {
        if ( current_user_can( 'activate_plugins' ) ) {
            update_user_meta( get_current_user_id(), 'dmc_dismiss_install_notice', 1 );
        }
        wp_safe_redirect( remove_query_arg( array( 'dmc_dismiss_notice', '_wpnonce' ) ) );
        exit;
    }
}
add_action( 'admin_init', 'digital_marketplace_handle_plugin_notice_dismissal' );

/**
 * Display a clear, dismissible admin notice if the plugin is not active.
 * Covers both failed auto-installs and instances where the theme was already
 * active before the plugin files were introduced.
 */
function digital_marketplace_plugin_admin_notice() {
    // Only display to administrators who can manage plugins
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    // Check if user previously dismissed this notice
    if ( get_user_meta( get_current_user_id(), 'dmc_dismiss_install_notice', true ) ) {
        return;
    }

    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_file = 'digital-marketplace-commerce/digital-marketplace-commerce.php';

    // If plugin is successfully active, do not display notice
    if ( is_plugin_active( $plugin_file ) ) {
        return;
    }

    // Only display notice if the bundled plugin folder exists in the theme
    $source_dir = get_template_directory() . '/digital-marketplace-commerce';
    if ( ! is_dir( $source_dir ) ) {
        return;
    }

    $plugins_url = admin_url( 'plugins.php' );
    $dismiss_url = wp_nonce_url( add_query_arg( 'dmc_dismiss_notice', '1' ), 'dmc_dismiss_notice' );
    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <strong><?php esc_html_e( 'Digital Marketplace Commerce:', 'digital-marketplace' ); ?></strong>
            <?php esc_html_e( 'The Digital Marketplace Commerce plugin could not be installed automatically. Please manually copy the \'digital-marketplace-commerce\' folder from your theme directory into wp-content/plugins/ and activate it from the Plugins screen.', 'digital-marketplace' ); ?>
        </p>
        <p>
            <a href="<?php echo esc_url( $plugins_url ); ?>" class="button button-primary">
                <?php esc_html_e( 'Go to Plugins Screen', 'digital-marketplace' ); ?>
            </a>
            <a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary" style="margin-left: 0.5rem;">
                <?php esc_html_e( 'Dismiss', 'digital-marketplace' ); ?>
            </a>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'digital_marketplace_plugin_admin_notice' );

/**
 * Fires when switching AWAY from this theme.
 *
 * We intentionally do NOT deactivate or delete the digital-marketplace-commerce
 * plugin, ensuring all customer orders, crypto transaction records, and store
 * settings persist safely even if the user switches themes in the future.
 */
function digital_marketplace_on_switch_theme() {
    // Intentionally empty: do not deactivate or delete the commerce plugin.
    // Preserves dmc_order posts, meta, and cryptocurrency settings.
}
add_action( 'switch_theme', 'digital_marketplace_on_switch_theme' );

/**
 * Handle frontend login errors: redirect back to /login with error query args
 * instead of exposing the WordPress core /wp-login.php screen.
 */
function digital_marketplace_login_failed_redirect( $username ) {
    $referrer = wp_get_referer();
    if ( $referrer && ! strstr( $referrer, 'wp-login' ) && ! strstr( $referrer, 'wp-admin' ) ) {
        wp_safe_redirect( add_query_arg( 'login', 'failed', home_url( '/login' ) ) );
        exit;
    }
}
add_action( 'wp_login_failed', 'digital_marketplace_login_failed_redirect' );

/**
 * Intercept empty user/pass submissions from custom frontend login.
 */
function digital_marketplace_authenticate_check( $user, $username, $password ) {
    $referrer = wp_get_referer();
    if ( $referrer && ! strstr( $referrer, 'wp-login' ) && ! strstr( $referrer, 'wp-admin' ) ) {
        if ( empty( $username ) || empty( $password ) ) {
            wp_safe_redirect( add_query_arg( 'login', 'empty', home_url( '/login' ) ) );
            exit;
        }
    }
    return $user;
}
add_filter( 'authenticate', 'digital_marketplace_authenticate_check', 20, 3 );



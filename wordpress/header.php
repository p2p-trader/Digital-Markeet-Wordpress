<?php
/**
 * The header for the Digital Marketplace theme
 *
 * @package Digital_Marketplace
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="marketplace-header" class="marketplace-header">
    <div class="site-container">
        <div class="header-inner">
            
            <!-- Brand Logo -->
            <a id="brand-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-logo">
                <div class="brand-icon">✨</div>
                <span><?php bloginfo( 'name' ); ?></span>
            </a>

            <!-- Header Search Bar (Desktop) -->
            <form id="header-search-form" role="search" method="get" class="header-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="search-input-wrap">
                    <span class="search-icon">🔍</span>
                    <input 
                        id="nav-search-input"
                        type="search" 
                        name="s" 
                        placeholder="<?php esc_attr_e( 'Search templates, UI kits, fonts, boilerplates...', 'digital-marketplace' ); ?>" 
                        value="<?php echo esc_attr( get_search_query() ); ?>"
                    />
                    <input type="hidden" name="post_type" value="product" />
                </div>
            </form>

            <!-- Desktop Navigation -->
            <nav id="desktop-nav-links" class="desktop-nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'digital-marketplace' ); ?>">
                <a id="nav-link-products" href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ? get_post_type_archive_link( 'product' ) : home_url( '/products' ) ); ?>">
                    <?php esc_html_e( 'Browse Products', 'digital-marketplace' ); ?>
                </a>

                <?php if ( is_user_logged_in() ) : 
                    $current_user = wp_get_current_user();
                    $account_page_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/account' );
                ?>
                    <a id="nav-link-account" href="<?php echo esc_url( $account_page_url ); ?>" class="nav-account-link">
                        <span>👤 <?php echo esc_html( $current_user->display_name ); ?></span>
                    </a>
                <?php else : 
                    $login_url = home_url( '/login' );
                ?>
                    <a id="nav-link-login" href="<?php echo esc_url( $login_url ); ?>">
                        <?php esc_html_e( 'Sign In', 'digital-marketplace' ); ?>
                    </a>
                <?php endif; ?>

                <!-- Cart Button -->
                <?php 
                    $cart_url = home_url( '/cart' );
                    $cart_count = 0;
                    if ( class_exists( 'DMC_Cart' ) ) {
                        $cart_count = DMC_Cart::get_item_count();
                    } elseif ( function_exists( 'WC' ) && WC()->cart ) {
                        $cart_count = WC()->cart->get_cart_contents_count();
                    }
                ?>
                <a id="nav-cart-btn" href="<?php echo esc_url( $cart_url ); ?>" class="nav-cart-badge-btn">
                    <span>🛍️ <?php esc_html_e( 'Cart', 'digital-marketplace' ); ?></span>
                    <span id="nav-cart-badge" class="cart-count-pill"><?php echo esc_html( $cart_count ); ?></span>
                </a>
            </nav>

            <!-- Mobile Menu Toggle Button -->
            <button id="wp-mobile-menu-toggle" class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'digital-marketplace' ); ?>">
                ☰
            </button>

        </div>

        <!-- Mobile Navigation Panel -->
        <div id="wp-mobile-nav-panel" class="mobile-nav-panel">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="margin-bottom: 1rem;">
                <div class="search-input-wrap">
                    <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search products...', 'digital-marketplace' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" />
                    <input type="hidden" name="post_type" value="product" />
                </div>
            </form>

            <div class="mobile-nav-links">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ? get_post_type_archive_link( 'product' ) : home_url( '/products' ) ); ?>">
                    <?php esc_html_e( 'Browse Products', 'digital-marketplace' ); ?>
                </a>
                <a href="<?php echo esc_url( $cart_url ); ?>">
                    <?php esc_html_e( 'Shopping Cart', 'digital-marketplace' ); ?>
                </a>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( $account_page_url ); ?>">
                        <?php esc_html_e( 'My Account', 'digital-marketplace' ); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( $login_url ); ?>">
                        <?php esc_html_e( 'Sign In / Register', 'digital-marketplace' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</header>

<main id="primary" class="site-main">

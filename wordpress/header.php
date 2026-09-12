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
                <span class="brand-icon-mark" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </span>
                <span class="brand-text-wrap">
                    <span class="brand-title"><?php bloginfo( 'name' ); ?></span>
                    <span class="brand-tagline-pill"><?php esc_html_e( 'Asset Studio', 'digital-marketplace' ); ?></span>
                </span>
            </a>

            <!-- Header Search Bar (Desktop) -->
            <form id="header-search-form" role="search" method="get" class="header-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="search-input-wrap">
                    <span class="search-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input 
                        id="nav-search-input"
                        type="search" 
                        name="s" 
                        placeholder="<?php esc_attr_e( 'Search UI kits, design systems, boilerplates, icons...', 'digital-marketplace' ); ?>" 
                        value="<?php echo esc_attr( get_search_query() ); ?>"
                    />
                    <input type="hidden" name="post_type" value="product" />
                    <span class="search-shortcut-hint" aria-hidden="true">⌘K</span>
                </div>
            </form>

            <!-- Desktop Navigation -->
            <nav id="desktop-nav-links" class="desktop-nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'digital-marketplace' ); ?>">
                <a id="nav-link-products" href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ? get_post_type_archive_link( 'product' ) : home_url( '/products' ) ); ?>" class="nav-item-link">
                    <span><?php esc_html_e( 'Browse Catalog', 'digital-marketplace' ); ?></span>
                </a>

                <?php if ( is_user_logged_in() ) : 
                    $current_user = wp_get_current_user();
                    $account_page_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/account' );
                ?>
                    <a id="nav-link-account" href="<?php echo esc_url( $account_page_url ); ?>" class="nav-account-link nav-item-link">
                        <span class="user-nav-avatar-mini" aria-hidden="true">
                            <?php echo get_avatar( $current_user->ID, 20 ); ?>
                        </span>
                        <span class="user-nav-name"><?php echo esc_html( $current_user->display_name ); ?></span>
                    </a>
                <?php else : 
                    $login_url = home_url( '/login' );
                ?>
                    <a id="nav-link-login" href="<?php echo esc_url( $login_url ); ?>" class="nav-item-link">
                        <span><?php esc_html_e( 'Sign In', 'digital-marketplace' ); ?></span>
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
                <a id="nav-cart-btn" href="<?php echo esc_url( $cart_url ); ?>" class="nav-cart-badge-btn" aria-label="<?php esc_attr_e( 'View Cart', 'digital-marketplace' ); ?>">
                    <span class="cart-btn-icon" aria-hidden="true">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </span>
                    <span class="cart-btn-label"><?php esc_html_e( 'Cart', 'digital-marketplace' ); ?></span>
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

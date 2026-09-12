<?php
/**
 * Template Name: Cart Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-cart-page" class="cart-page-wrapper">
    <div class="site-container">
        
        <header class="page-intro-header">
            <nav class="page-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'digital-marketplace' ); ?>">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'digital-marketplace' ); ?></a>
                <span class="crumb-sep">/</span>
                <span class="crumb-current"><?php esc_html_e( 'Shopping Cart', 'digital-marketplace' ); ?></span>
            </nav>
            <div class="page-intro-row">
                <div>
                    <h1 class="page-main-title"><?php esc_html_e( 'Your Shopping Cart', 'digital-marketplace' ); ?></h1>
                    <p class="page-main-sub"><?php esc_html_e( 'Review your selected digital assets before proceeding to peer-to-peer crypto checkout.', 'digital-marketplace' ); ?></p>
                </div>
                <div class="page-trust-badge">
                    <svg class="trust-badge-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                    <span class="trust-badge-text"><?php esc_html_e( 'Instant Crypto Settlement', 'digital-marketplace' ); ?></span>
                </div>
            </div>
        </header>

        <?php 
        // Execute Digital Marketplace Commerce Cart Shortcode
        if ( shortcode_exists( 'dmc_cart' ) ) {
            echo do_shortcode( '[dmc_cart]' );
        } elseif ( function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty() ) {
            echo do_shortcode( '[woocommerce_cart]' );
        } else {
            echo do_shortcode( '[dmc_cart]' );
        }
        ?>

    </div>
</div>

<?php get_footer(); ?>

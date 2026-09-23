<?php
/**
 * Template Name: Checkout Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-checkout-page" class="checkout-page-wrapper">
    <div class="site-container">

        <header class="page-intro-header">
            <nav class="page-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'digital-marketplace' ); ?>">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'digital-marketplace' ); ?></a>
                <span class="crumb-sep">/</span>
                <a href="<?php echo esc_url( home_url( '/cart' ) ); ?>"><?php esc_html_e( 'Cart', 'digital-marketplace' ); ?></a>
                <span class="crumb-sep">/</span>
                <span class="crumb-current"><?php esc_html_e( 'Checkout & Licensing', 'digital-marketplace' ); ?></span>
            </nav>
            <div class="page-intro-row">
                <div>
                    <h1 class="page-main-title"><?php esc_html_e( 'Checkout & Licensing', 'digital-marketplace' ); ?></h1>
                    <p class="page-main-sub"><?php esc_html_e( 'Complete your order with cryptocurrency to receive direct download links and license credentials.', 'digital-marketplace' ); ?></p>
                </div>
                <div class="page-trust-badge">
                    <svg class="trust-badge-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span class="trust-badge-text"><?php esc_html_e( 'P2P Encrypted Settlement', 'digital-marketplace' ); ?></span>
                </div>
            </div>
        </header>

        <?php 
        // Execute Digital Marketplace Commerce Checkout Shortcode
        if ( shortcode_exists( 'dmc_checkout' ) ) {
            echo do_shortcode( '[dmc_checkout]' );
        } elseif ( function_exists( 'is_checkout' ) && function_exists( 'WC' ) ) {
            echo do_shortcode( '[woocommerce_checkout]' );
        } else {
            echo do_shortcode( '[dmc_checkout]' );
        }
        ?>

    </div>
</div>

<?php get_footer(); ?>

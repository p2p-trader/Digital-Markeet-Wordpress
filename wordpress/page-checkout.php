<?php
/**
 * Template Name: Checkout Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-checkout-page" style="padding: 2.5rem 0 5rem;">
    <div class="site-container">

        <div style="margin-bottom: 2rem;">
            <h1 class="section-title"><?php esc_html_e( 'Checkout & Licensing', 'digital-marketplace' ); ?></h1>
            <p class="section-sub"><?php esc_html_e( 'Complete your order with cryptocurrency to receive direct download links and license credentials.', 'digital-marketplace' ); ?></p>
        </div>

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

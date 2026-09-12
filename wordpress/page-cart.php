<?php
/**
 * Template Name: Cart Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-cart-page" style="padding: 2.5rem 0 5rem;">
    <div class="site-container">
        
        <div style="margin-bottom: 2rem;">
            <h1 class="section-title"><?php esc_html_e( 'Your Shopping Cart', 'digital-marketplace' ); ?></h1>
            <p class="section-sub"><?php esc_html_e( 'Review your selected digital assets before proceeding to peer-to-peer crypto checkout.', 'digital-marketplace' ); ?></p>
        </div>

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

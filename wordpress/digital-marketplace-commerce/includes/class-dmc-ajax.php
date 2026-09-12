<?php
/**
 * DMC AJAX Endpoints
 *
 * Handles client-side cart interactions: adding items, updating quantities,
 * deleting items, and polling real-time cart state with nonces and sanitization.
 *
 * @package Digital_Marketplace_Commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DMC_Ajax {

    public static function init() {
        $actions = array(
            'dmc_add_to_cart'          => 'handle_add_to_cart',
            'dmc_update_cart_quantity' => 'handle_update_quantity',
            'dmc_remove_from_cart'     => 'handle_remove_from_cart',
            'dmc_get_cart'             => 'handle_get_cart',
        );

        foreach ( $actions as $action => $callback ) {
            add_action( 'wp_ajax_' . $action, array( __CLASS__, $callback ) );
            add_action( 'wp_ajax_nopriv_' . $action, array( __CLASS__, $callback ) );
        }
    }

    /**
     * Add product to cart via AJAX.
     */
    public static function handle_add_to_cart() {
        check_ajax_referer( 'dmc_cart_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $quantity   = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;

        if ( $product_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid product ID.', 'digital-marketplace-commerce' ) ) );
        }

        $post = get_post( $product_id );
        if ( ! $post || $post->post_type !== 'product' ) {
            wp_send_json_error( array( 'message' => __( 'Product not found.', 'digital-marketplace-commerce' ) ) );
        }

        DMC_Cart::add_item( $product_id, $quantity );

        wp_send_json_success( array(
            'message'    => sprintf( __( '"%s" added to cart.', 'digital-marketplace-commerce' ), get_the_title( $product_id ) ),
            'item_count' => DMC_Cart::get_item_count(),
            'subtotal'   => DMC_Cart::get_subtotal(),
            'total'      => DMC_Cart::get_total(),
        ) );
    }

    /**
     * Update item quantity in cart.
     */
    public static function handle_update_quantity() {
        check_ajax_referer( 'dmc_cart_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;

        if ( $product_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid product ID.', 'digital-marketplace-commerce' ) ) );
        }

        DMC_Cart::update_quantity( $product_id, $quantity );

        $subtotal = DMC_Cart::get_subtotal();
        $tax      = DMC_Cart::get_tax( $subtotal );
        $total    = DMC_Cart::get_total();

        wp_send_json_success( array(
            'item_count' => DMC_Cart::get_item_count(),
            'subtotal'   => number_format( $subtotal, 2 ),
            'tax'        => number_format( $tax, 2 ),
            'total'      => number_format( $total, 2 ),
        ) );
    }

    /**
     * Remove item from cart.
     */
    public static function handle_remove_from_cart() {
        check_ajax_referer( 'dmc_cart_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

        if ( $product_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid product ID.', 'digital-marketplace-commerce' ) ) );
        }

        DMC_Cart::remove_item( $product_id );

        $subtotal = DMC_Cart::get_subtotal();
        $tax      = DMC_Cart::get_tax( $subtotal );
        $total    = DMC_Cart::get_total();

        wp_send_json_success( array(
            'message'    => __( 'Item removed from cart.', 'digital-marketplace-commerce' ),
            'item_count' => DMC_Cart::get_item_count(),
            'subtotal'   => number_format( $subtotal, 2 ),
            'tax'        => number_format( $tax, 2 ),
            'total'      => number_format( $total, 2 ),
            'is_empty'   => DMC_Cart::get_item_count() === 0,
        ) );
    }

    /**
     * Get live cart state.
     */
    public static function handle_get_cart() {
        check_ajax_referer( 'dmc_cart_nonce', 'nonce' );

        $cart     = DMC_Cart::get_cart();
        $subtotal = DMC_Cart::get_subtotal();
        $tax      = DMC_Cart::get_tax( $subtotal );
        $total    = DMC_Cart::get_total();

        wp_send_json_success( array(
            'items'      => array_values( $cart ),
            'item_count' => DMC_Cart::get_item_count(),
            'subtotal'   => number_format( $subtotal, 2 ),
            'tax'        => number_format( $tax, 2 ),
            'total'      => number_format( $total, 2 ),
        ) );
    }
}

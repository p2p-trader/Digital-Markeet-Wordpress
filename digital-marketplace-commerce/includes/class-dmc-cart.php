<?php
/**
 * DMC Cart Manager
 *
 * Handles cart session/cookie persistence using tamper-resistant signed cookies,
 * item manipulation, and financial calculations.
 *
 * @package Digital_Marketplace_Commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DMC_Cart {

    const COOKIE_NAME = 'dmc_cart_session';
    const COOKIE_EXPIRY = 604800; // 7 days in seconds

    /**
     * Get signing secret key from WordPress salts.
     */
    private static function get_signing_key() {
        if ( defined( 'AUTH_KEY' ) && AUTH_KEY ) {
            return AUTH_KEY;
        }
        return 'dmc_default_secure_cart_key_2026';
    }

    /**
     * Retrieve all cart items from signed cookie.
     *
     * @return array
     */
    public static function get_cart() {
        if ( ! isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
            return array();
        }

        $raw = sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) );
        $parts = explode( '::', $raw, 2 );

        if ( count( $parts ) !== 2 ) {
            return array();
        }

        list( $signature, $payload_b64 ) = $parts;
        $expected_signature = hash_hmac( 'sha256', $payload_b64, self::get_signing_key() );

        // Constant-time comparison to prevent timing attacks
        if ( ! hash_equals( $expected_signature, $signature ) ) {
            return array();
        }

        $json = base64_decode( $payload_b64 );
        $cart = json_decode( $json, true );

        if ( ! is_array( $cart ) ) {
            return array();
        }

        // Validate each item structure
        $clean_cart = array();
        foreach ( $cart as $item_id => $item ) {
            $pid = absint( $item['id'] ?? $item_id );
            if ( $pid <= 0 ) continue;

            $qty = max( 1, absint( $item['quantity'] ?? 1 ) );
            $post = get_post( $pid );
            if ( ! $post || $post->post_type !== 'product' ) continue;

            // Fetch live price from product meta
            $price = floatval( get_post_meta( $pid, '_product_price', true ) );
            if ( $price <= 0 ) {
                $price = 49.00;
            }

            $thumb_url = '';
            if ( has_post_thumbnail( $pid ) ) {
                $thumb_url = get_the_post_thumbnail_url( $pid, 'thumbnail' );
            }
            if ( empty( $thumb_url ) ) {
                $thumb_url = 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=200&q=80';
            }

            $clean_cart[ $pid ] = array(
                'id'       => $pid,
                'title'    => get_the_title( $pid ),
                'price'    => $price,
                'quantity' => $qty,
                'image'    => $thumb_url,
                'format'   => get_post_meta( $pid, '_product_file_format', true ) ?: 'ZIP Package',
                'subtotal' => round( $price * $qty, 2 ),
            );
        }

        return $clean_cart;
    }

    /**
     * Save cart to signed cookie.
     *
     * @param array $cart
     */
    public static function save_cart( $cart ) {
        $clean_data = array();
        foreach ( $cart as $pid => $item ) {
            $clean_data[ absint( $pid ) ] = array(
                'id'       => absint( $item['id'] ),
                'quantity' => max( 1, absint( $item['quantity'] ) ),
            );
        }

        $json = wp_json_encode( $clean_data );
        $payload_b64 = base64_encode( $json );
        $signature = hash_hmac( 'sha256', $payload_b64, self::get_signing_key() );
        $cookie_val = $signature . '::' . $payload_b64;

        setcookie(
            self::COOKIE_NAME,
            $cookie_val,
            time() + self::COOKIE_EXPIRY,
            COOKIEPATH ?: '/',
            COOKIE_DOMAIN ?: '',
            is_ssl(),
            true // httponly
        );

        $_COOKIE[ self::COOKIE_NAME ] = $cookie_val;
    }

    /**
     * Clear all items from the cart.
     */
    public static function clear_cart() {
        setcookie(
            self::COOKIE_NAME,
            '',
            time() - 3600,
            COOKIEPATH ?: '/',
            COOKIE_DOMAIN ?: '',
            is_ssl(),
            true
        );
        unset( $_COOKIE[ self::COOKIE_NAME ] );
    }

    /**
     * Add an item to the cart.
     */
    public static function add_item( $product_id, $quantity = 1 ) {
        $product_id = absint( $product_id );
        $quantity   = max( 1, absint( $quantity ) );
        $cart       = self::get_cart();

        if ( isset( $cart[ $product_id ] ) ) {
            $cart[ $product_id ]['quantity'] += $quantity;
        } else {
            $post = get_post( $product_id );
            if ( ! $post ) {
                return false;
            }

            $price = floatval( get_post_meta( $product_id, '_product_price', true ) );
            if ( $price <= 0 ) $price = 49.00;

            $cart[ $product_id ] = array(
                'id'       => $product_id,
                'title'    => get_the_title( $product_id ),
                'price'    => $price,
                'quantity' => $quantity,
            );
        }

        self::save_cart( $cart );
        return true;
    }

    /**
     * Update quantity of a product in the cart.
     */
    public static function update_quantity( $product_id, $quantity ) {
        $product_id = absint( $product_id );
        $quantity   = absint( $quantity );
        $cart       = self::get_cart();

        if ( $quantity <= 0 ) {
            unset( $cart[ $product_id ] );
        } else if ( isset( $cart[ $product_id ] ) ) {
            $cart[ $product_id ]['quantity'] = $quantity;
        }

        self::save_cart( $cart );
        return true;
    }

    /**
     * Remove an item from the cart.
     */
    public static function remove_item( $product_id ) {
        $product_id = absint( $product_id );
        $cart       = self::get_cart();

        if ( isset( $cart[ $product_id ] ) ) {
            unset( $cart[ $product_id ] );
            self::save_cart( $cart );
        }

        return true;
    }

    /**
     * Calculate cart totals.
     */
    public static function get_subtotal() {
        $cart = self::get_cart();
        $subtotal = 0.0;
        foreach ( $cart as $item ) {
            $subtotal += floatval( $item['price'] ) * intval( $item['quantity'] );
        }
        return round( $subtotal, 2 );
    }

    public static function get_tax( $subtotal = null ) {
        if ( is_null( $subtotal ) ) {
            $subtotal = self::get_subtotal();
        }
        // Flat 8% digital goods processing/sales tax rate
        return round( $subtotal * 0.08, 2 );
    }

    public static function get_total() {
        $subtotal = self::get_subtotal();
        $tax      = self::get_tax( $subtotal );
        return round( $subtotal + $tax, 2 );
    }

    public static function get_item_count() {
        $cart = self::get_cart();
        $count = 0;
        foreach ( $cart as $item ) {
            $count += intval( $item['quantity'] );
        }
        return $count;
    }
}

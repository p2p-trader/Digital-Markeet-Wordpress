<?php
/**
 * DMC Checkout & Cart Shortcodes & Processing
 *
 * Handles [dmc_cart], [dmc_checkout], order creation into 'dmc_order',
 * crypto payment instructions, email dispatch, and order confirmation.
 *
 * @package Digital_Marketplace_Commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DMC_Checkout {

    public static function init() {
        add_shortcode( 'dmc_cart', array( __CLASS__, 'render_cart_shortcode' ) );
        add_shortcode( 'dmc_checkout', array( __CLASS__, 'render_checkout_shortcode' ) );
        add_action( 'template_redirect', array( __CLASS__, 'process_checkout_submission' ) );
    }

    /**
     * Shortcode: [dmc_cart]
     * Renders live cart contents dynamically.
     */
    public static function render_cart_shortcode() {
        $cart       = DMC_Cart::get_cart();
        $subtotal   = DMC_Cart::get_subtotal();
        $tax        = DMC_Cart::get_tax( $subtotal );
        $total      = DMC_Cart::get_total();
        $item_count = DMC_Cart::get_item_count();

        ob_start();
        ?>
        <div id="dmc-cart-wrapper" class="dmc-cart-container" data-item-count="<?php echo esc_attr( $item_count ); ?>">
            <?php if ( empty( $cart ) ) : ?>
                <div class="cart-items-card" style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🛍️</div>
                    <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text-main);">
                        <?php esc_html_e( 'Your shopping cart is empty', 'digital-marketplace-commerce' ); ?>
                    </h2>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 2rem;">
                        <?php esc_html_e( 'Explore our digital marketplace for UI kits, developer stacks, and fonts.', 'digital-marketplace-commerce' ); ?>
                    </p>
                    <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-primary btn-lg">
                        <?php esc_html_e( 'Explore Products Catalog →', 'digital-marketplace-commerce' ); ?>
                    </a>
                </div>
            <?php else : ?>
                <div class="cart-page-layout">
                    <!-- Left: Real Dynamic Cart Items -->
                    <div class="cart-items-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 0.5rem;">
                            <span style="font-weight: 800; font-size: 0.9rem;"><?php esc_html_e( 'Product Item', 'digital-marketplace-commerce' ); ?></span>
                            <span style="font-weight: 800; font-size: 0.9rem;"><?php esc_html_e( 'Price / Quantity', 'digital-marketplace-commerce' ); ?></span>
                        </div>

                        <div id="dmc-cart-items-list">
                            <?php foreach ( $cart as $pid => $item ) : 
                                $price = floatval( $item['price'] );
                                $qty   = intval( $item['quantity'] );
                                $line_subtotal = round( $price * $qty, 2 );
                            ?>
                                <div class="cart-item-row dmc-cart-row" data-product-id="<?php echo esc_attr( $pid ); ?>" data-price="<?php echo esc_attr( $price ); ?>">
                                    <div class="cart-item-info">
                                        <div class="cart-item-thumb">
                                            <img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" />
                                        </div>
                                        <div>
                                            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.2rem;">
                                                <a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
                                            </h3>
                                            <p style="font-size: 0.775rem; color: var(--text-muted);">
                                                <?php echo esc_html( $item['format'] ); ?> • $<?php echo esc_html( number_format( $price, 2 ) ); ?> <?php esc_html_e( 'each', 'digital-marketplace-commerce' ); ?>
                                            </p>
                                            <button type="button" class="dmc-cart-remove-btn" data-product-id="<?php echo esc_attr( $pid ); ?>" style="background:none; border:none; color:#ef4444; font-size:0.75rem; font-weight:600; cursor:pointer; padding:0; margin-top:0.35rem;">
                                                ✕ <?php esc_html_e( 'Remove', 'digital-marketplace-commerce' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                                        <div class="qty-stepper dmc-qty-stepper">
                                            <button type="button" class="qty-btn qty-btn-minus dmc-qty-minus">-</button>
                                            <span class="qty-val dmc-qty-val"><?php echo esc_html( $qty ); ?></span>
                                            <button type="button" class="qty-btn qty-btn-plus dmc-qty-plus">+</button>
                                        </div>
                                        <div style="text-align: right; min-width: 75px;">
                                            <span class="cart-item-subtotal dmc-row-subtotal" style="font-weight: 800; font-size: 1.05rem;">
                                                $<?php echo esc_html( number_format( $line_subtotal, 2 ) ); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-sm">
                                ← <?php esc_html_e( 'Continue Shopping', 'digital-marketplace-commerce' ); ?>
                            </a>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                ⚡ <?php esc_html_e( 'Direct peer-to-peer crypto fulfillment.', 'digital-marketplace-commerce' ); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Right: Dynamic Order Summary -->
                    <div>
                        <div class="summary-card">
                            <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--text-main);">
                                <?php esc_html_e( 'Order Summary', 'digital-marketplace-commerce' ); ?>
                            </h2>

                            <!-- Coupon Box -->
                            <form id="marketplace-coupon-form" style="margin-bottom: 1.25rem;">
                                <label for="coupon-input" class="form-label"><?php esc_html_e( 'Discount / Promo Code:', 'digital-marketplace-commerce' ); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input id="coupon-input" type="text" class="form-control" placeholder="e.g. WELCOME10" style="text-transform: uppercase;" />
                                    <button type="submit" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                                        <?php esc_html_e( 'Apply', 'digital-marketplace-commerce' ); ?>
                                    </button>
                                </div>
                                <p id="coupon-feedback" style="font-size: 0.75rem; margin-top: 0.35rem;"></p>
                            </form>

                            <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.85rem;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--text-muted);"><?php esc_html_e( 'Subtotal', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-display-subtotal" style="font-weight: 700;">$<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></span>
                                </div>
                                <div id="cart-discount-row" style="display: none; justify-content: space-between; color: #059669;">
                                    <span><?php esc_html_e( 'Promo Discount', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-discount-val">-$0.00</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--text-muted);"><?php esc_html_e( 'Estimated Tax (8%)', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-display-tax" style="font-weight: 700;">$<?php echo esc_html( number_format( $tax, 2 ) ); ?></span>
                                </div>
                                <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; margin-top: 0.5rem; display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 900;">
                                    <span><?php esc_html_e( 'Total', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-display-total">$<?php echo esc_html( number_format( $total, 2 ) ); ?></span>
                                </div>
                            </div>

                            <div style="margin-top: 1.5rem;">
                                <a href="<?php echo esc_url( home_url( '/checkout' ) ); ?>" class="btn btn-primary btn-block btn-lg">
                                    <?php esc_html_e( 'Proceed to Checkout →', 'digital-marketplace-commerce' ); ?>
                                </a>
                            </div>

                            <div style="margin-top: 1.25rem; padding: 0.85rem; background-color: var(--bg-subtle); border-radius: var(--radius-md); font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;">
                                <strong>🪙 <?php esc_html_e( 'Crypto Commerce Active:', 'digital-marketplace-commerce' ); ?></strong>
                                <?php esc_html_e( 'Orders are stored in the DMC Orders system with cryptocurrency settlement instructions.', 'digital-marketplace-commerce' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode: [dmc_checkout]
     * Renders real checkout form OR order confirmation if order placed.
     */
    public static function render_checkout_shortcode() {
        // Check if viewing Order Confirmation
        if ( isset( $_GET['dmc_order_id'] ) && isset( $_GET['order_key'] ) ) {
            return self::render_order_confirmation( absint( $_GET['dmc_order_id'] ), sanitize_text_field( wp_unslash( $_GET['order_key'] ) ) );
        }

        $cart       = DMC_Cart::get_cart();
        $subtotal   = DMC_Cart::get_subtotal();
        $tax        = DMC_Cart::get_tax( $subtotal );
        $total      = DMC_Cart::get_total();

        if ( empty( $cart ) ) {
            ob_start();
            ?>
            <div class="checkout-form-card" style="text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🛒</div>
                <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem;">
                    <?php esc_html_e( 'No items to checkout', 'digital-marketplace-commerce' ); ?>
                </h2>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 2rem;">
                    <?php esc_html_e( 'Please add one or more digital products to your cart before proceeding to checkout.', 'digital-marketplace-commerce' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-primary btn-lg">
                    <?php esc_html_e( 'Browse Products →', 'digital-marketplace-commerce' ); ?>
                </a>
            </div>
            <?php
            return ob_get_clean();
        }

        $wallet_btc  = get_option( 'dmc_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh' );
        $wallet_eth  = get_option( 'dmc_wallet_eth', '0x71C...B29' );
        $wallet_usdt = get_option( 'dmc_wallet_usdt', 'TXj...99A' );

        $current_user = wp_get_current_user();
        $default_email = $current_user->exists() ? $current_user->user_email : '';
        $default_first = $current_user->exists() ? $current_user->user_firstname : '';
        $default_last  = $current_user->exists() ? $current_user->user_lastname : '';

        ob_start();
        ?>
        <div class="checkout-page-layout">
            
            <!-- Left: Checkout Form with Nonce -->
            <div class="checkout-form-card">
                <h2 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.25rem;">
                    <?php esc_html_e( '1. Customer Details', 'digital-marketplace-commerce' ); ?>
                </h2>

                <form id="dmc-checkout-form" method="post" action="">
                    <input type="hidden" name="dmc_action" value="place_order" />
                    <?php wp_nonce_field( 'dmc_process_checkout', 'dmc_checkout_nonce' ); ?>

                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label for="dmc_first_name" class="form-label"><?php esc_html_e( 'First Name *', 'digital-marketplace-commerce' ); ?></label>
                            <input type="text" id="dmc_first_name" name="dmc_first_name" class="form-control" required value="<?php echo esc_attr( $default_first ); ?>" placeholder="Alex" />
                            <span class="field-error" style="display:none; color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">
                                <?php esc_html_e( 'First name is required.', 'digital-marketplace-commerce' ); ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="dmc_last_name" class="form-label"><?php esc_html_e( 'Last Name *', 'digital-marketplace-commerce' ); ?></label>
                            <input type="text" id="dmc_last_name" name="dmc_last_name" class="form-control" required value="<?php echo esc_attr( $default_last ); ?>" placeholder="Rivera" />
                            <span class="field-error" style="display:none; color:#ef4444; font-size:0.75rem; margin-top:0.25rem;">
                                <?php esc_html_e( 'Last name is required.', 'digital-marketplace-commerce' ); ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dmc_email" class="form-label"><?php esc_html_e( 'Delivery Email Address *', 'digital-marketplace-commerce' ); ?></label>
                        <input type="email" id="dmc_email" name="dmc_email" class="form-control" required value="<?php echo esc_attr( $default_email ); ?>" placeholder="alex.rivera@example.com" />
                        <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                            <?php esc_html_e( 'Order ID, payment confirmation, and digital download links will be sent here.', 'digital-marketplace-commerce' ); ?>
                        </small>
                    </div>

                    <!-- Payment Method: Crypto -->
                    <h2 style="font-size: 1.25rem; font-weight: 800; margin: 2rem 0 1.25rem; border-top: 1px solid var(--border-subtle); padding-top: 1.5rem;">
                        <?php esc_html_e( '2. Payment Method: Cryptocurrency', 'digital-marketplace-commerce' ); ?>
                    </h2>

                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 1.5rem;">
                        <p style="font-size: 0.85rem; color: #334155; margin-bottom: 1rem; line-height: 1.5;">
                            <?php esc_html_e( 'Select the cryptocurrency network you wish to send payment with:', 'digital-marketplace-commerce' ); ?>
                        </p>

                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; border: 2px solid var(--color-primary); border-radius: var(--radius-md); background: #fff; cursor: pointer;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <input type="radio" name="dmc_crypto_coin" value="Bitcoin (BTC)" checked />
                                    <span style="font-weight: 700; font-size: 0.9rem;">₿ Bitcoin (BTC)</span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;"><?php echo esc_html( substr( $wallet_btc, 0, 10 ) . '...' . substr( $wallet_btc, -6 ) ); ?></span>
                            </label>

                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); background: #fff; cursor: pointer;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <input type="radio" name="dmc_crypto_coin" value="Ethereum (ETH)" />
                                    <span style="font-weight: 700; font-size: 0.9rem;">Ξ Ethereum (ETH)</span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;"><?php echo esc_html( substr( $wallet_eth, 0, 8 ) . '...' . substr( $wallet_eth, -6 ) ); ?></span>
                            </label>

                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); background: #fff; cursor: pointer;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <input type="radio" name="dmc_crypto_coin" value="Tether (USDT)" />
                                    <span style="font-weight: 700; font-size: 0.9rem;">₮ Tether (USDT)</span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;"><?php echo esc_html( substr( $wallet_usdt, 0, 8 ) . '...' . substr( $wallet_usdt, -6 ) ); ?></span>
                            </label>
                        </div>
                    </div>

                    <div style="margin-top: 1.75rem;">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            🔒 <?php printf( esc_html__( 'Place Order & Pay with Crypto ($%s)', 'digital-marketplace-commerce' ), esc_html( number_format( $total, 2 ) ) ); ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Real Cart Order Summary -->
            <div>
                <div class="summary-card">
                    <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--text-main);">
                        <?php esc_html_e( 'Order Review', 'digital-marketplace-commerce' ); ?>
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 0.85rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1rem;">
                        <?php foreach ( $cart as $item ) : ?>
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                <div>
                                    <span style="font-weight: 700;"><?php echo esc_html( $item['title'] ); ?></span>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">Qty: <?php echo esc_html( $item['quantity'] ); ?> • <?php echo esc_html( $item['format'] ); ?></p>
                                </div>
                                <span style="font-weight: 700;">$<?php echo esc_html( number_format( $item['price'] * $item['quantity'], 2 ) ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);"><?php esc_html_e( 'Subtotal', 'digital-marketplace-commerce' ); ?></span>
                            <span style="font-weight: 700;">$<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);"><?php esc_html_e( 'Tax (8%)', 'digital-marketplace-commerce' ); ?></span>
                            <span style="font-weight: 700;">$<?php echo esc_html( number_format( $tax, 2 ) ); ?></span>
                        </div>
                        <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; margin-top: 0.5rem; display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 900;">
                            <span><?php esc_html_e( 'Total Due', 'digital-marketplace-commerce' ); ?></span>
                            <span>$<?php echo esc_html( number_format( $total, 2 ) ); ?></span>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.75rem; color: var(--text-muted);">
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: var(--color-success);">✓</span>
                            <span><?php esc_html_e( 'Zero intermediary payment processing fees', 'digital-marketplace-commerce' ); ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: var(--color-success);">✓</span>
                            <span><?php esc_html_e( 'Instant Order ID generated on submit', 'digital-marketplace-commerce' ); ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: var(--color-success);">✓</span>
                            <span><?php esc_html_e( 'Payment instructions emailed immediately', 'digital-marketplace-commerce' ); ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Process checkout form POST submission.
     */
    public static function process_checkout_submission() {
        if ( ! isset( $_POST['dmc_action'] ) || $_POST['dmc_action'] !== 'place_order' ) {
            return;
        }

        if ( ! isset( $_POST['dmc_checkout_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dmc_checkout_nonce'] ) ), 'dmc_process_checkout' ) ) {
            wp_die( esc_html__( 'Security verification failed. Please try again.', 'digital-marketplace-commerce' ) );
        }

        $cart = DMC_Cart::get_cart();
        if ( empty( $cart ) ) {
            wp_safe_redirect( home_url( '/cart' ) );
            exit;
        }

        $first_name = isset( $_POST['dmc_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['dmc_first_name'] ) ) : '';
        $last_name  = isset( $_POST['dmc_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['dmc_last_name'] ) ) : '';
        $email      = isset( $_POST['dmc_email'] ) ? sanitize_email( wp_unslash( $_POST['dmc_email'] ) ) : '';
        $coin       = isset( $_POST['dmc_crypto_coin'] ) ? sanitize_text_field( wp_unslash( $_POST['dmc_crypto_coin'] ) ) : 'Bitcoin (BTC)';

        $customer_name = trim( $first_name . ' ' . $last_name );
        if ( empty( $customer_name ) ) $customer_name = 'Customer';
        if ( empty( $email ) || ! is_email( $email ) ) {
            wp_die( esc_html__( 'Please provide a valid email address.', 'digital-marketplace-commerce' ) );
        }

        // Determine destination wallet address from plugin settings
        if ( strpos( $coin, 'BTC' ) !== false ) {
            $wallet_address = get_option( 'dmc_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh' );
        } elseif ( strpos( $coin, 'ETH' ) !== false ) {
            $wallet_address = get_option( 'dmc_wallet_eth', '0x71C...B29' );
        } else {
            $wallet_address = get_option( 'dmc_wallet_usdt', 'TXj...99A' );
        }

        $subtotal = DMC_Cart::get_subtotal();
        $tax      = DMC_Cart::get_tax( $subtotal );
        $total    = DMC_Cart::get_total();

        // Create random order key for secure front-end access
        $order_key = wp_generate_password( 24, false );

        // Insert real dmc_order post
        $order_post_data = array(
            'post_title'   => 'Order #' . time(),
            'post_status'  => 'publish',
            'post_type'    => 'dmc_order',
            'post_author'  => get_current_user_id() ?: 1,
        );

        $order_id = wp_insert_post( $order_post_data );
        if ( is_wp_error( $order_id ) || ! $order_id ) {
            wp_die( esc_html__( 'Unable to create order record. Please try again.', 'digital-marketplace-commerce' ) );
        }

        // Update post title with actual ID
        wp_update_post( array(
            'ID'         => $order_id,
            'post_title' => 'Order #' . $order_id,
        ) );

        // Save metadata
        update_post_meta( $order_id, '_dmc_customer_name', $customer_name );
        update_post_meta( $order_id, '_dmc_customer_email', $email );
        update_post_meta( $order_id, '_dmc_order_total', $total );
        update_post_meta( $order_id, '_dmc_order_status', DMC_Post_Type::STATUS_AWAITING_PAYMENT );
        update_post_meta( $order_id, '_dmc_payment_method', 'Crypto' );
        update_post_meta( $order_id, '_dmc_crypto_coin', $coin );
        update_post_meta( $order_id, '_dmc_crypto_wallet', $wallet_address );
        update_post_meta( $order_id, '_dmc_order_items', array_values( $cart ) );
        update_post_meta( $order_id, '_dmc_order_key', $order_key );
        update_post_meta( $order_id, '_dmc_date_created', current_time( 'mysql' ) );

        // Clear shopping cart cookie
        DMC_Cart::clear_cart();

        // Send order confirmation email via wp_mail()
        self::send_order_email( $order_id, $customer_name, $email, $total, $coin, $wallet_address );

        // Redirect to confirmation view
        $confirmation_url = add_query_arg( array(
            'dmc_order_id' => $order_id,
            'order_key'    => $order_key,
        ), home_url( '/checkout' ) );

        wp_safe_redirect( $confirmation_url );
        exit;
    }

    /**
     * Send email to customer with payment instructions.
     */
    private static function send_order_email( $order_id, $name, $email, $total, $coin, $wallet ) {
        $template = get_option( 'dmc_email_template' );
        if ( empty( $template ) ) {
            $template = "Hi {customer_name},\n\nThank you for your order #{order_id}!\nOrder Total: \${order_total}\nPayment Method: {crypto_coin}\n\nSend payment to:\n{crypto_address}\n\nDo not close your browser until you've saved your Order ID: {order_id}.";
        }

        $body = str_replace(
            array( '{customer_name}', '{order_id}', '{order_total}', '{crypto_coin}', '{crypto_address}' ),
            array( $name, $order_id, number_format( $total, 2 ), $coin, $wallet ),
            $template
        );

        $subject = sprintf( __( 'Order #%d Confirmation & Payment Instructions', 'digital-marketplace-commerce' ), $order_id );
        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

        wp_mail( $email, $subject, $body, $headers );
    }

    /**
     * Render the order confirmation screen.
     */
    private static function render_order_confirmation( $order_id, $order_key ) {
        $stored_key = get_post_meta( $order_id, '_dmc_order_key', true );
        if ( ! $stored_key || ! hash_equals( $stored_key, $order_key ) ) {
            return '<div class="checkout-form-card"><p>' . esc_html__( 'Invalid order verification key.', 'digital-marketplace-commerce' ) . '</p></div>';
        }

        $customer_name  = get_post_meta( $order_id, '_dmc_customer_name', true );
        $customer_email = get_post_meta( $order_id, '_dmc_customer_email', true );
        $total          = floatval( get_post_meta( $order_id, '_dmc_order_total', true ) );
        $status         = get_post_meta( $order_id, '_dmc_order_status', true );
        $coin           = get_post_meta( $order_id, '_dmc_crypto_coin', true );
        $wallet_address = get_post_meta( $order_id, '_dmc_crypto_wallet', true );
        $items          = get_post_meta( $order_id, '_dmc_order_items', true );

        ob_start();
        ?>
        <div class="checkout-form-card" style="max-width: 780px; margin: 0 auto; border: 2px solid var(--color-primary);">
            
            <div style="text-align: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background-color: var(--color-accent-bg); font-size: 1.75rem; margin-bottom: 0.75rem;">
                    🪙
                </div>
                <h1 style="font-size: 1.75rem; font-weight: 900; letter-spacing: -0.02em; color: var(--text-main); margin-bottom: 0.25rem;">
                    <?php esc_html_e( 'Order Placed — Payment Required', 'digital-marketplace-commerce' ); ?>
                </h1>
                <p style="font-size: 0.95rem; color: var(--text-muted);">
                    <?php printf( esc_html__( 'Order Reference: #%d', 'digital-marketplace-commerce' ), esc_html( $order_id ) ); ?> • 
                    <span class="status-badge" style="background:#fef3c7; color:#92400e;"><?php echo esc_html( $status ); ?></span>
                </p>
            </div>

            <!-- Prominent Warning Notice -->
            <div style="background-color: #fef3c7; border-left: 5px solid var(--color-accent); padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <p style="font-size: 1rem; font-weight: 800; color: #92400e; margin-bottom: 0.5rem;">
                    ⚠️ <?php esc_html_e( 'Important Payment Action:', 'digital-marketplace-commerce' ); ?>
                </p>
                <p style="font-size: 0.925rem; line-height: 1.6; color: #78350f; margin: 0;">
                    <?php 
                    /* translators: %d: Order ID */
                    printf( esc_html__( 'Send the exact total shown to this address, then wait for confirmation. Do not close this page until you\'ve saved your Order ID: #%d.', 'digital-marketplace-commerce' ), esc_html( $order_id ) ); 
                    ?>
                </p>
            </div>

            <!-- Payment Details Box -->
            <div style="background-color: var(--bg-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1rem;">
                    <div>
                        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;"><?php esc_html_e( 'Selected Network / Coin', 'digital-marketplace-commerce' ); ?></span>
                        <h3 style="font-size: 1.15rem; font-weight: 800;"><?php echo esc_html( $coin ); ?></h3>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;"><?php esc_html_e( 'Exact Total Due', 'digital-marketplace-commerce' ); ?></span>
                        <h2 style="font-size: 1.75rem; font-weight: 900; color: var(--text-main);">$<?php echo esc_html( number_format( $total, 2 ) ); ?></h2>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.4rem;">
                        <?php esc_html_e( 'Send Payment to this Wallet Address:', 'digital-marketplace-commerce' ); ?>
                    </label>
                    <div style="display: flex; gap: 0.5rem; align-items: center; background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.5rem 0.75rem;">
                        <code id="dmc-wallet-copy-text" style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); word-break: break-all; flex: 1;">
                            <?php echo esc_html( $wallet_address ); ?>
                        </code>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="navigator.clipboard.writeText('<?php echo esc_js( $wallet_address ); ?>'); alert('Wallet address copied to clipboard!');">
                            📋 <?php esc_html_e( 'Copy', 'digital-marketplace-commerce' ); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Notification -->
            <div style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                <p>
                    ✉️ <?php printf( esc_html__( 'A payment confirmation and invoice receipt has been dispatched to %s.', 'digital-marketplace-commerce' ), '<strong>' . esc_html( $customer_email ) . '</strong>' ); ?>
                </p>
                <p style="margin-top: 0.5rem;">
                    🛡️ <?php esc_html_e( 'Once our team manually validates your transaction hash on the blockchain, your order status will automatically switch to Completed, unlocking your instant asset downloads.', 'digital-marketplace-commerce' ); ?>
                </p>
            </div>

            <!-- Items Purchased Summary -->
            <h3 style="font-size: 1rem; font-weight: 800; margin-bottom: 0.75rem;">
                <?php esc_html_e( 'Items in this Order:', 'digital-marketplace-commerce' ); ?>
            </h3>
            <div style="border: 1px solid var(--border-subtle); border-radius: var(--radius-md); overflow: hidden; margin-bottom: 2rem;">
                <?php if ( is_array( $items ) ) : ?>
                    <?php foreach ( $items as $it ) : ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-subtle); font-size: 0.85rem;">
                            <div>
                                <strong><?php echo esc_html( $it['title'] ); ?></strong>
                                <span style="color: var(--text-muted); margin-left: 0.5rem;">x<?php echo esc_html( $it['quantity'] ); ?></span>
                            </div>
                            <span style="font-weight: 700;">$<?php echo esc_html( number_format( $it['price'] * $it['quantity'], 2 ) ); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="<?php echo esc_url( home_url( '/account' ) ); ?>" class="btn btn-primary btn-lg">
                    <?php esc_html_e( 'View My Account Dashboard →', 'digital-marketplace-commerce' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-lg">
                    <?php esc_html_e( 'Back to Catalog', 'digital-marketplace-commerce' ); ?>
                </a>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}

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
                <div class="cart-items-card dmc-cart-empty">
                    <div class="dmc-empty-badge">🛍️</div>
                    <h2 class="dmc-empty-title">
                        <?php esc_html_e( 'Your shopping cart is empty', 'digital-marketplace-commerce' ); ?>
                    </h2>
                    <p class="dmc-empty-desc">
                        <?php esc_html_e( 'Explore our digital marketplace for UI kits, developer stacks, design systems, and software templates.', 'digital-marketplace-commerce' ); ?>
                    </p>
                    <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-primary btn-lg">
                        <?php esc_html_e( 'Explore Products Catalog →', 'digital-marketplace-commerce' ); ?>
                    </a>
                </div>
            <?php else : ?>
                <div class="cart-page-layout">
                    <!-- Left: Real Dynamic Cart Items -->
                    <div class="cart-items-card">
                        <div class="cart-table-header">
                            <span class="cart-th-desc"><?php esc_html_e( 'Product Asset', 'digital-marketplace-commerce' ); ?></span>
                            <span class="cart-th-price"><?php esc_html_e( 'Quantity & Subtotal', 'digital-marketplace-commerce' ); ?></span>
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
                                        <div class="cart-item-meta">
                                            <h3 class="cart-item-title">
                                                <a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
                                            </h3>
                                            <div class="cart-item-badges">
                                                <span class="cart-badge-format"><?php echo esc_html( $item['format'] ); ?></span>
                                                <span class="cart-unit-price">$<?php echo esc_html( number_format( $price, 2 ) ); ?> <?php esc_html_e( 'each', 'digital-marketplace-commerce' ); ?></span>
                                            </div>
                                            <button type="button" class="dmc-cart-remove-btn" data-product-id="<?php echo esc_attr( $pid ); ?>">
                                                ✕ <?php esc_html_e( 'Remove item', 'digital-marketplace-commerce' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="cart-item-controls">
                                        <div class="qty-stepper dmc-qty-stepper">
                                            <button type="button" class="qty-btn qty-btn-minus dmc-qty-minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'digital-marketplace-commerce' ); ?>">-</button>
                                            <span class="qty-val dmc-qty-val"><?php echo esc_html( $qty ); ?></span>
                                            <button type="button" class="qty-btn qty-btn-plus dmc-qty-plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'digital-marketplace-commerce' ); ?>">+</button>
                                        </div>
                                        <div class="cart-item-subtotal-box">
                                            <span class="cart-item-subtotal dmc-row-subtotal">
                                                $<?php echo esc_html( number_format( $line_subtotal, 2 ) ); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="cart-table-footer">
                            <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-sm">
                                ← <?php esc_html_e( 'Continue Shopping', 'digital-marketplace-commerce' ); ?>
                            </a>
                            <span class="cart-trust-note">
                                ⚡ <?php esc_html_e( 'Direct peer-to-peer crypto fulfillment with zero fees.', 'digital-marketplace-commerce' ); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Right: Dynamic Order Summary -->
                    <div>
                        <div class="summary-card dmc-summary-card">
                            <h2 class="summary-card-title">
                                <?php esc_html_e( 'Order Summary', 'digital-marketplace-commerce' ); ?>
                            </h2>

                            <!-- Coupon Box -->
                            <form id="marketplace-coupon-form" class="dmc-coupon-form">
                                <label for="coupon-input" class="form-label"><?php esc_html_e( 'Promo or Referral Code', 'digital-marketplace-commerce' ); ?></label>
                                <div class="coupon-input-group">
                                    <input id="coupon-input" type="text" class="form-control coupon-input" placeholder="<?php esc_attr_e( 'e.g. WELCOME10', 'digital-marketplace-commerce' ); ?>" />
                                    <button type="submit" class="btn btn-secondary btn-sm btn-apply-coupon">
                                        <?php esc_html_e( 'Apply', 'digital-marketplace-commerce' ); ?>
                                    </button>
                                </div>
                                <p id="coupon-feedback" class="coupon-feedback-msg"></p>
                            </form>

                            <div class="summary-breakdown-list">
                                <div class="summary-row">
                                    <span class="summary-label"><?php esc_html_e( 'Subtotal', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-display-subtotal" class="summary-val">$<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></span>
                                </div>
                                <div id="cart-discount-row" class="summary-row summary-discount-row" style="display: none;">
                                    <span class="summary-label"><?php esc_html_e( 'Promo Discount', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-discount-val" class="summary-val">-$0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label"><?php esc_html_e( 'Estimated Tax (8%)', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-display-tax" class="summary-val">$<?php echo esc_html( number_format( $tax, 2 ) ); ?></span>
                                </div>
                                <div class="summary-row summary-total-row">
                                    <span class="summary-total-label"><?php esc_html_e( 'Total', 'digital-marketplace-commerce' ); ?></span>
                                    <span id="cart-display-total" class="summary-total-val">$<?php echo esc_html( number_format( $total, 2 ) ); ?></span>
                                </div>
                            </div>

                            <div class="summary-cta-wrap">
                                <a href="<?php echo esc_url( home_url( '/checkout' ) ); ?>" class="btn btn-primary btn-block btn-lg btn-checkout-cta">
                                    <?php esc_html_e( 'Proceed to Checkout →', 'digital-marketplace-commerce' ); ?>
                                </a>
                            </div>

                            <div class="summary-guarantee-box">
                                <div class="guarantee-badge">🪙 <strong><?php esc_html_e( 'Crypto Commerce Active', 'digital-marketplace-commerce' ); ?></strong></div>
                                <p><?php esc_html_e( 'Zero processing markups. Direct blockchain wallet settlement with cryptographic validation.', 'digital-marketplace-commerce' ); ?></p>
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
            <div class="checkout-form-card dmc-checkout-empty">
                <div class="dmc-empty-badge">🛒</div>
                <h2 class="dmc-empty-title">
                    <?php esc_html_e( 'No items in checkout', 'digital-marketplace-commerce' ); ?>
                </h2>
                <p class="dmc-empty-desc">
                    <?php esc_html_e( 'Please add one or more digital products or developer templates to your cart before proceeding.', 'digital-marketplace-commerce' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-primary btn-lg">
                    <?php esc_html_e( 'Browse Products Catalog →', 'digital-marketplace-commerce' ); ?>
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
                <div class="checkout-step-header">
                    <span class="step-num">1</span>
                    <div>
                        <h2 class="step-title"><?php esc_html_e( 'Customer & Delivery Information', 'digital-marketplace-commerce' ); ?></h2>
                        <p class="step-subtitle"><?php esc_html_e( 'Your contact info for license generation, order tracking, and file dispatch.', 'digital-marketplace-commerce' ); ?></p>
                    </div>
                </div>

                <form id="dmc-checkout-form" method="post" action="" class="dmc-checkout-form">
                    <input type="hidden" name="dmc_action" value="place_order" />
                    <?php wp_nonce_field( 'dmc_process_checkout', 'dmc_checkout_nonce' ); ?>

                    <!-- Honeypot Anti-Spam Field (hidden from genuine users) -->
                    <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
                        <label for="dmc_hp_phone"><?php esc_html_e( 'Leave this phone field empty', 'digital-marketplace-commerce' ); ?></label>
                        <input type="text" id="dmc_hp_phone" name="dmc_hp_phone" tabindex="-1" autocomplete="off" value="" />
                    </div>

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
                        <span class="field-hint">
                            <?php esc_html_e( 'Order ID, cryptographically signed invoice, and permanent download links will be delivered here.', 'digital-marketplace-commerce' ); ?>
                        </span>
                    </div>

                    <!-- Payment Method: Crypto -->
                    <div class="checkout-step-header checkout-step-payment">
                        <span class="step-num">2</span>
                        <div>
                            <h2 class="step-title"><?php esc_html_e( 'Payment Method: Cryptocurrency', 'digital-marketplace-commerce' ); ?></h2>
                            <p class="step-subtitle"><?php esc_html_e( 'Select the blockchain network you wish to send payment with:', 'digital-marketplace-commerce' ); ?></p>
                        </div>
                    </div>

                    <div class="crypto-selection-grid">
                        <label class="crypto-option-card">
                            <input type="radio" name="dmc_crypto_coin" value="Bitcoin (BTC)" checked class="crypto-radio" />
                            <div class="crypto-card-content">
                                <div class="crypto-coin-header">
                                    <span class="crypto-coin-icon">₿</span>
                                    <div>
                                        <strong class="crypto-coin-name">Bitcoin (BTC)</strong>
                                        <span class="crypto-network-tag">Mainnet</span>
                                    </div>
                                </div>
                                <code class="crypto-address-preview"><?php echo esc_html( substr( $wallet_btc, 0, 10 ) . '...' . substr( $wallet_btc, -6 ) ); ?></code>
                            </div>
                        </label>

                        <label class="crypto-option-card">
                            <input type="radio" name="dmc_crypto_coin" value="Ethereum (ETH)" class="crypto-radio" />
                            <div class="crypto-card-content">
                                <div class="crypto-coin-header">
                                    <span class="crypto-coin-icon">Ξ</span>
                                    <div>
                                        <strong class="crypto-coin-name">Ethereum (ETH)</strong>
                                        <span class="crypto-network-tag">ERC-20</span>
                                    </div>
                                </div>
                                <code class="crypto-address-preview"><?php echo esc_html( substr( $wallet_eth, 0, 8 ) . '...' . substr( $wallet_eth, -6 ) ); ?></code>
                            </div>
                        </label>

                        <label class="crypto-option-card">
                            <input type="radio" name="dmc_crypto_coin" value="Tether (USDT)" class="crypto-radio" />
                            <div class="crypto-card-content">
                                <div class="crypto-coin-header">
                                    <span class="crypto-coin-icon">₮</span>
                                    <div>
                                        <strong class="crypto-coin-name">Tether (USDT)</strong>
                                        <span class="crypto-network-tag">TRC-20 / ERC-20</span>
                                    </div>
                                </div>
                                <code class="crypto-address-preview"><?php echo esc_html( substr( $wallet_usdt, 0, 8 ) . '...' . substr( $wallet_usdt, -6 ) ); ?></code>
                            </div>
                        </label>
                    </div>

                    <div class="checkout-submit-wrap">
                        <button type="submit" class="btn btn-primary btn-block btn-lg btn-place-order">
                            🔒 <?php printf( esc_html__( 'Place Order & Pay with Crypto ($%s)', 'digital-marketplace-commerce' ), esc_html( number_format( $total, 2 ) ) ); ?>
                        </button>
                        <p class="checkout-submit-guarantee">
                            🛡️ <?php esc_html_e( 'Direct peer-to-peer settlement. Order reference and receiving wallet displayed immediately.', 'digital-marketplace-commerce' ); ?>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right: Real Cart Order Summary -->
            <div>
                <div class="summary-card dmc-summary-card">
                    <h2 class="summary-card-title">
                        <?php esc_html_e( 'Order Review', 'digital-marketplace-commerce' ); ?>
                    </h2>

                    <div class="review-items-list">
                        <?php foreach ( $cart as $item ) : ?>
                            <div class="review-item-row">
                                <div>
                                    <span class="review-item-title"><?php echo esc_html( $item['title'] ); ?></span>
                                    <p class="review-item-meta">Qty: <?php echo esc_html( $item['quantity'] ); ?> • <?php echo esc_html( $item['format'] ); ?></p>
                                </div>
                                <span class="review-item-price">$<?php echo esc_html( number_format( $item['price'] * $item['quantity'], 2 ) ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-breakdown-list">
                        <div class="summary-row">
                            <span class="summary-label"><?php esc_html_e( 'Subtotal', 'digital-marketplace-commerce' ); ?></span>
                            <span class="summary-val">$<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label"><?php esc_html_e( 'Estimated Tax (8%)', 'digital-marketplace-commerce' ); ?></span>
                            <span class="summary-val">$<?php echo esc_html( number_format( $tax, 2 ) ); ?></span>
                        </div>
                        <div class="summary-row summary-total-row">
                            <span class="summary-total-label"><?php esc_html_e( 'Total Due', 'digital-marketplace-commerce' ); ?></span>
                            <span class="summary-total-val">$<?php echo esc_html( number_format( $total, 2 ) ); ?></span>
                        </div>
                    </div>

                    <div class="review-perks-list">
                        <div class="review-perk-item">
                            <span class="perk-icon">✓</span>
                            <span><?php esc_html_e( 'Zero payment intermediary processing fees', 'digital-marketplace-commerce' ); ?></span>
                        </div>
                        <div class="review-perk-item">
                            <span class="perk-icon">✓</span>
                            <span><?php esc_html_e( 'Instant cryptographic Order ID generated', 'digital-marketplace-commerce' ); ?></span>
                        </div>
                        <div class="review-perk-item">
                            <span class="perk-icon">✓</span>
                            <span><?php esc_html_e( 'Payment instructions dispatched to email', 'digital-marketplace-commerce' ); ?></span>
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

        // 1. Anti-Spam Honeypot Verification: silently abort if filled by automated bots
        if ( ! empty( $_POST['dmc_hp_phone'] ) ) {
            wp_safe_redirect( home_url( '/checkout' ) );
            exit;
        }

        // 2. Anti-Spam Rate Limiting: max 3 orders per IP address per 5 minutes
        $client_ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
        if ( empty( $client_ip ) ) {
            $client_ip = 'unknown_client';
        }
        $rate_transient_key = 'dmc_rate_' . md5( $client_ip );
        $recent_submissions = (int) get_transient( $rate_transient_key );

        if ( $recent_submissions >= 3 ) {
            wp_die(
                '<h1>' . esc_html__( 'Submission Rate Limit Exceeded', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . esc_html__( 'Too many checkout submissions have been received from your IP address within the last 5 minutes. Please wait before placing another order.', 'digital-marketplace-commerce' ) . '</p>' .
                '<p><a href="' . esc_url( home_url( '/cart' ) ) . '" class="button">' . esc_html__( 'Return to Cart', 'digital-marketplace-commerce' ) . '</a></p>',
                esc_html__( 'Rate Limit Exceeded', 'digital-marketplace-commerce' ),
                array( 'response' => 429 )
            );
        }

        set_transient( $rate_transient_key, $recent_submissions + 1, 5 * MINUTE_IN_SECONDS );

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

        $cart_items_list = array_values( $cart );

        // Save metadata
        update_post_meta( $order_id, '_dmc_customer_name', $customer_name );
        update_post_meta( $order_id, '_dmc_customer_email', $email );
        update_post_meta( $order_id, '_dmc_order_total', $total );
        update_post_meta( $order_id, '_dmc_order_status', DMC_Post_Type::STATUS_AWAITING_PAYMENT );
        update_post_meta( $order_id, '_dmc_payment_method', 'Crypto' );
        update_post_meta( $order_id, '_dmc_crypto_coin', $coin );
        update_post_meta( $order_id, '_dmc_crypto_wallet', $wallet_address );
        update_post_meta( $order_id, '_dmc_order_items', $cart_items_list );
        update_post_meta( $order_id, '_dmc_order_key', $order_key );
        update_post_meta( $order_id, '_dmc_date_created', current_time( 'mysql' ) );

        // Clear shopping cart cookie
        DMC_Cart::clear_cart();

        // 1. Send order confirmation & payment instructions email to customer
        self::send_order_email( $order_id, $customer_name, $email, $total, $coin, $wallet_address );

        // 2. Send separate administrative order notification email to site admin
        self::send_admin_order_notification( $order_id, $customer_name, $email, $total, $coin, $wallet_address, $cart_items_list );

        // Redirect to confirmation view
        $confirmation_url = add_query_arg( array(
            'dmc_order_id' => $order_id,
            'order_key'    => $order_key,
        ), home_url( '/checkout' ) );

        wp_safe_redirect( $confirmation_url );
        exit;
    }

    /**
     * Send separate order notification email to the store administrator.
     *
     * @param int $order_id
     * @param string $name
     * @param string $email
     * @param float $total
     * @param string $coin
     * @param string $wallet
     * @param array $items
     */
    public static function send_admin_order_notification( $order_id, $name, $email, $total, $coin, $wallet, $items = array() ) {
        // Retrieve custom admin notification recipient or fall back to WordPress admin email
        $admin_email = get_option( 'dmc_admin_notification_email' );
        if ( empty( $admin_email ) || ! is_email( $admin_email ) ) {
            $admin_email = get_option( 'admin_email' );
        }

        if ( empty( $admin_email ) || ! is_email( $admin_email ) ) {
            return;
        }

        $admin_edit_url = admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' );

        // Build itemized purchased items summary text
        $items_text = '';
        if ( is_array( $items ) && ! empty( $items ) ) {
            foreach ( $items as $item ) {
                $item_title = ! empty( $item['title'] ) ? $item['title'] : __( 'Digital Asset', 'digital-marketplace-commerce' );
                $item_qty   = ! empty( $item['quantity'] ) ? intval( $item['quantity'] ) : 1;
                $item_price = ! empty( $item['price'] ) ? floatval( $item['price'] ) : 0.00;
                $line_total = number_format( $item_price * $item_qty, 2 );

                $items_text .= sprintf(
                    "• %s (Qty: %d) — $%s ($%s each)\n",
                    $item_title,
                    $item_qty,
                    $line_total,
                    number_format( $item_price, 2 )
                );
            }
        } else {
            $items_text = __( "No individual line items recorded.\n", 'digital-marketplace-commerce' );
        }

        $subject = sprintf(
            /* translators: 1: Order ID, 2: formatted total, 3: crypto coin */
            __( '[New Order #%1$d] $%2$s via %3$s', 'digital-marketplace-commerce' ),
            $order_id,
            number_format( $total, 2 ),
            $coin
        );

        $body = sprintf(
            __( "Hello Admin,\n\nA new cryptocurrency order has been placed on %s and is awaiting payment verification.\n\n" .
                "ORDER DETAILS:\n" .
                "--------------------------------------------------\n" .
                "Order ID: #%d\n" .
                "Customer Name: %s\n" .
                "Customer Email: %s\n" .
                "Order Total: $%s\n" .
                "Selected Crypto Coin: %s\n" .
                "Receiving Wallet Address: %s\n\n" .
                "ITEMS PURCHASED:\n" .
                "--------------------------------------------------\n" .
                "%s\n" .
                "DIRECT WP-ADMIN ORDER LINK:\n" .
                "--------------------------------------------------\n" .
                "%s\n\n" .
                "NEXT STEPS:\n" .
                "1. Verify that the payment of $%s has arrived at your %s wallet address.\n" .
                "2. Visit the edit link above and update the Order Status to \"Completed\".\n" .
                "3. Setting the order to \"Completed\" will automatically generate and activate the customer's secure download tokens.\n\n" .
                "— %s Automated Commerce System", 'digital-marketplace-commerce' ),
            get_bloginfo( 'name' ),
            $order_id,
            $name,
            $email,
            number_format( $total, 2 ),
            $coin,
            $wallet,
            $items_text,
            $admin_edit_url,
            number_format( $total, 2 ),
            $coin,
            get_bloginfo( 'name' )
        );

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
        wp_mail( $admin_email, $subject, $body, $headers );
    }

    /**
     * Send cancellation notice email to customer when order is marked Cancelled by admin.
     *
     * @param int $order_id
     */
    public static function send_order_cancellation_email( $order_id ) {
        $order_id = absint( $order_id );
        if ( ! $order_id ) {
            return;
        }

        $email = get_post_meta( $order_id, '_dmc_customer_email', true );
        if ( empty( $email ) || ! is_email( $email ) ) {
            return;
        }

        $name  = get_post_meta( $order_id, '_dmc_customer_name', true ) ?: __( 'Customer', 'digital-marketplace-commerce' );
        $total = floatval( get_post_meta( $order_id, '_dmc_order_total', true ) );
        $coin  = get_post_meta( $order_id, '_dmc_crypto_coin', true ) ?: 'Cryptocurrency';

        $subject = sprintf(
            /* translators: %d: Order ID */
            __( 'Order #%d Notice: Order Cancelled', 'digital-marketplace-commerce' ),
            $order_id
        );

        $body = sprintf(
            __( "Hi %s,\n\n" .
                "Your Order #%d at %s has been marked as Cancelled by our administration team.\n\n" .
                "ORDER REFERENCE:\n" .
                "--------------------------------------------------\n" .
                "Order ID: #%d\n" .
                "Order Total: $%s\n" .
                "Payment Method: %s\n\n" .
                "DOWNLOAD ACCESS NOTICE:\n" .
                "Any download links previously generated for this order have been revoked and invalidated.\n\n" .
                "If you believe this cancellation was made in error, or if you sent a cryptocurrency payment that was delayed on the blockchain, please reply directly to this email with your transaction hash (TXID) so our team can investigate and restore your order.\n\n" .
                "Best regards,\n" .
                "%s Support Team\n" .
                "%s", 'digital-marketplace-commerce' ),
            $name,
            $order_id,
            get_bloginfo( 'name' ),
            $order_id,
            number_format( $total, 2 ),
            $coin,
            get_bloginfo( 'name' ),
            home_url( '/' )
        );

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
        wp_mail( $email, $subject, $body, $headers );
    }

    /**
     * Send email to customer with payment instructions.
     */
    private static function send_order_email( $order_id, $name, $email, $total, $coin, $wallet ) {
        $template = get_option( 'dmc_email_template' );
        if ( empty( $template ) ) {
            $template = "Hi {customer_name},\n\nThank you for your order #{order_id}!\nOrder Total: \${order_total}\nPayment Method: {crypto_coin}\n\nPAYMENT INSTRUCTIONS:\nPlease send the exact total of \${order_total} to the following {crypto_coin} wallet address:\n{crypto_address}\n\nDOWNLOAD ACCESS NOTICE:\nPlease note that file downloads are not instant. Once your cryptocurrency payment is manually verified on the blockchain, our team will mark your order as Completed, and your secure download links will become available in your Account Dashboard (under 'My Downloads' and 'Order History').\n\nSave your Order Reference: #{order_id}";
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
        <div class="checkout-confirmation-card dmc-order-confirmation">
            
            <div class="confirmation-header">
                <div class="confirmation-icon-badge">
                    🪙
                </div>
                <h1 class="confirmation-title">
                    <?php esc_html_e( 'Order Placed — Crypto Payment Pending', 'digital-marketplace-commerce' ); ?>
                </h1>
                <p class="confirmation-subtitle">
                    <?php printf( esc_html__( 'Order Reference: #%d', 'digital-marketplace-commerce' ), esc_html( $order_id ) ); ?> • 
                    <span class="status-badge status-badge-pending"><?php echo esc_html( $status ); ?></span>
                </p>
            </div>

            <!-- Prominent Warning Notice -->
            <div class="confirmation-alert-box">
                <p class="alert-box-heading">
                    ⚠️ <?php esc_html_e( 'Important Settlement Action:', 'digital-marketplace-commerce' ); ?>
                </p>
                <p class="alert-box-body">
                    <?php 
                    /* translators: %d: Order ID */
                    printf( esc_html__( 'Transfer the exact total shown below to the specified recipient address. Please bookmark this page or record your Order ID: #%d for tracking.', 'digital-marketplace-commerce' ), esc_html( $order_id ) ); 
                    ?>
                </p>
            </div>

            <!-- Payment Details Box -->
            <div class="confirmation-details-box">
                <div class="confirmation-details-header">
                    <div>
                        <span class="details-label"><?php esc_html_e( 'Selected Network / Coin', 'digital-marketplace-commerce' ); ?></span>
                        <h3 class="details-coin-val"><?php echo esc_html( $coin ); ?></h3>
                    </div>
                    <div class="details-total-wrap">
                        <span class="details-label"><?php esc_html_e( 'Exact Total Due', 'digital-marketplace-commerce' ); ?></span>
                        <h2 class="details-total-val">$<?php echo esc_html( number_format( $total, 2 ) ); ?></h2>
                    </div>
                </div>

                <div class="confirmation-wallet-wrap">
                    <label class="details-label">
                        <?php esc_html_e( 'Send Payment to this Wallet Address:', 'digital-marketplace-commerce' ); ?>
                    </label>
                    <div class="wallet-copy-bar">
                        <code id="dmc-wallet-copy-text" class="wallet-copy-code">
                            <?php echo esc_html( $wallet_address ); ?>
                        </code>
                        <button type="button" class="btn btn-secondary btn-sm btn-copy-wallet" onclick="navigator.clipboard.writeText('<?php echo esc_js( $wallet_address ); ?>'); alert('Wallet address copied to clipboard!');">
                            📋 <?php esc_html_e( 'Copy', 'digital-marketplace-commerce' ); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Notification -->
            <div class="confirmation-notice-wrap">
                <p>
                    ✉️ <?php printf( esc_html__( 'A payment confirmation and cryptographic invoice receipt has been dispatched to %s.', 'digital-marketplace-commerce' ), '<strong>' . esc_html( $customer_email ) . '</strong>' ); ?>
                </p>
                <p class="notice-security-note">
                    🛡️ <?php esc_html_e( 'Direct license and file downloads activate automatically as soon as transaction validation completes on the blockchain and the order is marked Completed.', 'digital-marketplace-commerce' ); ?>
                </p>
            </div>

            <!-- Items Purchased Summary -->
            <h3 class="confirmation-items-heading">
                <?php esc_html_e( 'Purchased Digital Items:', 'digital-marketplace-commerce' ); ?>
            </h3>
            <div class="confirmation-items-card">
                <?php if ( is_array( $items ) ) : ?>
                    <?php foreach ( $items as $it ) : 
                        $pid       = ! empty( $it['id'] ) ? absint( $it['id'] ) : 0;
                        $has_file  = $pid && class_exists( 'DMC_Downloads' ) ? DMC_Downloads::has_download_file( $pid ) : false;
                        $file_info = $pid && class_exists( 'DMC_Downloads' ) ? DMC_Downloads::get_product_file_info( $pid ) : null;
                    ?>
                        <div class="confirmation-item-row">
                            <div>
                                <strong class="confirmation-item-title"><?php echo esc_html( $it['title'] ); ?></strong>
                                <span class="confirmation-item-qty">x<?php echo esc_html( $it['quantity'] ); ?></span>
                                
                                <div class="confirmation-item-status-note">
                                    <?php if ( $has_file && $file_info ) : ?>
                                        <span class="file-status-ready">
                                            ✓ <?php esc_html_e( 'Digital package ready for download', 'digital-marketplace-commerce' ); ?>
                                        </span>
                                        <?php if ( $status === DMC_Post_Type::STATUS_COMPLETED && class_exists( 'DMC_Downloads' ) ) : 
                                            $dl_url = DMC_Downloads::get_download_url( $order_id, $pid );
                                            if ( $dl_url ) : ?>
                                                <div class="file-download-btn-wrap">
                                                    <a href="<?php echo esc_url( $dl_url ); ?>" class="btn btn-primary btn-sm">
                                                        ⬇️ <?php esc_html_e( 'Download Package', 'digital-marketplace-commerce' ); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span class="file-status-pending">
                                                (<?php esc_html_e( 'Link unlocks when transaction confirmation completes', 'digital-marketplace-commerce' ); ?>)
                                            </span>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="file-status-none">
                                            ⚠️ <?php esc_html_e( 'No downloadable file currently attached to this product', 'digital-marketplace-commerce' ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="confirmation-item-price">$<?php echo esc_html( number_format( $it['price'] * $it['quantity'], 2 ) ); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="confirmation-action-buttons">
                <a href="<?php echo esc_url( home_url( '/account' ) ); ?>" class="btn btn-primary btn-lg">
                    <?php esc_html_e( 'Go to Account Dashboard →', 'digital-marketplace-commerce' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-lg">
                    <?php esc_html_e( 'Explore More Products', 'digital-marketplace-commerce' ); ?>
                </a>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}

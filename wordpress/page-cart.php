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
            <p class="section-sub"><?php esc_html_e( 'Review your selected digital assets before proceeding to download licensing.', 'digital-marketplace' ); ?></p>
        </div>

        <?php if ( function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty() ) : ?>
            <!-- Native WooCommerce Cart Engine -->
            <div class="woocommerce-cart-wrapper">
                <?php echo do_shortcode( '[woocommerce_cart]' ); ?>
            </div>
        <?php else : ?>
            <!-- 
                WooCommerce Shortcode Placeholder / Standalone Preview
                If WooCommerce plugin is installed & activated, [woocommerce_cart] handles real cart session.
                Below is the structured layout ready for WooCommerce or standalone preview.
            -->
            <div class="cart-page-layout">
                
                <!-- Left: Cart Items List -->
                <div class="cart-items-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 0.5rem;">
                        <span style="font-weight: 800; font-size: 0.9rem;"><?php esc_html_e( 'Product Item', 'digital-marketplace' ); ?></span>
                        <span style="font-weight: 800; font-size: 0.9rem;"><?php esc_html_e( 'Price / Quantity', 'digital-marketplace' ); ?></span>
                    </div>

                    <!-- Sample Cart Item Row 1 -->
                    <div class="cart-item-row" data-price="49.00">
                        <div class="cart-item-info">
                            <div class="cart-item-thumb">
                                <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=200&q=80" alt="Apex SaaS UI Design System" />
                            </div>
                            <div>
                                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main);">
                                    <?php esc_html_e( 'Apex SaaS UI Design System', 'digital-marketplace' ); ?>
                                </h3>
                                <p style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    <?php esc_html_e( 'Category: UI & Design Kits • Standard License', 'digital-marketplace' ); ?>
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <div class="qty-stepper">
                                <button type="button" class="qty-btn qty-btn-minus">-</button>
                                <span class="qty-val">1</span>
                                <button type="button" class="qty-btn qty-btn-plus">+</button>
                            </div>
                            <div style="text-align: right; min-width: 70px;">
                                <span class="cart-item-subtotal" style="font-weight: 800; font-size: 1.05rem;">$49.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sample Cart Item Row 2 -->
                    <div class="cart-item-row" data-price="79.00">
                        <div class="cart-item-info">
                            <div class="cart-item-thumb">
                                <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=200&q=80" alt="NextJS 15 SaaS Starter Boilerplate" />
                            </div>
                            <div>
                                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main);">
                                    <?php esc_html_e( 'NextJS 15 SaaS Starter Boilerplate', 'digital-marketplace' ); ?>
                                </h3>
                                <p style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    <?php esc_html_e( 'Category: Developer Boilerplates • Team License', 'digital-marketplace' ); ?>
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <div class="qty-stepper">
                                <button type="button" class="qty-btn qty-btn-minus">-</button>
                                <span class="qty-val">1</span>
                                <button type="button" class="qty-btn qty-btn-plus">+</button>
                            </div>
                            <div style="text-align: right; min-width: 70px;">
                                <span class="cart-item-subtotal" style="font-weight: 800; font-size: 1.05rem;">$79.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Continue Shopping Link -->
                    <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-sm">
                            ← <?php esc_html_e( 'Continue Shopping', 'digital-marketplace' ); ?>
                        </a>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">
                            <?php esc_html_e( 'Digital products are delivered immediately after checkout.', 'digital-marketplace' ); ?>
                        </span>
                    </div>
                </div>

                <!-- Right: Summary & Promo Card -->
                <div>
                    <div class="summary-card">
                        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--text-main);">
                            <?php esc_html_e( 'Order Summary', 'digital-marketplace' ); ?>
                        </h2>

                        <!-- Promo Code Form -->
                        <form id="marketplace-coupon-form" style="margin-bottom: 1.25rem;">
                            <label for="coupon-input" class="form-label"><?php esc_html_e( 'Discount / Coupon Code:', 'digital-marketplace' ); ?></label>
                            <div style="display: flex; gap: 0.5rem;">
                                <input id="coupon-input" type="text" class="form-control" placeholder="e.g. WELCOME10" style="text-transform: uppercase;" />
                                <button type="submit" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                                    <?php esc_html_e( 'Apply', 'digital-marketplace' ); ?>
                                </button>
                            </div>
                            <p id="coupon-feedback" style="font-size: 0.75rem; margin-top: 0.35rem;"></p>
                        </form>

                        <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.85rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);"><?php esc_html_e( 'Subtotal', 'digital-marketplace' ); ?></span>
                                <span id="cart-display-subtotal" style="font-weight: 700;">$128.00</span>
                            </div>
                            <div id="cart-discount-row" style="display: none; justify-content: space-between; color: #059669;">
                                <span><?php esc_html_e( 'Promo Discount', 'digital-marketplace' ); ?></span>
                                <span id="cart-discount-val">-$0.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);"><?php esc_html_e( 'Estimated Tax (8%)', 'digital-marketplace' ); ?></span>
                                <span id="cart-display-tax" style="font-weight: 700;">$10.24</span>
                            </div>
                            <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; margin-top: 0.5rem; display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 900;">
                                <span><?php esc_html_e( 'Total', 'digital-marketplace' ); ?></span>
                                <span id="cart-display-total">$138.24</span>
                            </div>
                        </div>

                        <?php 
                        $checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout' );
                        ?>
                        <div style="margin-top: 1.5rem;">
                            <a href="<?php echo esc_url( $checkout_url ); ?>" class="btn btn-primary btn-block btn-lg">
                                <?php esc_html_e( 'Proceed to Checkout →', 'digital-marketplace' ); ?>
                            </a>
                        </div>

                        <!-- WooCommerce Plugin Compatibility Notice -->
                        <div style="margin-top: 1.25rem; padding: 0.85rem; background-color: var(--bg-subtle); border-radius: var(--radius-md); font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;">
                            <strong>⚡ <?php esc_html_e( 'WooCommerce Ready:', 'digital-marketplace' ); ?></strong>
                            <?php esc_html_e( 'When the WooCommerce plugin is activated, this template seamlessly executes the [woocommerce_cart] shortcode with session storage.', 'digital-marketplace' ); ?>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>

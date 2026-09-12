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
            <p class="section-sub"><?php esc_html_e( 'Complete your order to receive direct download links and license credentials.', 'digital-marketplace' ); ?></p>
        </div>

        <?php if ( function_exists( 'is_checkout' ) && function_exists( 'WC' ) ) : ?>
            <!-- Native WooCommerce Secure Checkout Engine -->
            <div class="woocommerce-checkout-wrapper">
                <?php echo do_shortcode( '[woocommerce_checkout]' ); ?>
            </div>
        <?php else : ?>
            <!-- 
                WooCommerce Shortcode Placeholder & Standalone Interface
                When WooCommerce is activated, [woocommerce_checkout] takes over payment gateways,
                Stripe/PayPal tokens, customer sessions, and automatic order generation.
            -->
            <div class="checkout-page-layout">

                <!-- Left Column: Checkout Form with Nonces -->
                <div class="checkout-form-card">
                    <h2 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.25rem;">
                        <?php esc_html_e( '1. Customer Information', 'digital-marketplace' ); ?>
                    </h2>

                    <form id="wp-checkout-form" method="post" action="<?php echo esc_url( home_url( '/account' ) ); ?>">
                        <?php wp_nonce_field( 'digital_marketplace_checkout_action', 'marketplace_checkout_nonce' ); ?>

                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label for="checkout-first-name" class="form-label"><?php esc_html_e( 'First Name *', 'digital-marketplace' ); ?></label>
                                <input type="text" id="checkout-first-name" name="first_name" class="form-control" required placeholder="Alex" />
                                <span class="field-error" style="display: none; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">
                                    <?php esc_html_e( 'Please enter your first name.', 'digital-marketplace' ); ?>
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="checkout-last-name" class="form-label"><?php esc_html_e( 'Last Name *', 'digital-marketplace' ); ?></label>
                                <input type="text" id="checkout-last-name" name="last_name" class="form-control" required placeholder="Rivera" />
                                <span class="field-error" style="display: none; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">
                                    <?php esc_html_e( 'Please enter your last name.', 'digital-marketplace' ); ?>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="checkout-email" class="form-label"><?php esc_html_e( 'Digital Delivery Email *', 'digital-marketplace' ); ?></label>
                            <input type="email" id="checkout-email" name="email" class="form-control" required placeholder="alex.rivera@example.com" />
                            <small style="color: var(--text-muted); font-size: 0.75rem;">
                                <?php esc_html_e( 'Download links, invoice, and license certificates will be sent to this address.', 'digital-marketplace' ); ?>
                            </small>
                            <span class="field-error" style="display: none; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">
                                <?php esc_html_e( 'Please enter a valid email.', 'digital-marketplace' ); ?>
                            </span>
                        </div>

                        <h2 style="font-size: 1.2rem; font-weight: 800; margin: 2rem 0 1.25rem; border-top: 1px solid var(--border-subtle); padding-top: 1.5rem;">
                            <?php esc_html_e( '2. Payment Method (Demo Placeholder)', 'digital-marketplace' ); ?>
                        </h2>

                        <!-- Security and WooCommerce Architecture Notice -->
                        <div style="background-color: var(--color-accent-bg); border-left: 4px solid var(--color-accent); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-size: 0.8rem; line-height: 1.5; color: var(--color-accent-dark);">
                            <strong>🔒 <?php esc_html_e( 'Secure Gateway Compliance:', 'digital-marketplace' ); ?></strong>
                            <?php esc_html_e( 'Per security best practices, custom raw credit card processing is not built in PHP. Once WooCommerce and a gateway plugin (WooCommerce Stripe Gateway or PayPal Payments) are installed, this form is dynamically replaced with PCI-compliant payment fields.', 'digital-marketplace' ); ?>
                        </div>

                        <div style="display: flex; gap: 1rem; margin-bottom: 1.25rem;">
                            <label style="flex: 1; padding: 1rem; border: 2px solid var(--color-primary); border-radius: var(--radius-md); display: flex; align-items: center; gap: 0.75rem; cursor: pointer; background: #fff;">
                                <input type="radio" name="payment_method" value="card" checked />
                                <span style="font-weight: 700; font-size: 0.85rem;">💳 Credit / Debit Card</span>
                            </label>
                            <label style="flex: 1; padding: 1rem; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); display: flex; align-items: center; gap: 0.75rem; cursor: pointer; background: #fff;">
                                <input type="radio" name="payment_method" value="paypal" />
                                <span style="font-weight: 700; font-size: 0.85rem;">🅿️ PayPal Express</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e( 'Card Number Placeholder', 'digital-marketplace' ); ?></label>
                            <input type="text" class="form-control" placeholder="•••• •••• •••• 4242" value="4242 •••• •••• 4242" readonly style="background-color: #f5f5f4;" />
                        </div>

                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label"><?php esc_html_e( 'Expiration', 'digital-marketplace' ); ?></label>
                                <input type="text" class="form-control" placeholder="12 / 28" value="12 / 28" readonly style="background-color: #f5f5f4;" />
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?php esc_html_e( 'CVC', 'digital-marketplace' ); ?></label>
                                <input type="text" class="form-control" placeholder="•••" value="123" readonly style="background-color: #f5f5f4;" />
                            </div>
                        </div>

                        <div style="margin-top: 1.75rem;">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                🔒 <?php esc_html_e( 'Place Order & Get Downloads ($138.24)', 'digital-marketplace' ); ?>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Order Summary Review -->
                <div>
                    <div class="summary-card">
                        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--text-main);">
                            <?php esc_html_e( 'Summary of Items', 'digital-marketplace' ); ?>
                        </h2>

                        <div style="display: flex; flex-direction: column; gap: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                <div>
                                    <span style="font-weight: 700;">Apex SaaS UI Design System</span>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">Qty: 1 • Figma + React</p>
                                </div>
                                <span style="font-weight: 700;">$49.00</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                <div>
                                    <span style="font-weight: 700;">NextJS 15 SaaS Starter</span>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">Qty: 1 • Full-Stack</p>
                                </div>
                                <span style="font-weight: 700;">$79.00</span>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);"><?php esc_html_e( 'Subtotal', 'digital-marketplace' ); ?></span>
                                <span style="font-weight: 700;">$128.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);"><?php esc_html_e( 'Tax (8%)', 'digital-marketplace' ); ?></span>
                                <span style="font-weight: 700;">$10.24</span>
                            </div>
                            <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; margin-top: 0.5rem; display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 900;">
                                <span><?php esc_html_e( 'Total Due', 'digital-marketplace' ); ?></span>
                                <span>$138.24</span>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.75rem; color: var(--text-muted);">
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="color: var(--color-success);">✓</span>
                                <span><?php esc_html_e( '256-bit Encrypted Checkout', 'digital-marketplace' ); ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="color: var(--color-success);">✓</span>
                                <span><?php esc_html_e( 'Instant Download Key Emailed', 'digital-marketplace' ); ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="color: var(--color-success);">✓</span>
                                <span><?php esc_html_e( 'WooCommerce [woocommerce_checkout] shortcode enabled', 'digital-marketplace' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>

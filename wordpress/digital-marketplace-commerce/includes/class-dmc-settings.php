<?php
/**
 * DMC Plugin Settings Page
 *
 * Provides administration settings under Settings > Digital Marketplace
 * for managing crypto wallet addresses, order email templates, and stale flags.
 *
 * @package Digital_Marketplace_Commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DMC_Settings {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_settings_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    /**
     * Add settings page under standard Settings menu.
     */
    public static function register_settings_menu() {
        add_options_page(
            __( 'Digital Marketplace Commerce Settings', 'digital-marketplace-commerce' ),
            __( 'Digital Marketplace', 'digital-marketplace-commerce' ),
            'manage_options',
            'digital-marketplace',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    /**
     * Register settings and fields in WordPress options API.
     */
    public static function register_settings() {
        register_setting( 'dmc_settings_group', 'dmc_wallet_btc', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
        ) );

        register_setting( 'dmc_settings_group', 'dmc_wallet_eth', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '0x71C...B29',
        ) );

        register_setting( 'dmc_settings_group', 'dmc_wallet_usdt', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'TXj...99A',
        ) );

        register_setting( 'dmc_settings_group', 'dmc_email_template', array(
            'type'              => 'string',
            'sanitize_callback' => 'wp_kses_post',
            'default'           => "Hi {customer_name},\n\nThank you for your order #{order_id} at our Digital Marketplace!\n\nOrder Total: ${order_total}\nPayment Method: Crypto ({crypto_coin})\n\nPAYMENT INSTRUCTIONS:\nPlease send the exact total of ${order_total} to the following {crypto_coin} wallet address:\n{crypto_address}\n\nIMPORTANT: Once your transaction is confirmed on the blockchain, your order will be marked as Completed and your download links will be active in your account dashboard.\n\nThank you for building with us!",
        ) );

        register_setting( 'dmc_settings_group', 'dmc_stale_order_hours', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 24,
        ) );

        register_setting( 'dmc_settings_group', 'dmc_admin_notification_email', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_email',
            'default'           => '',
        ) );

        register_setting( 'dmc_settings_group', 'dmc_max_download_count', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 0,
        ) );
    }

    /**
     * Render settings page view.
     */
    public static function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Digital Marketplace Commerce Settings', 'digital-marketplace-commerce' ); ?></h1>
            <p style="color: #64748b; font-size: 14px;">
                <?php esc_html_e( 'Configure your cryptocurrency receiving addresses, customer transaction email instructions, and order monitoring parameters.', 'digital-marketplace-commerce' ); ?>
            </p>

            <form method="post" action="options.php" style="max-width: 800px; margin-top: 20px;">
                <?php
                settings_fields( 'dmc_settings_group' );
                do_settings_sections( 'dmc_settings_group' );
                ?>

                <!-- Section 1: Crypto Wallets -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                    <h2 style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                        🪙 <?php esc_html_e( 'Cryptocurrency Wallet Addresses', 'digital-marketplace-commerce' ); ?>
                    </h2>
                    <p style="font-size: 13px; color: #64748b;">
                        <?php esc_html_e( 'These addresses will be displayed to buyers on checkout and order confirmation to receive direct peer-to-peer crypto payments.', 'digital-marketplace-commerce' ); ?>
                    </p>

                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dmc_wallet_btc"><strong><?php esc_html_e( 'Bitcoin (BTC) Address', 'digital-marketplace-commerce' ); ?></strong></label>
                            </th>
                            <td>
                                <input type="text" id="dmc_wallet_btc" name="dmc_wallet_btc" value="<?php echo esc_attr( get_option( 'dmc_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh' ) ); ?>" class="regular-text" style="width: 100%; font-family: monospace;" />
                                <p class="description"><?php esc_html_e( 'Enter your native SegWit or Taproot Bitcoin receiving address.', 'digital-marketplace-commerce' ); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="dmc_wallet_eth"><strong><?php esc_html_e( 'Ethereum (ETH) Address', 'digital-marketplace-commerce' ); ?></strong></label>
                            </th>
                            <td>
                                <input type="text" id="dmc_wallet_eth" name="dmc_wallet_eth" value="<?php echo esc_attr( get_option( 'dmc_wallet_eth', '0x71C...B29' ) ); ?>" class="regular-text" style="width: 100%; font-family: monospace;" />
                                <p class="description"><?php esc_html_e( 'Enter your ERC-20 Ethereum / Layer-2 wallet address.', 'digital-marketplace-commerce' ); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="dmc_wallet_usdt"><strong><?php esc_html_e( 'Tether (USDT) / Alternative', 'digital-marketplace-commerce' ); ?></strong></label>
                            </th>
                            <td>
                                <input type="text" id="dmc_wallet_usdt" name="dmc_wallet_usdt" value="<?php echo esc_attr( get_option( 'dmc_wallet_usdt', 'TXj...99A' ) ); ?>" class="regular-text" style="width: 100%; font-family: monospace;" />
                                <p class="description"><?php esc_html_e( 'Enter your TRC-20 or secondary stablecoin wallet address.', 'digital-marketplace-commerce' ); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 2: Email Template -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                    <h2 style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                        ✉️ <?php esc_html_e( 'Customer Order Confirmation Email', 'digital-marketplace-commerce' ); ?>
                    </h2>
                    <p style="font-size: 13px; color: #64748b;">
                        <?php esc_html_e( 'This email will be dispatched via wp_mail() when a buyer places an order. Available template tags:', 'digital-marketplace-commerce' ); ?><br>
                        <code>{customer_name}</code>, <code>{order_id}</code>, <code>{order_total}</code>, <code>{crypto_coin}</code>, <code>{crypto_address}</code>
                    </p>

                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dmc_email_template"><strong><?php esc_html_e( 'Email Body Template', 'digital-marketplace-commerce' ); ?></strong></label>
                            </th>
                            <td>
                                <textarea id="dmc_email_template" name="dmc_email_template" rows="10" class="large-text code" style="width: 100%;"><?php echo esc_textarea( get_option( 'dmc_email_template' ) ); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 3: Monitoring & Stale Flag Threshold -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                    <h2 style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                        ⏱️ <?php esc_html_e( 'Order Monitoring & Stale Threshold', 'digital-marketplace-commerce' ); ?>
                    </h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dmc_stale_order_hours"><strong><?php esc_html_e( 'Stale Order Flag (Hours)', 'digital-marketplace-commerce' ); ?></strong></label>
                            </th>
                            <td>
                                <input type="number" id="dmc_stale_order_hours" name="dmc_stale_order_hours" value="<?php echo esc_attr( get_option( 'dmc_stale_order_hours', 24 ) ); ?>" min="1" max="168" style="width: 100px;" />
                                <span style="margin-left: 8px; color: #64748b;"><?php esc_html_e( 'hours', 'digital-marketplace-commerce' ); ?></span>
                                <p class="description">
                                    <?php esc_html_e( 'Unpaid orders exceeding this duration will display an informational warning flag in wp-admin Orders list. (No automatic cancellation occurs).', 'digital-marketplace-commerce' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 4: Admin Order Notifications -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                    <h2 style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                        🔔 <?php esc_html_e( 'Admin Order Notifications', 'digital-marketplace-commerce' ); ?>
                    </h2>
                    <p style="font-size: 13px; color: #64748b;">
                        <?php esc_html_e( 'Configure where store alerts are sent when a customer places a new cryptocurrency order.', 'digital-marketplace-commerce' ); ?>
                    </p>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dmc_admin_notification_email"><strong><?php esc_html_e( 'Admin Notification Email', 'digital-marketplace-commerce' ); ?></strong></label>
                            </th>
                            <td>
                                <input type="email" id="dmc_admin_notification_email" name="dmc_admin_notification_email" value="<?php echo esc_attr( get_option( 'dmc_admin_notification_email', '' ) ); ?>" class="regular-text" style="width: 100%;" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
                                <p class="description">
                                    <?php 
                                    /* translators: %s: default admin email */
                                    printf( esc_html__( 'Enter an email address to receive new order alerts. Leave blank to default to your WordPress site admin email (%s).', 'digital-marketplace-commerce' ), '<code>' . esc_html( get_option( 'admin_email' ) ) . '</code>' ); 
                                    ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 5: Digital Download Controls & Safety -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                    <h2 style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                        🔒 <?php esc_html_e( 'Digital Download Safety & Token Limits', 'digital-marketplace-commerce' ); ?>
                    </h2>
                    <p style="font-size: 13px; color: #64748b;">
                        <?php esc_html_e( 'Protect your assets by restricting the maximum number of times a purchased download link can be used.', 'digital-marketplace-commerce' ); ?>
                    </p>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dmc_max_download_count"><strong><?php esc_html_e( 'Max Downloads Per Token', 'digital-marketplace-commerce' ); ?></strong></label>
                            </th>
                            <td>
                                <input type="number" id="dmc_max_download_count" name="dmc_max_download_count" value="<?php echo esc_attr( get_option( 'dmc_max_download_count', 0 ) ); ?>" min="0" max="100" style="width: 100px;" />
                                <span style="margin-left: 8px; color: #64748b;"><?php esc_html_e( 'downloads (0 = Unlimited)', 'digital-marketplace-commerce' ); ?></span>
                                <p class="description">
                                    <?php esc_html_e( 'Enter the maximum number of times a customer can download each purchased file with their unique order token. Set to 0 or leave blank for unlimited customer downloads.', 'digital-marketplace-commerce' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button( __( 'Save Commerce Settings', 'digital-marketplace-commerce' ) ); ?>
            </form>
        </div>
        <?php
    }
}

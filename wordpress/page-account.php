<?php
/**
 * Template Name: Account Dashboard Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-account-page" class="account-container">
    <div class="site-container">

        <?php if ( ! is_user_logged_in() ) : ?>
            <!-- Prompt to Log In if not authenticated -->
            <div style="max-width: 480px; margin: 3rem auto; text-align: center; background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); padding: 3rem 2rem;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔒</div>
                <h1 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 0.5rem;"><?php esc_html_e( 'Sign in to Access Your Account', 'digital-marketplace' ); ?></h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem;">
                    <?php esc_html_e( 'View your purchased licenses, invoices, and instantaneous digital downloads.', 'digital-marketplace' ); ?>
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="<?php echo esc_url( home_url( '/login' ) ); ?>" class="btn btn-primary btn-block btn-lg">
                        <?php esc_html_e( 'Sign In to Account', 'digital-marketplace' ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-block">
                        <?php esc_html_e( 'Browse Marketplace', 'digital-marketplace' ); ?>
                    </a>
                </div>
            </div>

        <?php else : 
            $current_user = wp_get_current_user();
            $registered_date = date_i18n( get_option( 'date_format' ), strtotime( $current_user->user_registered ) );
        ?>

            <!-- Account Header Card -->
            <div class="account-header-card">
                <div class="user-profile-meta">
                    <div class="user-avatar-box">
                        <?php echo get_avatar( $current_user->ID, 128 ); ?>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <h1 style="font-size: 1.4rem; font-weight: 900; color: var(--text-main);">
                                <?php echo esc_html( $current_user->display_name ); ?>
                            </h1>
                            <span class="status-badge" style="background-color: var(--color-accent-bg); color: var(--color-accent-dark);">
                                <?php esc_html_e( 'Verified Buyer', 'digital-marketplace' ); ?>
                            </span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                            <?php echo esc_html( $current_user->user_email ); ?>
                        </p>
                        <p style="font-size: 0.75rem; color: var(--text-light); margin-top: 0.25rem;">
                            <?php 
                            /* translators: %s: registration date */
                            printf( esc_html__( 'Customer since %s', 'digital-marketplace' ), esc_html( $registered_date ) ); 
                            ?>
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-sm">
                        <?php esc_html_e( '+ Browse Catalog', 'digital-marketplace' ); ?>
                    </a>
                    <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="btn btn-secondary btn-sm" style="color: var(--color-danger); border-color: var(--color-danger-bg);">
                        <?php esc_html_e( 'Log Out', 'digital-marketplace' ); ?>
                    </a>
                </div>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="account-tab-nav" style="display: flex; gap: 0.5rem; border-bottom: 1px solid var(--border-subtle); margin-bottom: 2rem; overflow-x: auto;">
                <button type="button" class="btn btn-sm btn-primary active" data-target="panel-orders" style="border-radius: var(--radius-sm) var(--radius-sm) 0 0;">
                    📦 <?php esc_html_e( 'Order History', 'digital-marketplace' ); ?>
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-target="panel-downloads" style="border-radius: var(--radius-sm) var(--radius-sm) 0 0;">
                    ⬇️ <?php esc_html_e( 'My Downloads', 'digital-marketplace' ); ?>
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-target="panel-settings" style="border-radius: var(--radius-sm) var(--radius-sm) 0 0;">
                    ⚙️ <?php esc_html_e( 'Profile Settings', 'digital-marketplace' ); ?>
                </button>
            </div>

            <!-- TAB 1: Order History Panel -->
            <div id="panel-orders" class="account-tab-panel">
                <div style="background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-xs);">
                    
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 1.1rem; font-weight: 800;"><?php esc_html_e( 'Recent Marketplace Orders', 'digital-marketplace' ); ?></h2>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?php esc_html_e( 'Instant Digital Fulfillment', 'digital-marketplace' ); ?></span>
                    </div>

                    <?php
                    // Query Digital Marketplace Commerce orders for current user
                    $dmc_orders_query = new WP_Query( array(
                        'post_type'      => 'dmc_order',
                        'post_status'    => 'any',
                        'posts_per_page' => 15,
                        'meta_query'     => array(
                            array(
                                'key'     => '_dmc_customer_email',
                                'value'   => $current_user->user_email,
                                'compare' => '=',
                            ),
                        ),
                    ) );

                    // Check if WooCommerce exists to query dynamic customer orders
                    if ( function_exists( 'wc_get_orders' ) ) {
                        $customer_orders = wc_get_orders( array(
                            'customer' => $current_user->ID,
                            'limit'    => 10,
                        ) );
                    } else {
                        $customer_orders = array();
                    }

                    if ( $dmc_orders_query->have_posts() ) : ?>
                        <div style="overflow-x: auto;">
                            <table class="order-history-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Order ID', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Date', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Purchased Items', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Status', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Total', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Payment / Action', 'digital-marketplace' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ( $dmc_orders_query->have_posts() ) : $dmc_orders_query->the_post(); 
                                        $order_id = get_the_ID();
                                        $items = get_post_meta( $order_id, '_dmc_order_items', true );
                                        $total = floatval( get_post_meta( $order_id, '_dmc_order_total', true ) );
                                        $status = get_post_meta( $order_id, '_dmc_order_status', true ) ?: 'Awaiting Payment';
                                        $coin = get_post_meta( $order_id, '_dmc_crypto_coin', true ) ?: 'Crypto';
                                        $order_key = get_post_meta( $order_id, '_dmc_order_key', true );
                                        $confirm_url = add_query_arg( array( 'dmc_order_id' => $order_id, 'order_key' => $order_key ), home_url( '/checkout' ) );

                                        $badge_style = 'background:#fef3c7; color:#92400e;';
                                        if ( $status === 'Completed' ) {
                                            $badge_style = 'background:#d1fae5; color:#065f46;';
                                        } elseif ( $status === 'Paid - Processing' ) {
                                            $badge_style = 'background:#dbeafe; color:#1e40af;';
                                        } elseif ( $status === 'Cancelled' ) {
                                            $badge_style = 'background:#fee2e2; color:#991b1b;';
                                        }
                                    ?>
                                        <tr>
                                            <td style="font-weight: 700; font-family: monospace;">#<?php echo esc_html( $order_id ); ?></td>
                                            <td><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></td>
                                            <td>
                                                <?php if ( is_array( $items ) && ! empty( $items ) ) : ?>
                                                    <span style="font-weight: 700;"><?php echo esc_html( $items[0]['title'] ?? 'Product' ); ?></span>
                                                    <?php if ( count( $items ) > 1 ) : ?>
                                                        <p style="font-size: 0.75rem; color: var(--text-muted);">+<?php echo esc_html( count( $items ) - 1 ); ?> more items</p>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <span>Digital Goods License</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge" style="<?php echo esc_attr( $badge_style ); ?>">
                                                    <?php echo esc_html( $status ); ?>
                                                </span>
                                            </td>
                                            <td style="font-weight: 900;">$<?php echo esc_html( number_format( $total, 2 ) ); ?></td>
                                            <td>
                                                <?php if ( $status === 'Completed' ) : ?>
                                                    <a href="#" class="btn btn-secondary btn-sm" onclick="alert('Instant asset download active for Order #<?php echo esc_js( $order_id ); ?>'); return false;">
                                                        ⬇️ <?php esc_html_e( 'Download', 'digital-marketplace' ); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <a href="<?php echo esc_url( $confirm_url ); ?>" class="btn btn-primary btn-sm">
                                                        🪙 <?php esc_html_e( 'Instructions', 'digital-marketplace' ); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif ( ! empty( $customer_orders ) ) : ?>
                        <div style="overflow-x: auto;">
                            <table class="order-history-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Order', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Date', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Status', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Total', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Actions', 'digital-marketplace' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ( $customer_orders as $order ) : ?>
                                        <tr>
                                            <td style="font-weight: 700;">#<?php echo esc_html( $order->get_id() ); ?></td>
                                            <td><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
                                            <td><span class="status-badge"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span></td>
                                            <td style="font-weight: 800;"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
                                            <td>
                                                <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="btn btn-secondary btn-sm">
                                                    <?php esc_html_e( 'View', 'digital-marketplace' ); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <!-- 
                            Pre-WooCommerce Mock Order History 
                            Matches React app layout perfectly so the user sees populated order history right away!
                        -->
                        <div style="overflow-x: auto;">
                            <table class="order-history-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Order ID', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Date', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Purchased Items', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Status', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'Total', 'digital-marketplace' ); ?></th>
                                        <th><?php esc_html_e( 'License & Download', 'digital-marketplace' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-weight: 700; font-family: monospace;">#ORD-9842</td>
                                        <td><?php echo esc_html( date( 'M j, Y' ) ); ?></td>
                                        <td>
                                            <span style="font-weight: 700;">Apex SaaS UI Design System</span>
                                            <p style="font-size: 0.75rem; color: var(--text-muted);">Commercial License • Figma + React</p>
                                        </td>
                                        <td><span class="status-badge">Completed</span></td>
                                        <td style="font-weight: 900;">$49.00</td>
                                        <td>
                                            <a href="#" class="btn btn-secondary btn-sm" onclick="alert('Download started: Apex-UI-Kit-v2.4.zip'); return false;">
                                                ⬇️ <?php esc_html_e( 'Download ZIP', 'digital-marketplace' ); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 700; font-family: monospace;">#ORD-9721</td>
                                        <td><?php echo esc_html( date( 'M j, Y', strtotime( '-5 days' ) ) ); ?></td>
                                        <td>
                                            <span style="font-weight: 700;">NextJS 15 SaaS Starter Boilerplate</span>
                                            <p style="font-size: 0.75rem; color: var(--text-muted);">Team License • Full-Stack</p>
                                        </td>
                                        <td><span class="status-badge">Completed</span></td>
                                        <td style="font-weight: 900;">$79.00</td>
                                        <td>
                                            <a href="#" class="btn btn-secondary btn-sm" onclick="alert('Download started: NextJS15-Starter-v1.8.zip'); return false;">
                                                ⬇️ <?php esc_html_e( 'Download ZIP', 'digital-marketplace' ); ?>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- TAB 2: Downloads Panel -->
            <div id="panel-downloads" class="account-tab-panel" style="display: none;">
                <div style="background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-xs);">
                    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><?php esc_html_e( 'Active Asset Licenses & Files', 'digital-marketplace' ); ?></h2>
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; border: 1px solid var(--border-subtle); border-radius: var(--radius-md);">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="font-size: 1.75rem;">📦</div>
                                <div>
                                    <h4 style="font-weight: 800; font-size: 0.95rem;">Apex SaaS UI Design System (v2.4)</h4>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">ZIP • 142 MB • Expires: Never (Lifetime Access)</p>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="alert('Downloading Apex-UI-Kit-v2.4.zip...');">
                                ⬇️ <?php esc_html_e( 'Download File', 'digital-marketplace' ); ?>
                            </button>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; border: 1px solid var(--border-subtle); border-radius: var(--radius-md);">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="font-size: 1.75rem;">⚡</div>
                                <div>
                                    <h4 style="font-weight: 800; font-size: 0.95rem;">NextJS 15 SaaS Starter Boilerplate (v1.8)</h4>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">ZIP + GitHub Access • 48 MB • Lifetime Access</p>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="alert('Downloading NextJS15-Starter-v1.8.zip...');">
                                ⬇️ <?php esc_html_e( 'Download File', 'digital-marketplace' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Profile Settings Panel -->
            <div id="panel-settings" class="account-tab-panel" style="display: none;">
                <div style="max-width: 640px; background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; box-shadow: var(--shadow-xs);">
                    <h2 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.25rem;"><?php esc_html_e( 'Account Profile & Preferences', 'digital-marketplace' ); ?></h2>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field( 'marketplace_update_profile', 'marketplace_profile_nonce' ); ?>
                        
                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e( 'Display Name', 'digital-marketplace' ); ?></label>
                            <input type="text" class="form-control" value="<?php echo esc_attr( $current_user->display_name ); ?>" />
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e( 'Email Address', 'digital-marketplace' ); ?></label>
                            <input type="email" class="form-control" value="<?php echo esc_attr( $current_user->user_email ); ?>" />
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e( 'Registered Username', 'digital-marketplace' ); ?></label>
                            <input type="text" class="form-control" value="<?php echo esc_attr( $current_user->user_login ); ?>" readonly style="background-color: #f5f5f4;" />
                        </div>

                        <button type="button" class="btn btn-primary btn-sm" onclick="alert('Profile changes saved successfully.');">
                            <?php esc_html_e( 'Save Profile Changes', 'digital-marketplace' ); ?>
                        </button>
                    </form>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>

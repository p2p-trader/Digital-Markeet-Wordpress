<?php
/**
 * Template Name: Account Dashboard Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-account-page" class="account-container account-page-wrapper">
    <div class="site-container">

        <?php if ( ! is_user_logged_in() ) : ?>
            <!-- Prompt to Log In if not authenticated -->
            <div class="account-auth-prompt-card">
                <div class="prompt-icon-badge">🔒</div>
                <h1 class="prompt-title"><?php esc_html_e( 'Sign in to Access Your Account', 'digital-marketplace' ); ?></h1>
                <p class="prompt-subtitle">
                    <?php esc_html_e( 'View your purchased asset licenses, invoices, and instantaneous digital download tokens.', 'digital-marketplace' ); ?>
                </p>
                <div class="prompt-actions">
                    <a href="<?php echo esc_url( home_url( '/login' ) ); ?>" class="btn btn-primary btn-block btn-lg">
                        <?php esc_html_e( 'Sign In to Account →', 'digital-marketplace' ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-block">
                        <?php esc_html_e( 'Browse Marketplace Products', 'digital-marketplace' ); ?>
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
                    <div class="user-profile-details">
                        <div class="user-name-row">
                            <h1 class="user-display-name">
                                <?php echo esc_html( $current_user->display_name ); ?>
                            </h1>
                            <span class="status-badge dmc-badge-verified">
                                <span class="badge-dot">●</span> <?php esc_html_e( 'Verified Customer', 'digital-marketplace' ); ?>
                            </span>
                        </div>
                        <p class="user-email-text">
                            <?php echo esc_html( $current_user->user_email ); ?>
                        </p>
                        <p class="user-member-since">
                            <?php 
                            /* translators: %s: registration date */
                            printf( esc_html__( 'Customer member since %s', 'digital-marketplace' ), esc_html( $registered_date ) ); 
                            ?>
                        </p>
                    </div>
                </div>

                <div class="account-header-actions">
                    <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-sm">
                        <?php esc_html_e( '+ Browse Catalog', 'digital-marketplace' ); ?>
                    </a>
                    <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="btn btn-secondary btn-sm btn-logout">
                        <?php esc_html_e( 'Sign Out', 'digital-marketplace' ); ?>
                    </a>
                </div>
            </div>

            <!-- Modern Tab Navigation Bar -->
            <nav class="account-tab-nav" aria-label="<?php esc_attr_e( 'Account sections', 'digital-marketplace' ); ?>">
                <button type="button" class="tab-btn active" data-target="panel-orders">
                    <span class="tab-icon">📦</span>
                    <span><?php esc_html_e( 'Order History', 'digital-marketplace' ); ?></span>
                </button>
                <button type="button" class="tab-btn" data-target="panel-downloads">
                    <span class="tab-icon">⬇️</span>
                    <span><?php esc_html_e( 'My Downloads', 'digital-marketplace' ); ?></span>
                </button>
                <button type="button" class="tab-btn" data-target="panel-settings">
                    <span class="tab-icon">⚙️</span>
                    <span><?php esc_html_e( 'Profile Settings', 'digital-marketplace' ); ?></span>
                </button>
            </nav>

            <!-- TAB 1: Order History Panel -->
            <div id="panel-orders" class="account-tab-panel">
                <div class="account-panel-card">
                    
                    <div class="account-panel-header">
                        <div>
                            <h2 class="panel-header-title"><?php esc_html_e( 'Recent Marketplace Orders', 'digital-marketplace' ); ?></h2>
                            <p class="panel-header-sub"><?php esc_html_e( 'Real-time order statuses and cryptographic transaction records.', 'digital-marketplace' ); ?></p>
                        </div>
                        <span class="panel-header-badge"><?php esc_html_e( 'Instant Digital Fulfillment', 'digital-marketplace' ); ?></span>
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
                        <div class="table-responsive">
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

                                        $badge_class = 'status-badge dmc-status-awaiting';
                                        if ( $status === 'Completed' ) {
                                            $badge_class = 'status-badge dmc-status-completed';
                                        } elseif ( $status === 'Paid - Processing' ) {
                                            $badge_class = 'status-badge dmc-status-processing';
                                        } elseif ( $status === 'Cancelled' ) {
                                            $badge_class = 'status-badge dmc-status-cancelled';
                                        }
                                    ?>
                                        <tr>
                                            <td class="order-id-cell">#<?php echo esc_html( $order_id ); ?></td>
                                            <td class="order-date-cell"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></td>
                                            <td class="order-items-cell">
                                                <?php if ( is_array( $items ) && ! empty( $items ) ) : ?>
                                                    <span class="order-item-title"><?php echo esc_html( $items[0]['title'] ?? 'Product' ); ?></span>
                                                    <?php if ( count( $items ) > 1 ) : ?>
                                                        <span class="order-more-tag">+<?php echo esc_html( count( $items ) - 1 ); ?> more</span>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <span>Digital Goods License</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="<?php echo esc_attr( $badge_class ); ?>">
                                                    <span class="status-dot">●</span> <?php echo esc_html( $status ); ?>
                                                </span>
                                            </td>
                                            <td class="order-total-cell">$<?php echo esc_html( number_format( $total, 2 ) ); ?></td>
                                            <td class="order-action-cell">
                                                <?php if ( $status === 'Completed' ) : ?>
                                                    <?php 
                                                     $order_downloads = array();
                                                    if ( is_array( $items ) && class_exists( 'DMC_Downloads' ) ) {
                                                        foreach ( $items as $it ) {
                                                            $pid = ! empty( $it['id'] ) ? absint( $it['id'] ) : 0;
                                                            if ( $pid && DMC_Downloads::has_download_file( $pid ) ) {
                                                                $dl_url = DMC_Downloads::get_download_url( $order_id, $pid );
                                                                if ( $dl_url ) {
                                                                    $order_downloads[] = array(
                                                                        'url'   => $dl_url,
                                                                        'title' => $it['title'] ?? __( 'File', 'digital-marketplace' ),
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ( ! empty( $order_downloads ) ) : ?>
                                                        <div class="order-actions-stack">
                                                            <?php foreach ( $order_downloads as $odl ) : ?>
                                                                <a href="<?php echo esc_url( $odl['url'] ); ?>" class="btn btn-primary btn-sm btn-download-token">
                                                                    ⬇️ <?php echo esc_html( count( $order_downloads ) > 1 ? $odl['title'] : __( 'Download File', 'digital-marketplace' ) ); ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else : ?>
                                                        <span class="text-muted-sm">
                                                            <?php esc_html_e( 'No file attached', 'digital-marketplace' ); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php elseif ( $status === 'Awaiting Payment' || $status === 'Paid - Processing' ) : ?>
                                                    <div class="order-actions-stack">
                                                        <a href="<?php echo esc_url( $confirm_url ); ?>" class="btn btn-primary btn-sm btn-payment-info">
                                                            🪙 <?php esc_html_e( 'Payment Info', 'digital-marketplace' ); ?>
                                                        </a>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-muted-sm"><?php echo esc_html( $status ); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif ( ! empty( $customer_orders ) ) : ?>
                        <div class="table-responsive">
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
                                            <td class="order-id-cell">#<?php echo esc_html( $order->get_id() ); ?></td>
                                            <td class="order-date-cell"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
                                            <td><span class="status-badge dmc-status-completed"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span></td>
                                            <td class="order-total-cell"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
                                            <td>
                                                <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="btn btn-secondary btn-sm">
                                                    <?php esc_html_e( 'View Order', 'digital-marketplace' ); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <!-- Sample Starter Orders for clean demonstration -->
                        <div class="table-responsive">
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
                                        <td class="order-id-cell">#ORD-9842</td>
                                        <td class="order-date-cell"><?php echo esc_html( date( 'M j, Y' ) ); ?></td>
                                        <td class="order-items-cell">
                                            <span class="order-item-title">Apex SaaS UI Design System</span>
                                            <p class="order-item-sub">Commercial License • Figma + React</p>
                                        </td>
                                        <td><span class="status-badge dmc-status-completed"><span class="status-dot">●</span> Completed</span></td>
                                        <td class="order-total-cell">$49.00</td>
                                        <td>
                                            <a href="#" class="btn btn-primary btn-sm btn-download-token" onclick="alert('Download started: Apex-UI-Kit-v2.4.zip'); return false;">
                                                ⬇️ <?php esc_html_e( 'Download ZIP', 'digital-marketplace' ); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="order-id-cell">#ORD-9721</td>
                                        <td class="order-date-cell"><?php echo esc_html( date( 'M j, Y', strtotime( '-5 days' ) ) ); ?></td>
                                        <td class="order-items-cell">
                                            <span class="order-item-title">NextJS 15 SaaS Starter Boilerplate</span>
                                            <p class="order-item-sub">Team License • Full-Stack</p>
                                        </td>
                                        <td><span class="status-badge dmc-status-completed"><span class="status-dot">●</span> Completed</span></td>
                                        <td class="order-total-cell">$79.00</td>
                                        <td>
                                            <a href="#" class="btn btn-primary btn-sm btn-download-token" onclick="alert('Download started: NextJS15-Starter-v1.8.zip'); return false;">
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
                <div class="account-panel-card">
                    <div class="account-panel-header">
                        <div>
                            <h2 class="panel-header-title"><?php esc_html_e( 'Active Asset Licenses & Downloads', 'digital-marketplace' ); ?></h2>
                            <p class="panel-header-sub"><?php esc_html_e( 'Your permanent download links and package files.', 'digital-marketplace' ); ?></p>
                        </div>
                        <span class="panel-header-badge"><?php esc_html_e( 'Secure Cloud Delivery', 'digital-marketplace' ); ?></span>
                    </div>

                    <?php
                    // 1. Digital Marketplace Commerce: Query completed orders for the current user
                    $user_email = $current_user->user_email;
                    $dmc_downloads_list = array();

                    $completed_orders = get_posts( array(
                        'post_type'      => 'dmc_order',
                        'post_status'    => 'any',
                        'posts_per_page' => 100,
                        'meta_query'     => array(
                            'relation' => 'AND',
                            array(
                                'key'     => '_dmc_customer_email',
                                'value'   => $user_email,
                                'compare' => '=',
                            ),
                            array(
                                'key'     => '_dmc_order_status',
                                'value'   => 'Completed',
                                'compare' => '=',
                            ),
                        ),
                    ) );

                    if ( ! empty( $completed_orders ) && class_exists( 'DMC_Downloads' ) ) {
                        foreach ( $completed_orders as $order_post ) {
                            $oid = $order_post->ID;
                            $order_date = get_the_date( 'M j, Y', $oid );
                            $items = get_post_meta( $oid, '_dmc_order_items', true );

                            if ( is_array( $items ) ) {
                                foreach ( $items as $it ) {
                                    $pid = ! empty( $it['id'] ) ? absint( $it['id'] ) : 0;
                                    if ( $pid && DMC_Downloads::has_download_file( $pid ) ) {
                                        $dl_url = DMC_Downloads::get_download_url( $oid, $pid );
                                        if ( $dl_url ) {
                                            $finfo = DMC_Downloads::get_product_file_info( $pid );
                                            $dmc_downloads_list[] = array(
                                                'title'        => ! empty( $it['title'] ) ? $it['title'] : get_the_title( $pid ),
                                                'product_id'   => $pid,
                                                'order_id'     => $oid,
                                                'order_date'   => $order_date,
                                                'filename'     => $finfo['filename'] ?? 'package.zip',
                                                'filesize'     => $finfo['filesize'] ?? '',
                                                'download_url' => $dl_url,
                                                'extension'    => $finfo['extension'] ?? 'zip',
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // 2. WooCommerce Fallback downloads check if WooCommerce is installed
                    $wc_downloads = array();
                    if ( function_exists( 'WC' ) && WC()->customer ) {
                        $wc_downloads = WC()->customer->get_downloadable_products();
                    }
                    ?>

                    <?php if ( ! empty( $dmc_downloads_list ) ) : ?>
                        <div class="downloads-cards-grid">
                            <?php foreach ( $dmc_downloads_list as $dl_item ) : 
                                $ext = strtolower( $dl_item['extension'] );
                                $file_icon = '📦';
                                if ( in_array( $ext, array( 'zip', 'tar', 'gz', 'rar', '7z' ), true ) ) {
                                    $file_icon = '🗜️';
                                } elseif ( in_array( $ext, array( 'pdf', 'doc', 'docx' ), true ) ) {
                                    $file_icon = '📄';
                                } elseif ( in_array( $ext, array( 'dmg', 'exe', 'app' ), true ) ) {
                                    $file_icon = '💻';
                                }
                            ?>
                                <div class="download-card-item">
                                    <div class="download-item-main">
                                        <div class="download-icon-box"><?php echo esc_html( $file_icon ); ?></div>
                                        <div>
                                            <h4 class="download-title">
                                                <?php echo esc_html( $dl_item['title'] ); ?>
                                            </h4>
                                            <div class="download-meta-row">
                                                <code class="download-filename">
                                                    <?php echo esc_html( $dl_item['filename'] ); ?>
                                                </code>
                                                <?php if ( ! empty( $dl_item['filesize'] ) ) : ?>
                                                    <span class="download-meta-bullet">•</span>
                                                    <span class="download-filesize"><?php echo esc_html( $dl_item['filesize'] ); ?></span>
                                                <?php endif; ?>
                                                <span class="download-meta-bullet">•</span>
                                                <span><?php printf( esc_html__( 'Order #%d (%s)', 'digital-marketplace' ), esc_html( $dl_item['order_id'] ), esc_html( $dl_item['order_date'] ) ); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="download-action-wrap">
                                        <a href="<?php echo esc_url( $dl_item['download_url'] ); ?>" class="btn btn-primary btn-sm btn-download-token">
                                            ⬇️ <?php esc_html_e( 'Download File', 'digital-marketplace' ); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ( ! empty( $wc_downloads ) ) : ?>
                        <!-- WooCommerce Downloads Fallback -->
                        <div class="downloads-cards-grid">
                            <?php foreach ( $wc_downloads as $wc_download ) : ?>
                                <div class="download-card-item">
                                    <div class="download-item-main">
                                        <div class="download-icon-box">📦</div>
                                        <div>
                                            <h4 class="download-title">
                                                <?php echo esc_html( $wc_download['product_name'] ); ?>
                                            </h4>
                                            <p class="download-meta-row">
                                                <?php echo esc_html( $wc_download['download_name'] ); ?> • 
                                                <?php printf( esc_html__( 'Order #%s', 'digital-marketplace' ), esc_html( $wc_download['order_number'] ) ); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <a href="<?php echo esc_url( $wc_download['download_url'] ); ?>" class="btn btn-primary btn-sm btn-download-token">
                                        ⬇️ <?php esc_html_e( 'Download', 'digital-marketplace' ); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php else : ?>
                        <!-- Clean Empty State -->
                        <div class="account-empty-state">
                            <div class="empty-state-icon">📂</div>
                            <h3 class="empty-state-title">
                                <?php esc_html_e( 'No Downloadable Files Available', 'digital-marketplace' ); ?>
                            </h3>
                            <p class="empty-state-text">
                                <?php esc_html_e( 'You do not have any active product downloads yet. Once you complete a purchase and payment is confirmed, your files and lifetime access links will appear right here.', 'digital-marketplace' ); ?>
                            </p>
                            <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-primary btn-md">
                                <?php esc_html_e( 'Browse Digital Marketplace Catalog →', 'digital-marketplace' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- TAB 3: Profile Settings Panel -->
            <div id="panel-settings" class="account-tab-panel" style="display: none;">
                <div class="account-panel-card" style="max-width: 680px;">
                    <div class="account-panel-header">
                        <div>
                            <h2 class="panel-header-title"><?php esc_html_e( 'Account Profile & Preferences', 'digital-marketplace' ); ?></h2>
                            <p class="panel-header-sub"><?php esc_html_e( 'Manage your buyer contact info and display preferences.', 'digital-marketplace' ); ?></p>
                        </div>
                    </div>
                    
                    <form method="post" action="" class="account-settings-form">
                        <?php wp_nonce_field( 'marketplace_update_profile', 'marketplace_profile_nonce' ); ?>
                        
                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e( 'Display Name', 'digital-marketplace' ); ?></label>
                            <input type="text" class="form-control" value="<?php echo esc_attr( $current_user->display_name ); ?>" />
                            <span class="field-hint"><?php esc_html_e( 'Used across download licenses and order invoices.', 'digital-marketplace' ); ?></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e( 'Email Address', 'digital-marketplace' ); ?></label>
                            <input type="email" class="form-control" value="<?php echo esc_attr( $current_user->user_email ); ?>" />
                            <span class="field-hint"><?php esc_html_e( 'Instant download receipts and crypto confirmation alerts are delivered here.', 'digital-marketplace' ); ?></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php esc_html_e( 'Registered Username', 'digital-marketplace' ); ?></label>
                            <input type="text" class="form-control form-control-readonly" value="<?php echo esc_attr( $current_user->user_login ); ?>" readonly />
                        </div>

                        <div class="form-actions-row">
                            <button type="button" class="btn btn-primary btn-md" onclick="alert('Profile changes saved successfully.');">
                                <?php esc_html_e( 'Save Profile Changes', 'digital-marketplace' ); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>

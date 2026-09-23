<?php
/**
 * DMC Orders Custom Post Type & Admin Management
 *
 * Registers 'dmc_order', manages custom columns, status filtering,
 * and order details metabox with manual payment verification.
 *
 * @package Digital_Marketplace_Commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DMC_Post_Type {

    const STATUS_AWAITING_PAYMENT = 'Awaiting Payment';
    const STATUS_PAID_PROCESSING  = 'Paid - Processing';
    const STATUS_COMPLETED        = 'Completed';
    const STATUS_CANCELLED        = 'Cancelled';

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_order_cpt' ) );
        add_filter( 'manage_dmc_order_posts_columns', array( __CLASS__, 'set_custom_columns' ) );
        add_action( 'manage_dmc_order_posts_custom_column', array( __CLASS__, 'render_custom_columns' ), 10, 2 );
        add_action( 'restrict_manage_posts', array( __CLASS__, 'render_status_filter' ) );
        add_action( 'pre_get_posts', array( __CLASS__, 'filter_orders_by_status' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_order_metaboxes' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_product_metaboxes' ) );
        add_action( 'save_post_dmc_order', array( __CLASS__, 'save_order_meta' ) );
        add_action( 'save_post_product', array( __CLASS__, 'save_product_download_meta' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_product_scripts' ) );
        add_action( 'admin_head', array( __CLASS__, 'admin_custom_styles' ) );
    }

    /**
     * Get valid order statuses.
     */
    public static function get_statuses() {
        return array(
            self::STATUS_AWAITING_PAYMENT => __( 'Awaiting Payment', 'digital-marketplace-commerce' ),
            self::STATUS_PAID_PROCESSING  => __( 'Paid - Processing', 'digital-marketplace-commerce' ),
            self::STATUS_COMPLETED        => __( 'Completed', 'digital-marketplace-commerce' ),
            self::STATUS_CANCELLED        => __( 'Cancelled', 'digital-marketplace-commerce' ),
        );
    }

    /**
     * Register 'dmc_order' CPT (private, wp-admin only).
     */
    public static function register_order_cpt() {
        $labels = array(
            'name'                  => _x( 'Orders', 'Post Type General Name', 'digital-marketplace-commerce' ),
            'singular_name'         => _x( 'Order', 'Post Type Singular Name', 'digital-marketplace-commerce' ),
            'menu_name'             => __( 'Orders (DMC)', 'digital-marketplace-commerce' ),
            'all_items'             => __( 'All Orders', 'digital-marketplace-commerce' ),
            'edit_item'             => __( 'View / Edit Order', 'digital-marketplace-commerce' ),
            'view_item'             => __( 'View Order', 'digital-marketplace-commerce' ),
            'search_items'          => __( 'Search Orders', 'digital-marketplace-commerce' ),
            'not_found'             => __( 'No orders found', 'digital-marketplace-commerce' ),
            'not_found_in_trash'    => __( 'No orders found in Trash', 'digital-marketplace-commerce' ),
        );

        $args = array(
            'label'                 => __( 'Order', 'digital-marketplace-commerce' ),
            'description'           => __( 'Digital Marketplace Customer Orders', 'digital-marketplace-commerce' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-cart',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'map_meta_cap'          => true,
        );

        register_post_type( 'dmc_order', $args );
    }

    /**
     * Define custom admin columns.
     */
    public static function set_custom_columns( $columns ) {
        $new_columns = array();
        $new_columns['cb']             = $columns['cb'];
        $new_columns['title']          = __( 'Order ID', 'digital-marketplace-commerce' );
        $new_columns['customer']       = __( 'Customer', 'digital-marketplace-commerce' );
        $new_columns['items']          = __( 'Items Purchased', 'digital-marketplace-commerce' );
        $new_columns['total']          = __( 'Total', 'digital-marketplace-commerce' );
        $new_columns['order_status']   = __( 'Status', 'digital-marketplace-commerce' );
        $new_columns['payment_method'] = __( 'Payment', 'digital-marketplace-commerce' );
        $new_columns['order_date']     = __( 'Date Created', 'digital-marketplace-commerce' );
        $new_columns['stale_flag']     = __( 'Notice', 'digital-marketplace-commerce' );

        return $new_columns;
    }

    /**
     * Render data for custom admin columns.
     */
    public static function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'customer':
                $name  = get_post_meta( $post_id, '_dmc_customer_name', true );
                $email = get_post_meta( $post_id, '_dmc_customer_email', true );
                echo '<strong>' . esc_html( $name ?: 'Guest Buyer' ) . '</strong><br>';
                echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
                break;

            case 'items':
                $items = get_post_meta( $post_id, '_dmc_order_items', true );
                if ( is_array( $items ) && ! empty( $items ) ) {
                    $count = count( $items );
                    echo esc_html( sprintf( _n( '%d item', '%d items', $count, 'digital-marketplace-commerce' ), $count ) );
                    echo '<br><small style="color: #666;">';
                    $titles = array_column( $items, 'title' );
                    echo esc_html( wp_trim_words( implode( ', ', $titles ), 6 ) );
                    echo '</small>';
                } else {
                    echo '—';
                }
                break;

            case 'total':
                $total = floatval( get_post_meta( $post_id, '_dmc_order_total', true ) );
                echo '<span style="font-weight: 800; font-size: 1.05em;">$' . esc_html( number_format( $total, 2 ) ) . '</span>';
                break;

            case 'order_status':
                $status = get_post_meta( $post_id, '_dmc_order_status', true ) ?: self::STATUS_AWAITING_PAYMENT;
                $badge_class = 'dmc-status-' . sanitize_html_class( strtolower( str_replace( array( ' ', '-' ), '_', $status ) ) );
                echo '<span class="dmc-admin-badge ' . esc_attr( $badge_class ) . '">' . esc_html( $status ) . '</span>';
                break;

            case 'payment_method':
                $method = get_post_meta( $post_id, '_dmc_payment_method', true ) ?: 'Crypto';
                $coin   = get_post_meta( $post_id, '_dmc_crypto_coin', true );
                echo esc_html( $method );
                if ( $coin ) {
                    echo ' (' . esc_html( $coin ) . ')';
                }
                break;

            case 'order_date':
                $date = get_post_meta( $post_id, '_dmc_date_created', true );
                if ( ! $date ) {
                    $date = get_the_date( 'Y-m-d H:i:s', $post_id );
                }
                echo esc_html( $date );
                break;

            case 'stale_flag':
                $status = get_post_meta( $post_id, '_dmc_order_status', true ) ?: self::STATUS_AWAITING_PAYMENT;
                if ( $status === self::STATUS_AWAITING_PAYMENT ) {
                    $stale_hours = absint( get_option( 'dmc_stale_order_hours', 24 ) );
                    $post_time = get_post_time( 'U', true, $post_id );
                    $elapsed_hours = ( time() - $post_time ) / 3600;

                    if ( $elapsed_hours >= $stale_hours ) {
                        echo '<span style="display:inline-block; padding: 2px 6px; background:#fff1f2; color:#be123c; border-radius:3px; font-weight:600; font-size:11px;">⚠️ May Be Stale (' . intval( $elapsed_hours ) . 'h)</span>';
                    } else {
                        echo '<span style="color: #10b981; font-size:11px;">● Active (' . intval( $elapsed_hours ) . 'h)</span>';
                    }
                } else {
                    echo '—';
                }
                break;
        }
    }

    /**
     * Add filter dropdown by status on admin orders list.
     */
    public static function render_status_filter() {
        global $typenow;
        if ( $typenow === 'dmc_order' ) {
            $selected = isset( $_GET['dmc_filter_status'] ) ? sanitize_text_field( wp_unslash( $_GET['dmc_filter_status'] ) ) : '';
            ?>
            <select name="dmc_filter_status">
                <option value=""><?php esc_html_e( 'All Order Statuses', 'digital-marketplace-commerce' ); ?></option>
                <?php foreach ( self::get_statuses() as $status_val => $label ) : ?>
                    <option value="<?php echo esc_attr( $status_val ); ?>" <?php selected( $selected, $status_val ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }

    /**
     * Filter orders by status in admin list query.
     */
    public static function filter_orders_by_status( $query ) {
        global $pagenow, $typenow;
        if ( is_admin() && $pagenow === 'edit.php' && $typenow === 'dmc_order' && isset( $_GET['dmc_filter_status'] ) && ! empty( $_GET['dmc_filter_status'] ) ) {
            $status = sanitize_text_field( wp_unslash( $_GET['dmc_filter_status'] ) );
            $query->set( 'meta_key', '_dmc_order_status' );
            $query->set( 'meta_value', $status );
        }
    }

    /**
     * Add Metabox on Single Order screen.
     */
    public static function add_order_metaboxes() {
        add_meta_box(
            'dmc_order_details_metabox',
            __( 'Order Details & Payment Management', 'digital-marketplace-commerce' ),
            array( __CLASS__, 'render_order_details_metabox' ),
            'dmc_order',
            'normal',
            'high'
        );
    }

    /**
     * Render the order details metabox.
     */
    public static function render_order_details_metabox( $post ) {
        wp_nonce_field( 'dmc_save_order_meta_action', 'dmc_order_meta_nonce' );

        $customer_name   = get_post_meta( $post->ID, '_dmc_customer_name', true );
        $customer_email  = get_post_meta( $post->ID, '_dmc_customer_email', true );
        $order_total     = floatval( get_post_meta( $post->ID, '_dmc_order_total', true ) );
        $order_status    = get_post_meta( $post->ID, '_dmc_order_status', true ) ?: self::STATUS_AWAITING_PAYMENT;
        $payment_method  = get_post_meta( $post->ID, '_dmc_payment_method', true ) ?: 'Crypto';
        $crypto_coin     = get_post_meta( $post->ID, '_dmc_crypto_coin', true ) ?: 'Bitcoin';
        $crypto_wallet   = get_post_meta( $post->ID, '_dmc_crypto_wallet', true );
        $items           = get_post_meta( $post->ID, '_dmc_order_items', true );
        $date_created    = get_post_meta( $post->ID, '_dmc_date_created', true ) ?: get_the_date( 'Y-m-d H:i:s', $post->ID );

        $stale_hours = absint( get_option( 'dmc_stale_order_hours', 24 ) );
        $post_time = get_post_time( 'U', true, $post->ID );
        $elapsed_hours = ( time() - $post_time ) / 3600;
        $is_stale = ( $order_status === self::STATUS_AWAITING_PAYMENT && $elapsed_hours >= $stale_hours );
        ?>
        <div style="padding: 10px 0;">
            <?php if ( $is_stale ) : ?>
                <div style="background-color: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                    <strong>⚠️ <?php esc_html_e( 'Notice: This order may be stale.', 'digital-marketplace-commerce' ); ?></strong>
                    <p style="margin: 4px 0 0; font-size: 13px;">
                        <?php printf( esc_html__( 'It has been %d hours since creation and remains "Awaiting Payment". Please verify if the crypto transaction was received on the blockchain.', 'digital-marketplace-commerce' ), intval( $elapsed_hours ) ); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Status Control Header -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                <div>
                    <label for="dmc_order_status" style="font-weight: 700; font-size: 14px; margin-right: 8px;">
                        <?php esc_html_e( 'Change Order Status:', 'digital-marketplace-commerce' ); ?>
                    </label>
                    <select name="dmc_order_status" id="dmc_order_status" style="font-weight: 700; font-size: 14px; padding: 6px 12px; height: auto;">
                        <?php foreach ( self::get_statuses() as $status_val => $label ) : ?>
                            <option value="<?php echo esc_attr( $status_val ); ?>" <?php selected( $order_status, $status_val ); ?>>
                                <?php echo esc_html( $label ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span style="font-size: 12px; color: #64748b; margin-left: 8px;">
                        <?php esc_html_e( '(Select "Completed" after verifying transaction in wallet)', 'digital-marketplace-commerce' ); ?>
                    </span>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Order Created:', 'digital-marketplace-commerce' ); ?></span>
                    <strong><?php echo esc_html( $date_created ); ?></strong>
                </div>
            </div>

            <!-- Customer & Payment Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <h3 style="margin-top: 0; font-size: 14px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                        👤 <?php esc_html_e( 'Customer Details', 'digital-marketplace-commerce' ); ?>
                    </h3>
                    <p style="margin: 6px 0;"><strong><?php esc_html_e( 'Name:', 'digital-marketplace-commerce' ); ?></strong> <?php echo esc_html( $customer_name ?: 'Guest' ); ?></p>
                    <p style="margin: 6px 0;">
                        <strong><?php esc_html_e( 'Email:', 'digital-marketplace-commerce' ); ?></strong> 
                        <a href="mailto:<?php echo esc_attr( $customer_email ); ?>"><?php echo esc_html( $customer_email ); ?></a>
                    </p>
                </div>

                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <h3 style="margin-top: 0; font-size: 14px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                        💳 <?php esc_html_e( 'Payment & Crypto Wallet', 'digital-marketplace-commerce' ); ?>
                    </h3>
                    <p style="margin: 6px 0;"><strong><?php esc_html_e( 'Method:', 'digital-marketplace-commerce' ); ?></strong> <?php echo esc_html( $payment_method ); ?></p>
                    <p style="margin: 6px 0;"><strong><?php esc_html_e( 'Coin Selected:', 'digital-marketplace-commerce' ); ?></strong> <?php echo esc_html( $crypto_coin ); ?></p>
                    <p style="margin: 6px 0;">
                        <strong><?php esc_html_e( 'Wallet Provided to Buyer:', 'digital-marketplace-commerce' ); ?></strong><br>
                        <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; display: inline-block; font-size: 12px; margin-top: 4px; word-break: break-all;">
                            <?php echo esc_html( $crypto_wallet ?: 'Not recorded' ); ?>
                        </code>
                    </p>
                </div>
            </div>

            <!-- Items Purchased Table -->
            <h3 style="font-size: 14px; margin-bottom: 10px;">📦 <?php esc_html_e( 'Purchased Items', 'digital-marketplace-commerce' ); ?></h3>
            <table class="widefat fixed striped" style="margin-bottom: 16px;">
                <thead>
                    <tr>
                        <th style="width: 32%;"><?php esc_html_e( 'Product Name', 'digital-marketplace-commerce' ); ?></th>
                        <th style="width: 28%;"><?php esc_html_e( 'Download File Attachment', 'digital-marketplace-commerce' ); ?></th>
                        <th style="width: 12%;"><?php esc_html_e( 'Unit Price', 'digital-marketplace-commerce' ); ?></th>
                        <th style="width: 10%;"><?php esc_html_e( 'Quantity', 'digital-marketplace-commerce' ); ?></th>
                        <th style="width: 18%; text-align: right;"><?php esc_html_e( 'Line Total', 'digital-marketplace-commerce' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( is_array( $items ) && ! empty( $items ) ) : ?>
                        <?php foreach ( $items as $item ) : 
                            $pid        = ! empty( $item['id'] ) ? absint( $item['id'] ) : 0;
                            $price      = floatval( $item['price'] ?? 0 );
                            $qty        = intval( $item['quantity'] ?? 1 );
                            $line_total = round( $price * $qty, 2 );

                            // File attachment check
                            $file_info  = $pid && class_exists( 'DMC_Downloads' ) ? DMC_Downloads::get_product_file_info( $pid ) : null;
                            $has_file   = $pid && class_exists( 'DMC_Downloads' ) ? DMC_Downloads::has_download_file( $pid ) : false;
                        ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $item['title'] ?? 'Product' ); ?></strong>
                                    <?php if ( $pid ) : ?>
                                        <small style="color: #64748b; margin-left: 6px;">(ID: #<?php echo esc_html( $pid ); ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ( $has_file && $file_info ) : ?>
                                        <div style="display: inline-flex; align-items: center; gap: 5px; color: #059669; font-weight: 700; font-size: 12px;">
                                            <span>✅</span>
                                            <span><?php esc_html_e( 'File Attached', 'digital-marketplace-commerce' ); ?></span>
                                        </div>
                                        <div style="font-size: 11px; color: #475569; font-family: monospace; word-break: break-all; margin-top: 2px;">
                                            <?php echo esc_html( $file_info['filename'] ); ?>
                                            <?php if ( ! empty( $file_info['filesize'] ) ) : ?>
                                                <span style="color: #94a3b8;">(<?php echo esc_html( $file_info['filesize'] ); ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ( $order_status === self::STATUS_COMPLETED && class_exists( 'DMC_Downloads' ) ) : 
                                            $dl_url = DMC_Downloads::get_download_url( $post->ID, $pid );
                                            $stats  = DMC_Downloads::get_token_stats( $post->ID, $pid );
                                            if ( $dl_url ) : ?>
                                                <a href="<?php echo esc_url( $dl_url ); ?>" target="_blank" style="display: inline-block; margin-top: 4px; font-size: 11px; color: #2563eb; text-decoration: underline;">
                                                    ⬇️ <?php esc_html_e( 'Test Customer Download Link', 'digital-marketplace-commerce' ); ?>
                                                </a>
                                            <?php endif; ?>
                                            <div style="margin-top: 6px; padding: 6px 8px; background: #f1f5f9; border-radius: 4px; font-size: 11px; color: #334155;">
                                                <div>
                                                    <strong><?php esc_html_e( 'Downloads:', 'digital-marketplace-commerce' ); ?></strong>
                                                    <?php 
                                                    if ( ! empty( $stats['max'] ) && $stats['max'] > 0 ) {
                                                        printf( esc_html__( '%1$d of %2$d max used', 'digital-marketplace-commerce' ), intval( $stats['count'] ), intval( $stats['max'] ) );
                                                        if ( $stats['count'] >= $stats['max'] ) {
                                                            echo ' <span style="color:#ef4444; font-weight:700;">(' . esc_html__( 'Limit reached', 'digital-marketplace-commerce' ) . ')</span>';
                                                        }
                                                    } else {
                                                        printf( esc_html__( '%d times (unlimited)', 'digital-marketplace-commerce' ), intval( $stats['count'] ) );
                                                    }
                                                    ?>
                                                </div>
                                                <?php if ( ! empty( $stats['last_download'] ) ) : ?>
                                                    <div style="color: #64748b; font-size: 10px; margin-top: 2px;">
                                                        <?php printf( esc_html__( 'Last accessed: %s', 'digital-marketplace-commerce' ), esc_html( $stats['last_download'] ) ); ?>
                                                    </div>
                                                <?php else : ?>
                                                    <div style="color: #94a3b8; font-size: 10px; margin-top: 2px;">
                                                        <?php esc_html_e( 'Not yet downloaded by customer', 'digital-marketplace-commerce' ); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif ( $order_status === self::STATUS_CANCELLED ) : ?>
                                            <div style="margin-top: 4px; display: inline-block; padding: 2px 6px; background: #fee2e2; color: #b91c1c; font-size: 11px; font-weight: 700; border-radius: 4px;">
                                                ❌ <?php esc_html_e( 'Tokens Revoked (Cancelled)', 'digital-marketplace-commerce' ); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif ( $pid ) : ?>
                                        <div style="display: inline-flex; align-items: center; gap: 5px; color: #dc2626; font-weight: 700; font-size: 12px;">
                                            <span>❌</span>
                                            <span><?php esc_html_e( 'No File Attached', 'digital-marketplace-commerce' ); ?></span>
                                        </div>
                                        <div style="margin-top: 3px;">
                                            <a href="<?php echo esc_url( get_edit_post_link( $pid ) ); ?>" target="_blank" style="font-size: 11px; color: #2563eb; text-decoration: underline; font-weight: 600;">
                                                ⚠️ <?php esc_html_e( 'Attach downloadable file to product →', 'digital-marketplace-commerce' ); ?>
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <span style="color: #94a3b8; font-size: 11px;"><?php esc_html_e( 'Product ID unavailable', 'digital-marketplace-commerce' ); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo esc_html( number_format( $price, 2 ) ); ?></td>
                                <td><?php echo esc_html( $qty ); ?></td>
                                <td style="text-align: right; font-weight: 700;">$<?php echo esc_html( number_format( $line_total, 2 ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php esc_html_e( 'No line items recorded.', 'digital-marketplace-commerce' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align: right; font-weight: 800; font-size: 14px;"><?php esc_html_e( 'Order Total:', 'digital-marketplace-commerce' ); ?></th>
                        <th style="text-align: right; font-weight: 900; font-size: 16px; color: #0f172a;">
                            $<?php echo esc_html( number_format( $order_total, 2 ) ); ?>
                        </th>
                    </tr>
                </tfoot>
            </table>

            <p style="font-size: 12px; color: #64748b; margin: 0;">
                <?php esc_html_e( 'Tip: Click "Update" on the right sidebar to save changes to the Order Status. Once set to "Completed", secure download tokens are automatically generated.', 'digital-marketplace-commerce' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Enqueue media uploader scripts on product edit screens.
     */
    public static function enqueue_admin_product_scripts( $hook ) {
        if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
            $screen = get_current_screen();
            if ( $screen && $screen->post_type === 'product' ) {
                wp_enqueue_media();
            }
        }
    }

    /**
     * Register downloadable file metabox on 'product' CPT.
     */
    public static function add_product_metaboxes() {
        add_meta_box(
            'dmc_product_download_metabox',
            __( 'Downloadable Digital File (DMC)', 'digital-marketplace-commerce' ),
            array( __CLASS__, 'render_product_download_metabox' ),
            'product',
            'normal',
            'high'
        );
    }

    /**
     * Render the product downloadable file metabox in wp-admin.
     */
    public static function render_product_download_metabox( $post ) {
        wp_nonce_field( 'dmc_save_product_download_action', 'dmc_product_download_nonce' );

        $file_id   = absint( get_post_meta( $post->ID, '_product_download_file_id', true ) );
        $file_info = class_exists( 'DMC_Downloads' ) ? DMC_Downloads::get_product_file_info( $post->ID ) : null;
        $has_file  = ! empty( $file_info['exists'] );
        ?>
        <div style="padding: 10px 0;">
            <p style="font-size: 13px; color: #475569; margin-top: 0; margin-bottom: 12px; line-height: 1.5;">
                <?php esc_html_e( 'Attach the digital product archive (ZIP, DMG, PDF, or codebase) that buyers receive upon order completion. The physical server file path is never exposed publicly; customers receive protected, single-order streaming links once their order is marked "Completed".', 'digital-marketplace-commerce' ); ?>
            </p>

            <input type="hidden" id="dmc_product_download_file_id" name="_product_download_file_id" value="<?php echo esc_attr( $file_id ?: '' ); ?>" />

            <!-- File Details Card -->
            <div id="dmc_file_details_card" style="padding: 14px 16px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 14px; <?php echo $file_id ? '' : 'display: none;'; ?>">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 28px;">📦</span>
                        <div>
                            <strong id="dmc_file_name_display" style="font-size: 14px; color: #0f172a; word-break: break-all;">
                                <?php echo esc_html( $file_info['filename'] ?? '' ); ?>
                            </strong>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                <?php esc_html_e( 'Media Attachment ID:', 'digital-marketplace-commerce' ); ?> #<span id="dmc_file_id_display"><?php echo esc_html( $file_id ); ?></span>
                                <span id="dmc_file_size_container" style="<?php echo ! empty( $file_info['filesize'] ) ? '' : 'display: none;'; ?>">
                                    • <?php esc_html_e( 'Size:', 'digital-marketplace-commerce' ); ?> <span id="dmc_file_size_display"><?php echo esc_html( $file_info['filesize'] ?? '' ); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #dcfce7; color: #166534; font-size: 11px; font-weight: 700; border-radius: 9999px;">
                        ✓ <?php esc_html_e( 'Attached & Ready for Buyers', 'digital-marketplace-commerce' ); ?>
                    </span>
                </div>
            </div>

            <!-- Empty File Warning -->
            <div id="dmc_file_empty_warning" style="padding: 12px 14px; background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; margin-bottom: 14px; color: #92400e; font-size: 13px; <?php echo $file_id ? 'display: none;' : ''; ?>">
                <strong>⚠️ <?php esc_html_e( 'No downloadable file currently attached.', 'digital-marketplace-commerce' ); ?></strong>
                <p style="margin: 4px 0 0; font-size: 12px;">
                    <?php esc_html_e( 'If a customer purchases this product, they will not have a file to download until you upload and attach a file here.', 'digital-marketplace-commerce' ); ?>
                </p>
            </div>

            <!-- Upload / Select Controls -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <button type="button" id="dmc_upload_file_button" class="button button-primary button-large">
                    <?php echo $file_id ? esc_html__( 'Change / Replace File', 'digital-marketplace-commerce' ) : esc_html__( 'Upload / Select Downloadable File', 'digital-marketplace-commerce' ); ?>
                </button>
                <button type="button" id="dmc_remove_file_button" class="button button-large" style="<?php echo $file_id ? '' : 'display: none;'; ?> color: #dc2626; border-color: #fca5a5;">
                    <?php esc_html_e( 'Remove File', 'digital-marketplace-commerce' ); ?>
                </button>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var fileFrame;

            $('#dmc_upload_file_button').on('click', function(e) {
                e.preventDefault();

                if (fileFrame) {
                    fileFrame.open();
                    return;
                }

                fileFrame = wp.media({
                    title: '<?php echo esc_js( __( 'Select or Upload Product Download File', 'digital-marketplace-commerce' ) ); ?>',
                    button: {
                        text: '<?php echo esc_js( __( 'Attach File to Product', 'digital-marketplace-commerce' ) ); ?>'
                    },
                    multiple: false
                });

                fileFrame.on('select', function() {
                    var attachment = fileFrame.state().get('selection').first().toJSON();
                    $('#dmc_product_download_file_id').val(attachment.id);
                    $('#dmc_file_id_display').text(attachment.id);
                    $('#dmc_file_name_display').text(attachment.filename || attachment.title);
                    
                    if (attachment.filesizeHumanReadable) {
                        $('#dmc_file_size_display').text(attachment.filesizeHumanReadable);
                        $('#dmc_file_size_container').show();
                    } else {
                        $('#dmc_file_size_container').hide();
                    }

                    $('#dmc_file_details_card').show();
                    $('#dmc_file_empty_warning').hide();
                    $('#dmc_remove_file_button').show();
                    $('#dmc_upload_file_button').text('<?php echo esc_js( __( 'Change / Replace File', 'digital-marketplace-commerce' ) ); ?>');
                });

                fileFrame.open();
            });

            $('#dmc_remove_file_button').on('click', function(e) {
                e.preventDefault();
                if (confirm('<?php echo esc_js( __( 'Are you sure you want to detach this downloadable file from the product?', 'digital-marketplace-commerce' ) ); ?>')) {
                    $('#dmc_product_download_file_id').val('');
                    $('#dmc_file_details_card').hide();
                    $('#dmc_file_empty_warning').show();
                    $('#dmc_remove_file_button').hide();
                    $('#dmc_upload_file_button').text('<?php echo esc_js( __( 'Upload / Select Downloadable File', 'digital-marketplace-commerce' ) ); ?>');
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Save product downloadable file ID meta when product post is saved.
     */
    public static function save_product_download_meta( $post_id ) {
        if ( ! isset( $_POST['dmc_product_download_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dmc_product_download_nonce'] ) ), 'dmc_save_product_download_action' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['_product_download_file_id'] ) ) {
            $file_id = absint( $_POST['_product_download_file_id'] );
            if ( $file_id > 0 ) {
                update_post_meta( $post_id, '_product_download_file_id', $file_id );
            } else {
                delete_post_meta( $post_id, '_product_download_file_id' );
            }
        }
    }

    /**
     * Save order metabox fields on post save.
     */
    public static function save_order_meta( $post_id ) {
        if ( ! isset( $_POST['dmc_order_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dmc_order_meta_nonce'] ) ), 'dmc_save_order_meta_action' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['dmc_order_status'] ) ) {
            $new_status = sanitize_text_field( wp_unslash( $_POST['dmc_order_status'] ) );
            $valid_statuses = self::get_statuses();
            if ( array_key_exists( $new_status, $valid_statuses ) ) {
                $old_status = get_post_meta( $post_id, '_dmc_order_status', true ) ?: self::STATUS_AWAITING_PAYMENT;
                update_post_meta( $post_id, '_dmc_order_status', $new_status );

                // If marked "Completed", generate unique download tokens for digital delivery
                if ( $new_status === self::STATUS_COMPLETED && class_exists( 'DMC_Downloads' ) ) {
                    DMC_Downloads::generate_order_tokens( $post_id );
                }

                // If marked "Cancelled", invalidate tokens and email customer
                if ( $new_status === self::STATUS_CANCELLED && $old_status !== self::STATUS_CANCELLED ) {
                    if ( class_exists( 'DMC_Downloads' ) ) {
                        DMC_Downloads::invalidate_order_tokens( $post_id );
                    }
                    if ( class_exists( 'DMC_Checkout' ) ) {
                        DMC_Checkout::send_order_cancellation_email( $post_id );
                    }
                }
            }
        }
    }

    /**
     * Enqueue custom admin badge styling.
     */
    public static function admin_custom_styles() {
        ?>
        <style>
            .dmc-admin-badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 9999px;
                font-size: 11px;
                font-weight: 700;
                line-height: 1.2;
            }
            .dmc-status-awaiting_payment {
                background-color: #fef3c7;
                color: #92400e;
                border: 1px solid #fde68a;
            }
            .dmc-status-paid___processing {
                background-color: #dbeafe;
                color: #1e40af;
                border: 1px solid #bfdbfe;
            }
            .dmc-status-completed {
                background-color: #d1fae5;
                color: #065f46;
                border: 1px solid #a7f3d0;
            }
            .dmc-status-cancelled {
                background-color: #fee2e2;
                color: #991b1b;
                border: 1px solid #fecaca;
            }
        </style>
        <?php
    }
}

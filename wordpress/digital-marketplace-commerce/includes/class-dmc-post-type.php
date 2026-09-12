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
        add_action( 'save_post_dmc_order', array( __CLASS__, 'save_order_meta' ) );
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
                        <th style="width: 50%;"><?php esc_html_e( 'Product Name', 'digital-marketplace-commerce' ); ?></th>
                        <th style="width: 15%;"><?php esc_html_e( 'Unit Price', 'digital-marketplace-commerce' ); ?></th>
                        <th style="width: 15%;"><?php esc_html_e( 'Quantity', 'digital-marketplace-commerce' ); ?></th>
                        <th style="width: 20%; text-align: right;"><?php esc_html_e( 'Line Total', 'digital-marketplace-commerce' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( is_array( $items ) && ! empty( $items ) ) : ?>
                        <?php foreach ( $items as $item ) : 
                            $price = floatval( $item['price'] ?? 0 );
                            $qty   = intval( $item['quantity'] ?? 1 );
                            $line_total = round( $price * $qty, 2 );
                        ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $item['title'] ?? 'Product' ); ?></strong>
                                    <?php if ( ! empty( $item['id'] ) ) : ?>
                                        <small style="color: #64748b; margin-left: 6px;">(ID: #<?php echo esc_html( $item['id'] ); ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo esc_html( number_format( $price, 2 ) ); ?></td>
                                <td><?php echo esc_html( $qty ); ?></td>
                                <td style="text-align: right; font-weight: 700;">$<?php echo esc_html( number_format( $line_total, 2 ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4"><?php esc_html_e( 'No line items recorded.', 'digital-marketplace-commerce' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right; font-weight: 800; font-size: 14px;"><?php esc_html_e( 'Order Total:', 'digital-marketplace-commerce' ); ?></th>
                        <th style="text-align: right; font-weight: 900; font-size: 16px; color: #0f172a;">
                            $<?php echo esc_html( number_format( $order_total, 2 ) ); ?>
                        </th>
                    </tr>
                </tfoot>
            </table>

            <p style="font-size: 12px; color: #64748b; margin: 0;">
                <?php esc_html_e( 'Tip: Click "Update" on the right sidebar to save your changes to the Order Status.', 'digital-marketplace-commerce' ); ?>
            </p>
        </div>
        <?php
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
                update_post_meta( $post_id, '_dmc_order_status', $new_status );
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

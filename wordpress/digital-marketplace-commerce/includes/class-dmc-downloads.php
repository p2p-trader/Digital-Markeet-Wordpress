<?php
/**
 * DMC Secure Digital File Delivery & Download Management
 *
 * Handles generation of unique, unguessable download tokens per order item,
 * verifies customer authorization and order "Completed" status,
 * and securely streams digital file attachments through PHP without exposing
 * physical file paths or URLs.
 *
 * @package Digital_Marketplace_Commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DMC_Downloads {

    /**
     * Initialize download hooks, query variables, and endpoints.
     */
    public static function init() {
        add_filter( 'query_vars', array( __CLASS__, 'register_query_vars' ) );
        add_action( 'init', array( __CLASS__, 'register_rewrite_rules' ) );
        add_action( 'template_redirect', array( __CLASS__, 'handle_download_request' ) );
    }

    /**
     * Register public query variable for download endpoint.
     *
     * @param array $vars Existing query vars.
     * @return array
     */
    public static function register_query_vars( $vars ) {
        $vars[] = 'dmc_download';
        return $vars;
    }

    /**
     * Register clean rewrite rule for /dmc-download/TOKEN.
     */
    public static function register_rewrite_rules() {
        add_rewrite_rule(
            '^dmc-download/([^/]+)/?',
            'index.php?dmc_download=$matches[1]',
            'top'
        );
    }

    /**
     * Check if a product has a valid downloadable file attachment on disk.
     *
     * @param int $product_id Product post ID.
     * @return bool
     */
    public static function has_download_file( $product_id ) {
        $product_id = absint( $product_id );
        if ( ! $product_id ) {
            return false;
        }

        $file_id = absint( get_post_meta( $product_id, '_product_download_file_id', true ) );
        if ( ! $file_id ) {
            return false;
        }

        $file_path = get_attached_file( $file_id );
        return (bool) ( $file_path && file_exists( $file_path ) && is_readable( $file_path ) );
    }

    /**
     * Get attachment details for a product (file name, size, mime).
     *
     * @param int $product_id Product post ID.
     * @return array|null
     */
    public static function get_product_file_info( $product_id ) {
        $product_id = absint( $product_id );
        if ( ! $product_id ) {
            return null;
        }

        $file_id = absint( get_post_meta( $product_id, '_product_download_file_id', true ) );
        if ( ! $file_id ) {
            return null;
        }

        $file_path = get_attached_file( $file_id );
        if ( ! $file_path || ! file_exists( $file_path ) ) {
            return array(
                'file_id'   => $file_id,
                'filename'  => get_the_title( $file_id ) ?: __( 'Attached File', 'digital-marketplace-commerce' ),
                'filesize'  => '',
                'mime_type' => '',
                'exists'    => false,
            );
        }

        $filetype = wp_check_filetype( $file_path );

        return array(
            'file_id'   => $file_id,
            'filename'  => basename( $file_path ),
            'filesize'  => size_format( filesize( $file_path ) ),
            'mime_type' => ! empty( $filetype['type'] ) ? $filetype['type'] : 'application/octet-stream',
            'exists'    => true,
        );
    }

    /**
     * Generate unique, cryptographically strong download tokens for an order's items.
     * Tokens are generated once when the order is marked "Completed" (or on-demand for completed orders).
     *
     * @param int $order_id Order post ID.
     * @return array Array of token information keyed by product ID.
     */
    public static function generate_order_tokens( $order_id ) {
        $order_id = absint( $order_id );
        if ( ! $order_id ) {
            return array();
        }

        $items = get_post_meta( $order_id, '_dmc_order_items', true );
        if ( ! is_array( $items ) || empty( $items ) ) {
            return array();
        }

        $existing_tokens = get_post_meta( $order_id, '_dmc_download_tokens', true );
        if ( ! is_array( $existing_tokens ) ) {
            $existing_tokens = array();
        }

        $updated = false;

        foreach ( $items as $item ) {
            $product_id = ! empty( $item['id'] ) ? absint( $item['id'] ) : 0;
            if ( ! $product_id ) {
                continue;
            }

            // If token does not exist yet for this product item, generate a cryptographically unguessable token
            if ( empty( $existing_tokens[ $product_id ]['token'] ) ) {
                // 48-character unguessable alphanumeric string
                $token = wp_generate_password( 48, false );

                $existing_tokens[ $product_id ] = array(
                    'token'      => $token,
                    'product_id' => $product_id,
                    'title'      => ! empty( $item['title'] ) ? sanitize_text_field( $item['title'] ) : get_the_title( $product_id ),
                    'created_at' => time(),
                );

                // Add individual meta key for fast, prepared SQL / meta query lookup
                add_post_meta( $order_id, '_dmc_download_token', $token );
                update_post_meta( $order_id, '_dmc_token_item_' . $token, $product_id );

                $updated = true;
            }
        }

        if ( $updated ) {
            update_post_meta( $order_id, '_dmc_download_tokens', $existing_tokens );
        }

        return $existing_tokens;
    }

    /**
     * Ensure tokens exist for an order if its status is Completed.
     *
     * @param int $order_id Order post ID.
     * @return array
     */
    public static function ensure_order_tokens( $order_id ) {
        $order_id = absint( $order_id );
        $status   = get_post_meta( $order_id, '_dmc_order_status', true );

        if ( $status === DMC_Post_Type::STATUS_COMPLETED ) {
            return self::generate_order_tokens( $order_id );
        }

        return get_post_meta( $order_id, '_dmc_download_tokens', true ) ?: array();
    }

    /**
     * Build the secure download URL for a specific purchased product within an order.
     *
     * @param int $order_id Order post ID.
     * @param int $product_id Product post ID.
     * @return string Download URL or empty string if not applicable.
     */
    public static function get_download_url( $order_id, $product_id ) {
        $order_id   = absint( $order_id );
        $product_id = absint( $product_id );

        if ( ! $order_id || ! $product_id ) {
            return '';
        }

        $tokens = self::ensure_order_tokens( $order_id );
        if ( empty( $tokens[ $product_id ]['token'] ) ) {
            return '';
        }

        $token     = $tokens[ $product_id ]['token'];
        $order_key = get_post_meta( $order_id, '_dmc_order_key', true );

        $params = array(
            'dmc_download' => $token,
        );

        if ( ! empty( $order_key ) ) {
            $params['order_key'] = $order_key;
        }

        return add_query_arg( $params, home_url( '/' ) );
    }

    /**
     * Handle incoming secure download requests via ?dmc_download=TOKEN or /dmc-download/TOKEN.
     */
    public static function handle_download_request() {
        $token = get_query_var( 'dmc_download' );
        if ( empty( $token ) && isset( $_GET['dmc_download'] ) ) {
            $token = sanitize_text_field( wp_unslash( $_GET['dmc_download'] ) );
        }

        if ( empty( $token ) ) {
            return;
        }

        // 1. Look up the order using WordPress meta query API or prepared SQL statement
        $order_id = self::find_order_by_token( $token );

        if ( ! $order_id ) {
            wp_die(
                '<h1>' . esc_html__( 'Invalid Download Token', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . esc_html__( 'This download token is invalid, unrecognized, or has expired. Please check your order confirmation email or account dashboard.', 'digital-marketplace-commerce' ) . '</p>' .
                '<p><a href="' . esc_url( home_url( '/account' ) ) . '" class="button">' . esc_html__( 'Go to Account Dashboard', 'digital-marketplace-commerce' ) . '</a></p>',
                esc_html__( 'Download Error', 'digital-marketplace-commerce' ),
                array( 'response' => 404 )
            );
        }

        // 2. Verify Order Status: must be "Completed"
        $status = get_post_meta( $order_id, '_dmc_order_status', true ) ?: DMC_Post_Type::STATUS_AWAITING_PAYMENT;

        if ( $status !== DMC_Post_Type::STATUS_COMPLETED ) {
            wp_die(
                '<h1>' . esc_html__( 'Download Not Available Yet', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . sprintf(
                    /* translators: %s: Order status */
                    esc_html__( 'This download link is not active yet. The order is currently "%s". Download links become available only after the order is verified and marked "Completed" by an administrator.', 'digital-marketplace-commerce' ),
                    '<strong>' . esc_html( $status ) . '</strong>'
                ) . '</p>' .
                '<p>' . esc_html__( 'If you recently sent cryptocurrency payment, please allow time for blockchain transaction validation and manual verification.', 'digital-marketplace-commerce' ) . '</p>' .
                '<p><a href="' . esc_url( home_url( '/account' ) ) . '" class="button">' . esc_html__( 'View Order in Dashboard', 'digital-marketplace-commerce' ) . '</a></p>',
                esc_html__( 'Order Pending Completion', 'digital-marketplace-commerce' ),
                array( 'response' => 403 )
            );
        }

        // 3. User Authorization: Must be logged in as customer OR provide matching order confirmation key
        $is_authorized   = false;
        $customer_email  = get_post_meta( $order_id, '_dmc_customer_email', true );
        $stored_key      = get_post_meta( $order_id, '_dmc_order_key', true );
        $provided_key    = isset( $_GET['order_key'] ) ? sanitize_text_field( wp_unslash( $_GET['order_key'] ) ) : ( isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '' );

        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();

            // Store administrators can always access downloads for testing/support
            if ( current_user_can( 'manage_options' ) ) {
                $is_authorized = true;
            } elseif ( ! empty( $customer_email ) && strtolower( $current_user->user_email ) === strtolower( $customer_email ) ) {
                $is_authorized = true;
            } elseif ( (int) get_post_field( 'post_author', $order_id ) === (int) $current_user->ID && (int) $current_user->ID > 0 ) {
                $is_authorized = true;
            }
        }

        // If not authenticated via user session, check order confirmation key
        if ( ! $is_authorized && ! empty( $provided_key ) && ! empty( $stored_key ) ) {
            if ( hash_equals( $stored_key, $provided_key ) ) {
                $is_authorized = true;
            }
        }

        if ( ! $is_authorized ) {
            $login_url = wp_login_url( add_query_arg( array( 'dmc_download' => $token ), home_url( '/' ) ) );
            wp_die(
                '<h1>' . esc_html__( 'Access Denied', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . esc_html__( 'You do not have permission to access this download. Please log in with the email address used when placing this order, or access the download via your original order confirmation link.', 'digital-marketplace-commerce' ) . '</p>' .
                '<p><a href="' . esc_url( $login_url ) . '" class="button">' . esc_html__( 'Log In to Account', 'digital-marketplace-commerce' ) . '</a></p>',
                esc_html__( 'Unauthorized Download', 'digital-marketplace-commerce' ),
                array( 'response' => 403 )
            );
        }

        // 4. Identify the purchased product corresponding to this token
        $product_id = self::get_product_id_from_token( $order_id, $token );

        if ( ! $product_id ) {
            wp_die(
                '<h1>' . esc_html__( 'Product Not Identified', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . esc_html__( 'Could not determine which purchased item this token corresponds to. Please contact support.', 'digital-marketplace-commerce' ) . '</p>',
                esc_html__( 'Product Error', 'digital-marketplace-commerce' ),
                array( 'response' => 404 )
            );
        }

        // 5. Look up the attached file
        $attachment_id = absint( get_post_meta( $product_id, '_product_download_file_id', true ) );

        if ( ! $attachment_id ) {
            wp_die(
                '<h1>' . esc_html__( 'No File Attached', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . esc_html__( 'No downloadable file has been attached to this product by the site administrator yet. Please contact support.', 'digital-marketplace-commerce' ) . '</p>' .
                '<p><a href="' . esc_url( home_url( '/account' ) ) . '" class="button">' . esc_html__( 'Return to Account', 'digital-marketplace-commerce' ) . '</a></p>',
                esc_html__( 'File Not Configured', 'digital-marketplace-commerce' ),
                array( 'response' => 404 )
            );
        }

        $file_path = get_attached_file( $attachment_id );

        if ( ! $file_path || ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
            wp_die(
                '<h1>' . esc_html__( 'File Not Found on Server', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . esc_html__( 'The downloadable file could not be located on the server filesystem. Please contact the site administrator.', 'digital-marketplace-commerce' ) . '</p>' .
                '<p><a href="' . esc_url( home_url( '/account' ) ) . '" class="button">' . esc_html__( 'Return to Account', 'digital-marketplace-commerce' ) . '</a></p>',
                esc_html__( 'File Missing', 'digital-marketplace-commerce' ),
                array( 'response' => 404 )
            );
        }

        // 6. Token Safety: Enforce optional max download count limit
        $max_downloads = absint( get_option( 'dmc_max_download_count', 0 ) );
        $current_count = absint( get_post_meta( $order_id, '_dmc_token_count_' . $token, true ) );

        if ( $max_downloads > 0 && $current_count >= $max_downloads ) {
            wp_die(
                '<h1>' . esc_html__( 'Download Limit Reached', 'digital-marketplace-commerce' ) . '</h1>' .
                '<p>' . sprintf(
                    /* translators: 1: max downloads limit */
                    esc_html__( 'The download limit of %1$d download(s) has been reached for this purchased file. For security and license protection, further downloads are prohibited with this token.', 'digital-marketplace-commerce' ),
                    $max_downloads
                ) . '</p>' .
                '<p>' . esc_html__( 'If you experienced a connection failure or need access reset, please contact support with your order ID.', 'digital-marketplace-commerce' ) . '</p>' .
                '<p><a href="' . esc_url( home_url( '/account' ) ) . '" class="button">' . esc_html__( 'Return to Account Dashboard', 'digital-marketplace-commerce' ) . '</a></p>',
                esc_html__( 'Download Limit Exceeded', 'digital-marketplace-commerce' ),
                array( 'response' => 403 )
            );
        }

        // Increment download counter and record access timestamp
        $new_count = $current_count + 1;
        update_post_meta( $order_id, '_dmc_token_count_' . $token, $new_count );
        update_post_meta( $order_id, '_dmc_token_last_' . $token, current_time( 'mysql' ) );

        $order_tokens = get_post_meta( $order_id, '_dmc_download_tokens', true );
        if ( is_array( $order_tokens ) && isset( $order_tokens[ $product_id ] ) ) {
            $order_tokens[ $product_id ]['download_count'] = $new_count;
            $order_tokens[ $product_id ]['last_download']  = current_time( 'mysql' );
            update_post_meta( $order_id, '_dmc_download_tokens', $order_tokens );
        }

        // 7. Securely stream the file directly through PHP
        // The real file path on disk is NEVER printed, linked, or exposed to the client
        self::stream_file_download( $file_path );
    }

    /**
     * Invalidate and revoke all download tokens associated with an order.
     * Invoked when an order is Cancelled or Refunded.
     *
     * @param int $order_id Order post ID.
     */
    public static function invalidate_order_tokens( $order_id ) {
        $order_id = absint( $order_id );
        if ( ! $order_id ) {
            return;
        }

        // Delete all _dmc_download_token rows for this order
        delete_post_meta( $order_id, '_dmc_download_token' );

        $tokens = get_post_meta( $order_id, '_dmc_download_tokens', true );
        if ( is_array( $tokens ) ) {
            foreach ( $tokens as $pid => $data ) {
                if ( ! empty( $data['token'] ) ) {
                    delete_post_meta( $order_id, '_dmc_token_item_' . $data['token'] );
                }
            }
        }

        // Mark as revoked with timestamp and clear active tokens
        update_post_meta( $order_id, '_dmc_download_tokens_revoked', current_time( 'mysql' ) );
        delete_post_meta( $order_id, '_dmc_download_tokens' );
    }

    /**
     * Get download tracking stats for a specific product in an order.
     *
     * @param int $order_id Order post ID.
     * @param int $product_id Product post ID.
     * @return array
     */
    public static function get_token_stats( $order_id, $product_id ) {
        $order_id   = absint( $order_id );
        $product_id = absint( $product_id );

        $tokens = get_post_meta( $order_id, '_dmc_download_tokens', true );
        $token  = ! empty( $tokens[ $product_id ]['token'] ) ? $tokens[ $product_id ]['token'] : '';

        $count = 0;
        $last  = '';
        if ( $token ) {
            $count = absint( get_post_meta( $order_id, '_dmc_token_count_' . $token, true ) );
            $last  = get_post_meta( $order_id, '_dmc_token_last_' . $token, true );
        }

        $max = absint( get_option( 'dmc_max_download_count', 0 ) );

        return array(
            'token'         => $token,
            'count'         => $count,
            'max'           => $max,
            'last_download' => $last,
        );
    }

    /**
     * Find an order ID by token using secure prepared statements or meta query.
     *
     * @param string $token Token string.
     * @return int Order ID or 0 if not found.
     */
    private static function find_order_by_token( $token ) {
        global $wpdb;

        // Primary: Exact match in postmeta using prepared query
        $order_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
            '_dmc_download_token',
            $token
        ) );

        if ( $order_id ) {
            return (int) $order_id;
        }

        // Secondary fallback: WP_Query with meta_query API
        $query = new WP_Query( array(
            'post_type'      => 'dmc_order',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => '_dmc_download_token',
                    'value'   => $token,
                    'compare' => '=',
                ),
            ),
        ) );

        if ( ! empty( $query->posts ) ) {
            return (int) $query->posts[0];
        }

        // Tertiary fallback: Serialized array in _dmc_download_tokens
        $serialized_orders = $wpdb->get_col( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s LIMIT 1",
            '_dmc_download_tokens',
            '%' . $wpdb->esc_like( $token ) . '%'
        ) );

        if ( ! empty( $serialized_orders ) ) {
            return (int) $serialized_orders[0];
        }

        return 0;
    }

    /**
     * Look up the product ID associated with a token in an order.
     *
     * @param int $order_id Order post ID.
     * @param string $token Token string.
     * @return int Product post ID or 0 if not found.
     */
    private static function get_product_id_from_token( $order_id, $token ) {
        // Direct meta lookup
        $direct_pid = absint( get_post_meta( $order_id, '_dmc_token_item_' . $token, true ) );
        if ( $direct_pid ) {
            return $direct_pid;
        }

        // Array lookup
        $tokens = get_post_meta( $order_id, '_dmc_download_tokens', true );
        if ( is_array( $tokens ) ) {
            foreach ( $tokens as $pid => $data ) {
                if ( ! empty( $data['token'] ) && hash_equals( $data['token'], $token ) ) {
                    return absint( $pid );
                }
            }
        }

        return 0;
    }

    /**
     * Stream a physical file to the client with binary download headers.
     * Exits script execution after transmission.
     *
     * @param string $file_path Absolute physical file path on the server.
     */
    private static function stream_file_download( $file_path ) {
        $filename  = basename( $file_path );
        $filetype  = wp_check_filetype( $file_path );
        $mime_type = ! empty( $filetype['type'] ) ? $filetype['type'] : 'application/octet-stream';
        $filesize  = filesize( $file_path );

        // Clean any active output buffers to prevent corrupting binary output
        while ( ob_get_level() ) {
            ob_end_clean();
        }

        // Prevent browser caching of dynamic protected download
        nocache_headers();

        // Standard binary transfer headers
        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: ' . $mime_type );
        header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $filename ) . '"' );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Pragma: public' );

        if ( $filesize > 0 ) {
            header( 'Content-Length: ' . $filesize );
        }

        // Stream file in 64KB chunks to efficiently handle large downloads without memory spikes
        $handle = fopen( $file_path, 'rb' );
        if ( false !== $handle ) {
            while ( ! feof( $handle ) ) {
                echo fread( $handle, 65536 );
                flush();
            }
            fclose( $handle );
        } else {
            readfile( $file_path );
        }

        exit;
    }
}

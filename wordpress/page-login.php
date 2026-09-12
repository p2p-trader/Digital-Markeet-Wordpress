<?php
/**
 * Template Name: Login / Signup Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-auth-page" class="auth-container auth-page-wrapper">
    <div class="site-container">
        
        <div class="auth-card" id="auth-card-container">
            
            <?php if ( is_user_logged_in() ) : 
                $current_user = wp_get_current_user();
            ?>
                <!-- Already Logged In State -->
                <div class="auth-logged-in-state">
                    <div class="auth-avatar-icon">👋</div>
                    <h2 class="auth-user-greeting">
                        <?php 
                        /* translators: %s: user display name */
                        printf( esc_html__( 'Welcome, %s', 'digital-marketplace' ), esc_html( $current_user->display_name ) ); 
                        ?>
                    </h2>
                    <p class="auth-user-email">
                        <?php echo esc_html( $current_user->user_email ); ?>
                    </p>
                    <p class="auth-session-info">
                        <?php esc_html_e( 'You are signed into your verified customer account.', 'digital-marketplace' ); ?>
                    </p>

                    <div class="auth-action-buttons">
                        <a href="<?php echo esc_url( home_url( '/account' ) ); ?>" class="btn btn-primary btn-block">
                            <?php esc_html_e( 'Go to My Dashboard & Downloads →', 'digital-marketplace' ); ?>
                        </a>
                        <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="btn btn-secondary btn-block">
                            <?php esc_html_e( 'Sign Out', 'digital-marketplace' ); ?>
                        </a>
                    </div>
                </div>

            <?php else : ?>
                <!-- Sign In Form using WordPress Core wp_login_form() -->
                <div class="auth-header">
                    <div class="auth-badge">
                        <span class="auth-badge-icon">✨</span>
                        <span class="auth-badge-text"><?php esc_html_e( 'Creator & Developer Portal', 'digital-marketplace' ); ?></span>
                    </div>
                    <h1 class="auth-title">
                        <?php esc_html_e( 'Sign in to Marketplace', 'digital-marketplace' ); ?>
                    </h1>
                    <p class="auth-subtitle">
                        <?php esc_html_e( 'Access your purchased assets, licenses, and invoice history.', 'digital-marketplace' ); ?>
                    </p>
                </div>

                <?php
                // Display error message if WP login redirected with login error
                if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) : ?>
                    <div class="auth-alert auth-alert-danger" role="alert">
                        <span class="alert-icon">⚠️</span>
                        <span><?php esc_html_e( 'Invalid username or password. Please verify your credentials and try again.', 'digital-marketplace' ); ?></span>
                    </div>
                <?php elseif ( isset( $_GET['login'] ) && 'empty' === $_GET['login'] ) : ?>
                    <div class="auth-alert auth-alert-danger" role="alert">
                        <span class="alert-icon">⚠️</span>
                        <span><?php esc_html_e( 'Please enter both your username/email address and password to continue.', 'digital-marketplace' ); ?></span>
                    </div>
                <?php elseif ( isset( $_GET['loggedout'] ) && 'true' === $_GET['loggedout'] ) : ?>
                    <div class="auth-alert auth-alert-success" role="status">
                        <span class="alert-icon">✓</span>
                        <span><?php esc_html_e( 'You have been securely signed out of your account.', 'digital-marketplace' ); ?></span>
                    </div>
                <?php endif; ?>

                <div class="auth-form-wrapper">
                    <?php
                    // Standard WordPress login form configuration
                    $login_args = array(
                        'echo'           => true,
                        'redirect'       => home_url( '/account' ),
                        'form_id'        => 'marketplace-wp-login-form',
                        'label_username' => __( 'Username or Email Address', 'digital-marketplace' ),
                        'label_password' => __( 'Password', 'digital-marketplace' ),
                        'label_remember' => __( 'Stay signed in on this device', 'digital-marketplace' ),
                        'label_log_in'   => __( 'Sign In to Account →', 'digital-marketplace' ),
                        'id_username'    => 'user_login',
                        'id_password'    => 'user_pass',
                        'id_remember'    => 'rememberme',
                        'id_submit'      => 'wp-submit',
                        'remember'       => true,
                        'value_username' => '',
                        'value_remember' => true,
                    );

                    wp_login_form( $login_args );
                    ?>
                </div>

                <!-- Registration / Password Help -->
                <div class="auth-footer-nav">
                    <?php if ( get_option( 'users_can_register' ) ) : ?>
                        <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="auth-create-account-link">
                            <?php esc_html_e( 'Create an Account', 'digital-marketplace' ); ?>
                        </a>
                    <?php else : ?>
                        <span class="auth-signup-hint">
                            <?php esc_html_e( 'New customer? Instant signup at checkout.', 'digital-marketplace' ); ?>
                        </span>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="auth-forgot-pwd-link">
                        <?php esc_html_e( 'Forgot Password?', 'digital-marketplace' ); ?>
                    </a>
                </div>

                <!-- Trust Security Notice -->
                <div class="auth-trust-notice">
                    <span class="trust-shield-icon">🔒</span>
                    <div>
                        <strong><?php esc_html_e( 'WordPress Core Authentication', 'digital-marketplace' ); ?></strong>
                        <p><?php esc_html_e( 'Encrypted session tokens & nonces protect your account and digital license downloads.', 'digital-marketplace' ); ?></p>
                    </div>
                </div>

            <?php endif; ?>

        </div>

    </div>
</div>

<?php get_footer(); ?>

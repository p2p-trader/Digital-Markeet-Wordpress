<?php
/**
 * Template Name: Login / Signup Page
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-auth-page" class="auth-container">
    <div class="site-container">
        
        <div class="auth-card">
            
            <?php if ( is_user_logged_in() ) : 
                $current_user = wp_get_current_user();
            ?>
                <!-- Already Logged In State -->
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">👋</div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.5rem;">
                        <?php 
                        /* translators: %s: user display name */
                        printf( esc_html__( 'Welcome, %s!', 'digital-marketplace' ), esc_html( $current_user->display_name ) ); 
                        ?>
                    </h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                        <?php esc_html_e( 'You are currently authenticated in the marketplace.', 'digital-marketplace' ); ?>
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
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
                <div style="text-align: center; margin-bottom: 1.75rem;">
                    <div class="brand-icon" style="margin: 0 auto 1rem;">✨</div>
                    <h1 style="font-size: 1.5rem; font-weight: 900; letter-spacing: -0.02em; color: var(--text-main);">
                        <?php esc_html_e( 'Sign in to Marketplace', 'digital-marketplace' ); ?>
                    </h1>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.35rem;">
                        <?php esc_html_e( 'Access your purchased assets, licenses, and invoice history.', 'digital-marketplace' ); ?>
                    </p>
                </div>

                <?php
                // Display error message if WP login redirected with login error
                if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) : ?>
                    <div style="padding: 0.75rem; background-color: var(--color-danger-bg); color: var(--color-danger); border-radius: var(--radius-md); font-size: 0.8rem; font-weight: 600; margin-bottom: 1.25rem;">
                        ⚠️ <?php esc_html_e( 'Invalid username or password. Please check your credentials and try again.', 'digital-marketplace' ); ?>
                    </div>
                <?php elseif ( isset( $_GET['login'] ) && 'empty' === $_GET['login'] ) : ?>
                    <div style="padding: 0.75rem; background-color: var(--color-danger-bg); color: var(--color-danger); border-radius: var(--radius-md); font-size: 0.8rem; font-weight: 600; margin-bottom: 1.25rem;">
                        ⚠️ <?php esc_html_e( 'Please enter both your username/email address and password.', 'digital-marketplace' ); ?>
                    </div>
                <?php elseif ( isset( $_GET['loggedout'] ) && 'true' === $_GET['loggedout'] ) : ?>
                    <div style="padding: 0.75rem; background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: var(--radius-md); font-size: 0.8rem; font-weight: 600; margin-bottom: 1.25rem;">
                        ✓ <?php esc_html_e( 'You have been signed out successfully.', 'digital-marketplace' ); ?>
                    </div>
                <?php endif; ?>

                <?php
                // Standard WordPress login form configuration
                $login_args = array(
                    'echo'           => true,
                    'redirect'       => home_url( '/account' ),
                    'form_id'        => 'marketplace-wp-login-form',
                    'label_username' => __( 'Username or Email Address', 'digital-marketplace' ),
                    'label_password' => __( 'Password', 'digital-marketplace' ),
                    'label_remember' => __( 'Remember Me', 'digital-marketplace' ),
                    'label_log_in'   => __( 'Sign In to Account', 'digital-marketplace' ),
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

                <!-- Registration / Password Help -->
                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                    <?php if ( get_option( 'users_can_register' ) ) : ?>
                        <a href="<?php echo esc_url( wp_registration_url() ); ?>" style="font-weight: 700; color: var(--text-main);">
                            <?php esc_html_e( 'Create an Account', 'digital-marketplace' ); ?>
                        </a>
                    <?php else : ?>
                        <span style="color: var(--text-muted);">
                            <?php esc_html_e( 'New customer? Sign up during checkout.', 'digital-marketplace' ); ?>
                        </span>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="color: var(--text-muted);">
                        <?php esc_html_e( 'Forgot Password?', 'digital-marketplace' ); ?>
                    </a>
                </div>

                <!-- One-click Demo Credentials Notice -->
                <div style="margin-top: 1.5rem; background-color: var(--bg-subtle); border-radius: var(--radius-md); padding: 0.85rem; font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;">
                    <strong>💡 <?php esc_html_e( 'WordPress Core Authentication:', 'digital-marketplace' ); ?></strong>
                    <?php esc_html_e( 'This page strictly relies on WordPress core authentication (wp_login_form, wp_verify_nonce). No insecure custom password hashes or unsafe storage are used.', 'digital-marketplace' ); ?>
                </div>

            <?php endif; ?>

        </div>

    </div>
</div>

<?php get_footer(); ?>

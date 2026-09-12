<?php
/**
 * The template for displaying the footer
 *
 * @package Digital_Marketplace
 */
?>
</main><!-- #primary -->

<footer id="marketplace-footer" class="marketplace-footer">
    <div class="site-container">
        
        <!-- Value Props Row -->
        <div class="footer-features-row">
            <div class="footer-feature">
                <div class="footer-feature-icon">⚡</div>
                <div>
                    <h4><?php esc_html_e( 'Instant Digital Delivery', 'digital-marketplace' ); ?></h4>
                    <p><?php esc_html_e( 'Receive access links, source repositories, and design asset files the moment payment completes.', 'digital-marketplace' ); ?></p>
                </div>
            </div>

            <div class="footer-feature">
                <div class="footer-feature-icon">🛡️</div>
                <div>
                    <h4><?php esc_html_e( 'Verified Creator Quality', 'digital-marketplace' ); ?></h4>
                    <p><?php esc_html_e( 'Every boilerplate, UI kit, and asset pack is tested for clean code, typography scale, and standards.', 'digital-marketplace' ); ?></p>
                </div>
            </div>

            <div class="footer-feature">
                <div class="footer-feature-icon">🔄</div>
                <div>
                    <h4><?php esc_html_e( 'Lifetime Updates', 'digital-marketplace' ); ?></h4>
                    <p><?php esc_html_e( 'Download future framework updates, bugfixes, and expansions directly from your personal dashboard.', 'digital-marketplace' ); ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation Columns -->
        <div class="footer-nav-grid">
            <div class="footer-col">
                <div class="brand-logo" style="color: #fff; margin-bottom: 0.75rem;">
                    <div class="brand-icon">✨</div>
                    <span><?php bloginfo( 'name' ); ?></span>
                </div>
                <p style="font-size: 0.775rem; color: #a8a29e; line-height: 1.6;">
                    <?php esc_html_e( 'Curated digital goods for designers, developers, creators, and modern product builders.', 'digital-marketplace' ); ?>
                </p>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'Explore Marketplace', 'digital-marketplace' ); ?></h5>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/products?category=ui-design-kits' ) ); ?>"><?php esc_html_e( 'UI & Design Kits', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products?category=developer-boilerplates' ) ); ?>"><?php esc_html_e( 'Developer Boilerplates', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products?category=fonts-typography' ) ); ?>"><?php esc_html_e( 'Fonts & Typography', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products?category=3d-assets-icons' ) ); ?>"><?php esc_html_e( '3D Assets & Icons', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products?category=productivity-templates' ) ); ?>"><?php esc_html_e( 'Productivity Templates', 'digital-marketplace' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'User Center', 'digital-marketplace' ); ?></h5>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/account' ) ); ?>"><?php esc_html_e( 'Order History & Downloads', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/cart' ) ); ?>"><?php esc_html_e( 'Active Shopping Cart', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/checkout' ) ); ?>"><?php esc_html_e( 'Checkout Portal', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/login' ) ); ?>"><?php esc_html_e( 'Sign In / Register', 'digital-marketplace' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'Standard License', 'digital-marketplace' ); ?></h5>
                <p style="font-size: 0.775rem; color: #a8a29e; line-height: 1.6; margin-bottom: 0.75rem;">
                    <?php esc_html_e( 'All digital marketplace items include royalty-free commercial project use with single or team attribution waivers.', 'digital-marketplace' ); ?>
                </p>
                <div style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.65rem; background-color: #292524; border-radius: var(--radius-sm); font-size: 0.725rem; color: #34d399;">
                    <span>✓ Commercial Ready</span>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'digital-marketplace' ); ?></p>
            <div style="display: flex; gap: 1.25rem; flex-wrap: wrap;">
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>"><?php esc_html_e( 'Catalog', 'digital-marketplace' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/cart' ) ); ?>"><?php esc_html_e( 'Cart', 'digital-marketplace' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/account' ) ); ?>"><?php esc_html_e( 'Dashboard', 'digital-marketplace' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><?php esc_html_e( 'Terms & Refund Policy', 'digital-marketplace' ); ?></a>
            </div>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

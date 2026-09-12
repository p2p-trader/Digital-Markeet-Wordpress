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
                <div class="footer-feature-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                </div>
                <div class="footer-feature-text">
                    <h4><?php esc_html_e( 'Instant Digital Fulfillment', 'digital-marketplace' ); ?></h4>
                    <p><?php esc_html_e( 'Direct ZIP download packages, tokenized license keys, and Git repository access available immediately upon settlement.', 'digital-marketplace' ); ?></p>
                </div>
            </div>

            <div class="footer-feature">
                <div class="footer-feature-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div class="footer-feature-text">
                    <h4><?php esc_html_e( 'Architectural Code Quality', 'digital-marketplace' ); ?></h4>
                    <p><?php esc_html_e( 'Every design kit, component library, and starter stack is audited for semantic markup, strict TypeScript, and modular tokens.', 'digital-marketplace' ); ?></p>
                </div>
            </div>

            <div class="footer-feature">
                <div class="footer-feature-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                    </svg>
                </div>
                <div class="footer-feature-text">
                    <h4><?php esc_html_e( 'Version Updates Included', 'digital-marketplace' ); ?></h4>
                    <p><?php esc_html_e( 'Receive framework upgrades, bug fixes, and patch releases directly through your verified account dashboard.', 'digital-marketplace' ); ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation Columns -->
        <div class="footer-nav-grid">
            <div class="footer-col footer-col-brand">
                <div class="brand-logo" style="color: #fff; margin-bottom: 0.85rem;">
                    <span class="brand-icon-mark" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                    </span>
                    <span class="brand-title"><?php bloginfo( 'name' ); ?></span>
                </div>
                <p class="footer-brand-bio">
                    <?php esc_html_e( 'Curated engineering tools and design systems for software builders, product studios, and independent developers.', 'digital-marketplace' ); ?>
                </p>
                <div class="footer-crypto-ribbon">
                    <span class="crypto-chip-pill">BTC</span>
                    <span class="crypto-chip-pill">ETH</span>
                    <span class="crypto-chip-pill">USDT</span>
                    <span class="crypto-chip-pill">SOL</span>
                </div>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'Asset Catalog', 'digital-marketplace' ); ?></h5>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/products?category=ui-design-kits' ) ); ?>"><?php esc_html_e( 'UI & Design Systems', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products?category=developer-boilerplates' ) ); ?>"><?php esc_html_e( 'Full-Stack Boilerplates', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products?category=fonts-typography' ) ); ?>"><?php esc_html_e( 'Typography & Typefaces', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products?category=3d-assets-icons' ) ); ?>"><?php esc_html_e( '3D Assets & Icon Suites', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/products' ) ); ?>"><?php esc_html_e( 'All Marketplace Products', 'digital-marketplace' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'Buyer Center', 'digital-marketplace' ); ?></h5>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/account' ) ); ?>"><?php esc_html_e( 'Order History & Licenses', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/cart' ) ); ?>"><?php esc_html_e( 'Active Cart', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/checkout' ) ); ?>"><?php esc_html_e( 'Crypto Checkout', 'digital-marketplace' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/login' ) ); ?>"><?php esc_html_e( 'Customer Sign In', 'digital-marketplace' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'Commercial Rights', 'digital-marketplace' ); ?></h5>
                <p class="footer-license-summary">
                    <?php esc_html_e( 'Every purchased item includes a standard royalty-free commercial license. Deploy into unlimited commercial client builds, SaaS products, and personal projects.', 'digital-marketplace' ); ?>
                </p>
                <div class="footer-license-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span><?php esc_html_e( 'Commercial Ready License Included', 'digital-marketplace' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'digital-marketplace' ); ?></p>
            <div class="footer-bottom-links">
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>"><?php esc_html_e( 'Catalog', 'digital-marketplace' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/cart' ) ); ?>"><?php esc_html_e( 'Cart', 'digital-marketplace' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/account' ) ); ?>"><?php esc_html_e( 'Dashboard', 'digital-marketplace' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><?php esc_html_e( 'Terms & Licensing', 'digital-marketplace' ); ?></a>
            </div>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

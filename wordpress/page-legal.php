<?php
/**
 * Template Name: Legal / Policy Page
 *
 * Designed for Terms of Service, Refund Policy, Privacy Policy, and License Agreements.
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-legal-page" style="padding: 3.5rem 0 6rem; background-color: var(--bg-main);">
    <div class="site-container" style="max-width: 1080px;">

        <!-- Header Hero -->
        <div style="margin-bottom: 3rem; text-align: center; max-width: 760px; margin-left: auto; margin-right: auto;">
            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: var(--color-accent-bg); color: var(--color-accent-dark); font-size: 0.75rem; font-weight: 700; border-radius: 9999px; margin-bottom: 0.75rem;">
                ⚖️ <?php esc_html_e( 'Legal & Store Policies', 'digital-marketplace' ); ?>
            </span>
            <h1 style="font-size: 2.25rem; font-weight: 900; letter-spacing: -0.03em; color: var(--text-main); margin-bottom: 0.75rem; line-height: 1.2;">
                <?php the_title(); ?>
            </h1>
            <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
                <?php 
                /* translators: %s: modified date */
                printf( esc_html__( 'Effective Date & Last Updated: %s. Please read these terms carefully before placing your digital order.', 'digital-marketplace' ), esc_html( get_the_modified_date() ) ); 
                ?>
            </p>
        </div>

        <div class="legal-layout-grid" style="display: grid; grid-template-columns: 260px 1fr; gap: 2.5rem; align-items: start;">
            
            <!-- Policy Navigation Sticky Sidebar -->
            <aside class="legal-sidebar" style="position: sticky; top: 2rem; background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.25rem; box-shadow: var(--shadow-sm);">
                <div style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-light); margin-bottom: 0.75rem;">
                    <?php esc_html_e( 'Policy Sections', 'digital-marketplace' ); ?>
                </div>
                <nav class="legal-nav" style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.85rem;">
                    <a href="#digital-delivery" style="padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); color: var(--text-main); text-decoration: none; font-weight: 600; display: block;">
                        ⚡ <?php esc_html_e( 'Digital Delivery & Access', 'digital-marketplace' ); ?>
                    </a>
                    <a href="#license-terms" style="padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); color: var(--text-main); text-decoration: none; font-weight: 600; display: block;">
                        📜 <?php esc_html_e( 'Commercial Licenses', 'digital-marketplace' ); ?>
                    </a>
                    <a href="#refund-policy" style="padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); color: var(--text-main); text-decoration: none; font-weight: 600; display: block;">
                        🔄 <?php esc_html_e( 'Refund & Cancellation Policy', 'digital-marketplace' ); ?>
                    </a>
                    <a href="#crypto-payments" style="padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); color: var(--text-main); text-decoration: none; font-weight: 600; display: block;">
                        🪙 <?php esc_html_e( 'Cryptocurrency Payments', 'digital-marketplace' ); ?>
                    </a>
                    <a href="#support" style="padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); color: var(--text-main); text-decoration: none; font-weight: 600; display: block;">
                        💬 <?php esc_html_e( 'Support & Inquiries', 'digital-marketplace' ); ?>
                    </a>
                </nav>

                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
                    <div style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 0.75rem;">
                        <?php esc_html_e( 'Have questions about licensing or need a bespoke contract?', 'digital-marketplace' ); ?>
                    </div>
                    <a href="<?php echo esc_url( 'mailto:' . get_option( 'admin_email' ) ); ?>" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center; justify-content: center;">
                        <?php esc_html_e( 'Contact Legal / Support', 'digital-marketplace' ); ?>
                    </a>
                </div>
            </aside>

            <!-- Main Legal Content Body -->
            <main class="legal-content" style="background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 2.5rem; box-shadow: var(--shadow-sm); line-height: 1.75; font-size: 0.95rem; color: var(--text-main);">
                
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php if ( get_the_content() ) : ?>
                        <div class="entry-content typography-content">
                            <?php the_content(); ?>
                        </div>
                    <?php else : ?>
                        <!-- Production-ready Standard Digital Marketplace Terms & Refund Policy -->
                        
                        <section id="digital-delivery" style="margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-subtle);">
                            <h2 style="font-size: 1.35rem; font-weight: 800; margin-top: 0; margin-bottom: 0.75rem; color: var(--text-main);">
                                1. <?php esc_html_e( 'Digital Delivery & Download Fulfillment', 'digital-marketplace' ); ?>
                            </h2>
                            <p>
                                <?php esc_html_e( 'All assets hosted on this marketplace are intangible digital products delivered electronically. Upon completion and verification of your payment transaction, unique secure download tokens are generated instantly and made available through your customer account dashboard and order confirmation receipt.', 'digital-marketplace' ); ?>
                            </p>
                            <div style="background: #f8fafc; border-left: 4px solid var(--color-accent); padding: 1rem 1.25rem; border-radius: 0 var(--radius-md) var(--radius-md) 0; margin: 1rem 0; font-size: 0.9rem;">
                                <strong>💡 <?php esc_html_e( 'Token Security Notice:', 'digital-marketplace' ); ?></strong>
                                <?php esc_html_e( 'Download URLs contain cryptographic, single-order tokens. Tokens are strictly non-transferable and may be subject to fair-use rate limits or expiry periods established by store administration.', 'digital-marketplace' ); ?>
                            </div>
                        </section>

                        <section id="license-terms" style="margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-subtle);">
                            <h2 style="font-size: 1.35rem; font-weight: 800; margin-top: 0; margin-bottom: 0.75rem; color: var(--text-main);">
                                2. <?php esc_html_e( 'Commercial Licensing & Usage Rights', 'digital-marketplace' ); ?>
                            </h2>
                            <p>
                                <?php esc_html_e( 'Unless specified otherwise on the individual product listing, purchases include a worldwide, non-exclusive commercial license permitting you to:', 'digital-marketplace' ); ?>
                            </p>
                            <ul style="padding-left: 1.25rem; margin: 0.75rem 0;">
                                <li><?php esc_html_e( 'Incorporate code, design files, or audio assets into unlimited commercial and personal end-products.', 'digital-marketplace' ); ?></li>
                                <li><?php esc_html_e( 'Modify, adapt, and build derivative solutions for clients or in-house applications.', 'digital-marketplace' ); ?></li>
                                <li><?php esc_html_e( 'Deploy compiled binaries and production web applications without per-user seat fees.', 'digital-marketplace' ); ?></li>
                            </ul>
                            <p>
                                <strong><?php esc_html_e( 'Restrictions:', 'digital-marketplace' ); ?></strong>
                                <?php esc_html_e( 'You may not re-distribute, sub-license, resell, or publicly share the raw source archive files as a standalone competing marketplace asset or template.', 'digital-marketplace' ); ?>
                            </p>
                        </section>

                        <section id="refund-policy" style="margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-subtle);">
                            <h2 style="font-size: 1.35rem; font-weight: 800; margin-top: 0; margin-bottom: 0.75rem; color: var(--text-main);">
                                3. <?php esc_html_e( 'Refund & Cancellation Policy', 'digital-marketplace' ); ?>
                            </h2>
                            <p>
                                <?php esc_html_e( 'Due to the non-returnable, irrevocable nature of digital software and code downloads, all completed digital sales are generally final once the download token has been accessed or redeemed.', 'digital-marketplace' ); ?>
                            </p>
                            <p>
                                <strong><?php esc_html_e( 'Exceptions where refunds are honored:', 'digital-marketplace' ); ?></strong>
                            </p>
                            <ul style="padding-left: 1.25rem; margin: 0.75rem 0;">
                                <li><?php esc_html_e( 'The downloadable archive file is proven corrupt or missing essential assets advertised in the product documentation and our engineering team is unable to provide a working build within 48 hours.', 'digital-marketplace' ); ?></li>
                                <li><?php esc_html_e( 'An accidental duplicate order was submitted for the exact same digital asset before any files were downloaded.', 'digital-marketplace' ); ?></li>
                                <li><?php esc_html_e( 'The order was cancelled prior to payment confirmation.', 'digital-marketplace' ); ?></li>
                            </ul>
                            <p>
                                <?php esc_html_e( 'When an order status is updated to "Cancelled" or "Refunded" by site administration, associated digital download tokens are immediately revoked and rendered inactive.', 'digital-marketplace' ); ?>
                            </p>
                        </section>

                        <section id="crypto-payments" style="margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-subtle);">
                            <h2 style="font-size: 1.35rem; font-weight: 800; margin-top: 0; margin-bottom: 0.75rem; color: var(--text-main);">
                                4. <?php esc_html_e( 'Cryptocurrency Payments & Blockchain Settlement', 'digital-marketplace' ); ?>
                            </h2>
                            <p>
                                <?php esc_html_e( 'When selecting cryptocurrency at checkout, you must send the exact token amount to the designated wallet address within the valid checkout window. Blockchain transactions are irreversible on-chain. Customers are solely responsible for inputting correct network gas/miner fees.', 'digital-marketplace' ); ?>
                            </p>
                        </section>

                        <section id="support">
                            <h2 style="font-size: 1.35rem; font-weight: 800; margin-top: 0; margin-bottom: 0.75rem; color: var(--text-main);">
                                5. <?php esc_html_e( 'Customer Support & Inquiries', 'digital-marketplace' ); ?>
                            </h2>
                            <p>
                                <?php esc_html_e( 'For any licensing clarifications, technical support with extracted archives, or billing inquiries, please contact our support team with your order reference number.', 'digital-marketplace' ); ?>
                            </p>
                            <div style="margin-top: 1rem;">
                                <a href="mailto:<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" class="btn btn-primary btn-sm">
                                    📧 <?php esc_html_e( 'Email Store Support', 'digital-marketplace' ); ?> (<?php echo esc_html( get_option( 'admin_email' ) ); ?>)
                                </a>
                            </div>
                        </section>

                    <?php endif; ?>
                <?php endwhile; ?>

            </main>

        </div>

    </div>
</div>

<?php get_footer(); ?>

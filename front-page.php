<?php
/**
 * The Front Page template for Digital Marketplace
 * Features dynamic personalization, recently viewed shelf, flash deals countdown,
 * weekly popular trends, new arrivals, and dedicated category shelves.
 *
 * @package Digital_Marketplace
 */

get_header();

$products_url = get_post_type_archive_link( 'product' ) ?: home_url( '/products' );

/**
 * Fallback curated card renderer for guaranteed rich display
 */
function dmp_render_curated_card( $item, $badge = '' ) {
    $title    = isset( $item['title'] ) ? $item['title'] : 'Digital Product';
    $cat      = isset( $item['category'] ) ? $item['category'] : 'Digital Asset';
    $format   = isset( $item['format'] ) ? $item['format'] : 'Instant download';
    $rating   = isset( $item['rating'] ) ? $item['rating'] : '4.9';
    $reviews  = isset( $item['reviews'] ) ? $item['reviews'] : '94';
    $price    = isset( $item['price'] ) ? (float) $item['price'] : 29.00;
    $orig     = isset( $item['orig'] ) ? (float) $item['orig'] : null;
    $desc     = isset( $item['desc'] ) ? $item['desc'] : 'Instant digital fulfillment with lifetime updates and commercial license.';
    $initial  = strtoupper( substr( $title, 0, 1 ) );
    $url      = isset( $item['url'] ) ? $item['url'] : home_url( '/products?s=' . urlencode( $title ) );
    $img_bg   = isset( $item['color'] ) ? $item['color'] : 'linear-gradient(135deg, #182834, #0b151c)';
    $discount = ( $orig && $orig > $price ) ? '-' . round( ( ( $orig - $price ) / $orig ) * 100 ) . '%' : '';
    ?>
    <article class="market-card curated-card" data-category="<?php echo esc_attr( strtolower( $cat ) ); ?>">
        <a class="market-card-image" href="<?php echo esc_url( $url ); ?>">
            <div class="image-placeholder curated-art" style="background: <?php echo esc_attr( $img_bg ); ?>;">
                <span><?php echo esc_html( $initial ); ?></span>
            </div>
            <span class="card-category"><?php echo esc_html( $cat ); ?></span>
            <?php if ( ! empty( $badge ) ) : ?>
                <span class="card-promo-badge"><?php echo esc_html( $badge ); ?></span>
            <?php elseif ( ! empty( $discount ) ) : ?>
                <span class="card-promo-badge deal-badge"><?php echo esc_html( $discount ); ?></span>
            <?php endif; ?>
            <button class="wishlist" type="button" aria-label="<?php esc_attr_e( 'Add to wishlist', 'digital-marketplace' ); ?>">♡</button>
        </a>
        <div class="market-card-body">
            <div class="card-meta">
                <span><?php echo esc_html( $format ); ?></span>
                <span>★ <?php echo esc_html( $rating ); ?> (<?php echo esc_html( $reviews ); ?>)</span>
            </div>
            <h3><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h3>
            <p><?php echo esc_html( wp_trim_words( $desc, 12 ) ); ?></p>
            <div class="card-buy">
                <div>
                    <strong>$<?php echo esc_html( number_format( $price, 2 ) ); ?></strong>
                    <?php if ( $orig ) : ?>
                        <del>$<?php echo esc_html( number_format( $orig, 2 ) ); ?></del>
                    <?php endif; ?>
                </div>
                <a href="<?php echo esc_url( $url ); ?>" class="btn-card-view"><?php esc_html_e( 'View →', 'digital-marketplace' ); ?></a>
            </div>
        </div>
    </article>
    <?php
}

/**
 * Smart Shelf Renderer: Queries database first, falls back to realistic curated products
 */
function dmp_render_category_shelf( $slug, $title, $eyebrow, $desc, $curated_fallback, $badge = '', $section_id = '' ) {
    $products_url = get_post_type_archive_link( 'product' ) ?: home_url( '/products' );
    $cat_link     = home_url( '/products?product_cat=' . urlencode( $slug ) );
    
    // Check if category term exists for direct link
    $term = get_term_by( 'slug', $slug, 'product_cat' );
    if ( $term && ! is_wp_error( $term ) ) {
        $cat_link = get_term_link( $term );
    }

    $query = new WP_Query( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 4,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => array( $slug ),
            ),
        ),
    ) );
    ?>
    <section class="section shelf-section" <?php echo $section_id ? 'id="' . esc_attr( $section_id ) . '"' : ''; ?>>
        <div class="site-container">
            <div class="section-heading">
                <div>
                    <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
                    <h2><?php echo esc_html( $title ); ?></h2>
                    <p><?php echo esc_html( $desc ); ?></p>
                </div>
                <div class="shelf-controls">
                    <a class="outline-btn" href="<?php echo esc_url( $cat_link ); ?>"><?php esc_html_e( 'Explore collection →', 'digital-marketplace' ); ?></a>
                </div>
            </div>
            <div class="products-grid four">
                <?php
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        digital_marketplace_render_card( get_the_ID(), $badge );
                    }
                    wp_reset_postdata();
                } else {
                    foreach ( $curated_fallback as $item ) {
                        dmp_render_curated_card( $item, $badge );
                    }
                }
                ?>
            </div>
        </div>
    </section>
    <?php
}
?>

<!-- =========================================================================
     1. HERO SHOWCASE WITH INSTANT SEARCH & METRICS
     ========================================================================= -->
<section class="hero-premium">
    <div class="site-container hero-wrap">
        <div class="hero-copy">
            <span class="hero-badge"><i></i> Premium digital goods, delivered instantly</span>
            <h1>Everything digital.<br><em>Personalized for you.</em></h1>
            <p>Discover hand-verified subscriptions, developer software, hosting licenses, UI design systems, and business tools from one dynamic marketplace.</p>
            
            <form class="hero-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span>⌕</span>
                <input type="search" name="s" placeholder="Search subscriptions, software, hosting, UI kits..." aria-label="Search products" autocomplete="off">
                <input type="hidden" name="post_type" value="product">
                <button type="submit"><?php esc_html_e( 'Search', 'digital-marketplace' ); ?></button>
                <div class="instant-search-popover hero-search-popover" aria-hidden="true" role="listbox"></div>
            </form>

            <div class="hero-tags">
                <span>Popular:</span>
                <a href="#deals-section">⚡ Deals</a>
                <a href="#subscriptions-section">Subscriptions</a>
                <a href="#software-section">Software</a>
                <a href="#hosting-section">Hosting</a>
                <a href="#design-section">Design Kits</a>
                <a href="#business-section">Business</a>
            </div>

            <div class="hero-proof">
                <strong>★ 4.9/5 verified rating</strong>
                <span>•</span>
                <span>Instant digital downloads</span>
                <span>•</span>
                <span>Dynamic customer recommendations</span>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-glow"></div>
            <div class="dashboard-card">
                <div class="dashboard-top">
                    <span class="window-dots"><i></i><i></i><i></i></span>
                    <b>marketplace personalization hub</b>
                    <small class="live-pill">● DYNAMIC</small>
                </div>
                <div class="dashboard-body">
                    <div class="dashboard-welcome">
                        <div>
                            <small>Browsing Preferences</small>
                            <strong>Tailored1 Marketplace Feed</strong>
                        </div>
                        <span class="curated-status-pill">Active</span>
                    </div>
                    <div class="mini-grid">
                        <div class="mini-panel large">
                            <small>TRENDING DEMAND</small>
                            <h4>Popular Digital Tools</h4>
                            <div class="mini-chart"></div>
                            <div class="mini-list"><span></span><span></span><span></span></div>
                        </div>
                        <div class="mini-panel">
                            <small>FLASH DEALS</small>
                            <h4>Up to 50%</h4>
                            <span style="color:var(--accent);font-size:8px">Limited time</span>
                        </div>
                        <div class="mini-panel">
                            <small>CATEGORIES</small>
                            <h4>6 Core</h4>
                            <span style="color:#84939d;font-size:8px">Curated catalog</span>
                        </div>
                        <div class="mini-panel">
                            <small>RECENT VIEWS</small>
                            <h4 id="hero-view-counter">Saved</h4>
                            <span style="color:#84939d;font-size:8px">Session active</span>
                        </div>
                        <div class="mini-panel">
                            <small>DELIVERY</small>
                            <h4>Direct</h4>
                            <span style="color:#84939d;font-size:8px">Instant keys</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="floating-proof">
                <strong>4.9 ★</strong>
                <span>Over 12,000 satisfied buyers</span>
            </div>
            <div class="floating-delivery">
                <b>✓</b>
                <div>
                    <span>Instant Digital Access</span>
                    <small>Keys and downloads delivered post-checkout</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust Row -->
    <div class="hero-trust-row">
        <div class="site-container hero-trust-inner">
            <div><i class="trust-icon">✓</i><div><b>Verified Creators</b><span>Audited licenses & code</span></div></div>
            <div><i class="trust-icon">↯</i><div><b>Instant Fulfillment</b><span>No waiting for digital assets</span></div></div>
            <div><i class="trust-icon">◆</i><div><b>Personalized Suggestions</b><span>Reflects your active interests</span></div></div>
            <div><i class="trust-icon">◉</i><div><b>24/7 Order Support</b><span>Help with files & licenses</span></div></div>
        </div>
    </div>
</section>

<!-- =========================================================================
     2. DYNAMIC MARKETPLACE QUICK-JUMP NAVIGATION STRIP
     ========================================================================= -->
<nav class="marketplace-jump-nav" aria-label="<?php esc_attr_e( 'Marketplace Shelves', 'digital-marketplace' ); ?>">
    <div class="site-container jump-nav-inner">
        <span class="jump-nav-label"><?php esc_html_e( 'Jump to shelf:', 'digital-marketplace' ); ?></span>
        <div class="jump-nav-links">
            <a href="#recommended-section" class="jump-chip active">🎯 Recommended</a>
            <a href="#recently-viewed-section" class="jump-chip">🕒 Recently Viewed</a>
            <a href="#popular-section" class="jump-chip">🔥 Popular This Week</a>
            <a href="#new-arrivals-section" class="jump-chip">✨ New Arrivals</a>
            <a href="#deals-section" class="jump-chip">⚡ Limited Deals</a>
            <a href="#subscriptions-section" class="jump-chip">📦 Subscriptions</a>
            <a href="#software-section" class="jump-chip">💻 Software & Tools</a>
            <a href="#hosting-section" class="jump-chip">☁️ Hosting & Cloud</a>
            <a href="#entertainment-section" class="jump-chip">🎮 Entertainment</a>
            <a href="#design-section" class="jump-chip">🎨 Design Resources</a>
            <a href="#business-section" class="jump-chip">💼 Business Tools</a>
        </div>
    </div>
</nav>

<!-- =========================================================================
     3. RECOMMENDED FOR YOU (Personalized Feed with Dynamic Filter Pills)
     ========================================================================= -->
<section id="recommended-section" class="section shelf-section">
    <div class="site-container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Personalized for you</span>
                <h2>Recommended For You</h2>
                <p>Smart recommendations tuned to your workflow, recent searches, and popular items in your focus areas.</p>
            </div>
            <div class="rec-filter-chips" id="recommended-filter-chips">
                <button type="button" class="rec-chip active" data-rec-filter="all">★ All Matches</button>
                <button type="button" class="rec-chip" data-rec-filter="software">Software</button>
                <button type="button" class="rec-chip" data-rec-filter="design">Design Kits</button>
                <button type="button" class="rec-chip" data-rec-filter="subscriptions">Subscriptions</button>
                <button type="button" class="rec-chip" data-rec-filter="business">Business</button>
            </div>
        </div>

        <div class="products-grid four" id="recommended-products-grid">
            <?php
            // Fetch top-rated or featured products
            $rec_query = new WP_Query( array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 4,
                'meta_key'       => '_product_rating',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
            ) );

            if ( $rec_query->have_posts() ) {
                $i = 0;
                $badges = array( '98% Match', '★ Top Pick', 'Trending', 'Recommended' );
                while ( $rec_query->have_posts() ) {
                    $rec_query->the_post();
                    digital_marketplace_render_card( get_the_ID(), $badges[ $i % 4 ] );
                    $i++;
                }
                wp_reset_postdata();
            } else {
                $curated_recs = array(
                    array(
                        'title'    => 'Apex Next.js SaaS Starter & Boilerplate',
                        'category' => 'Software',
                        'format'   => 'TypeScript / Next.js 15',
                        'rating'   => '4.98',
                        'reviews'  => '142',
                        'price'    => 79.00,
                        'orig'     => 129.00,
                        'desc'     => 'Complete authentication, billing portal, and multi-tenant database infrastructure.',
                        'color'    => 'linear-gradient(135deg, #142a38, #0e1d27)',
                    ),
                    array(
                        'title'    => 'FluidDesign 3D Icon & UI Mega Bundle',
                        'category' => 'Design Resources',
                        'format'   => 'Figma / Blender / PNG',
                        'rating'   => '4.95',
                        'reviews'  => '210',
                        'price'    => 48.00,
                        'orig'     => 89.00,
                        'desc'     => 'Over 1,200 ultra-high definition rendered 3D icons, illustrations, and design tokens.',
                        'color'    => 'linear-gradient(135deg, #2b1a3d, #140d21)',
                    ),
                    array(
                        'title'    => 'CloudStream Unlimited Developer VPN & Proxy',
                        'category' => 'Subscriptions',
                        'format'   => 'Annual Pass / Multi-Device',
                        'rating'   => '4.92',
                        'reviews'  => '88',
                        'price'    => 39.00,
                        'orig'     => 69.00,
                        'desc'     => 'Low-latency dedicated residential IP access for testing, development, and secure browsing.',
                        'color'    => 'linear-gradient(135deg, #132e29, #091a17)',
                    ),
                    array(
                        'title'    => 'SaaS Metrics & Investor Model Spreadsheet',
                        'category' => 'Business Tools',
                        'format'   => 'Excel / Google Sheets',
                        'rating'   => '4.90',
                        'reviews'  => '95',
                        'price'    => 34.00,
                        'orig'     => 59.00,
                        'desc'     => 'Institutional grade financial model with cohort retention, MRR forecasts, and burn calculations.',
                        'color'    => 'linear-gradient(135deg, #2e2614, #1a150a)',
                    ),
                );
                $badges = array( '98% Match', '★ Top Pick', 'Trending', 'Recommended' );
                foreach ( $curated_recs as $i => $item ) {
                    dmp_render_curated_card( $item, $badges[ $i % 4 ] );
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     4. RECENTLY VIEWED SHELF (Live LocalStorage Client Dynamic Persistence)
     ========================================================================= -->
<section id="recently-viewed-section" class="section shelf-section recently-viewed-wrap">
    <div class="site-container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Your browsing history</span>
                <h2>Recently Viewed</h2>
                <p>Pick up right where you left off. Items you inspect in the catalog automatically sync here.</p>
            </div>
            <div class="recently-viewed-actions">
                <button type="button" class="clear-history-btn" id="clear-recent-history" style="display:none;">
                    <?php esc_html_e( '✕ Clear history', 'digital-marketplace' ); ?>
                </button>
            </div>
        </div>

        <!-- Dynamic container hydrated by marketplace.js -->
        <div class="products-grid four" id="recently-viewed-shelf">
            <!-- Populated via JavaScript if viewed items exist -->
        </div>

        <!-- Empty State when user hasn't browsed items yet -->
        <div class="recent-view-empty" id="recently-viewed-empty">
            <div class="recent-empty-icon">🕒</div>
            <h3><?php esc_html_e( 'Your history is ready to populate', 'digital-marketplace' ); ?></h3>
            <p><?php esc_html_e( 'Click on any product in the marketplace to inspect its details, and it will automatically be remembered here for easy reference.', 'digital-marketplace' ); ?></p>
            <a href="<?php echo esc_url( $products_url ); ?>" class="btn-primary-compact">
                <?php esc_html_e( 'Browse catalog to get started →', 'digital-marketplace' ); ?>
            </a>
        </div>
    </div>
</section>

<!-- =========================================================================
     5. LIMITED-TIME DEALS (Flash Sale with Live Ticking Countdown)
     ========================================================================= -->
<section id="deals-section" class="section shelf-section deals-section-bg">
    <div class="site-container">
        <div class="section-heading deals-heading-row">
            <div>
                <span class="eyebrow deals-eyebrow">⚡ Flash digital sale</span>
                <h2>Limited-Time Deals</h2>
                <p>Special promotional discounts on premium digital licenses and toolkits. Offers refresh weekly.</p>
            </div>
            
            <!-- Live Ticking Countdown Timer Widget -->
            <div class="deals-countdown-box" id="deals-countdown">
                <div class="countdown-pulse-dot"></div>
                <span class="countdown-label">Offers end in:</span>
                <div class="countdown-timer">
                    <span class="countdown-unit"><b id="deals-hrs">07</b><small>HRS</small></span>
                    <span class="countdown-colon">:</span>
                    <span class="countdown-unit"><b id="deals-mins">34</b><small>MIN</small></span>
                    <span class="countdown-colon">:</span>
                    <span class="countdown-unit"><b id="deals-secs">18</b><small>SEC</small></span>
                </div>
            </div>
        </div>

        <div class="products-grid four">
            <?php
            // Query products with discounted price
            $deals_query = new WP_Query( array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 4,
                'meta_key'       => '_product_original_price',
                'meta_compare'   => 'EXISTS',
            ) );

            if ( $deals_query->have_posts() ) {
                while ( $deals_query->have_posts() ) {
                    $deals_query->the_post();
                    digital_marketplace_render_card( get_the_ID(), '-45% OFF' );
                }
                wp_reset_postdata();
            } else {
                $curated_deals = array(
                    array(
                        'title'    => 'KubeOps Kubernetes Cluster Automation CLI',
                        'category' => 'Software & Tools',
                        'format'   => 'Go Binary / Docker',
                        'rating'   => '4.94',
                        'reviews'  => '76',
                        'price'    => 39.00,
                        'orig'     => 79.00,
                        'desc'     => 'One-line multi-region Kubernetes provisioning, autoscaling, and secret rotation CLI tool.',
                        'color'    => 'linear-gradient(135deg, #261623, #120b11)',
                    ),
                    array(
                        'title'    => 'UltraFast Edge SSD VPS 4-Core Cloud Plan',
                        'category' => 'Hosting & Domains',
                        'format'   => '1-Year Subscription Key',
                        'rating'   => '4.91',
                        'reviews'  => '130',
                        'price'    => 54.00,
                        'orig'     => 108.00,
                        'desc'     => '10Gbps unmetered bandwidth, NVMe storage, and automated snapshots across 14 global points of presence.',
                        'color'    => 'linear-gradient(135deg, #132731, #0a141a)',
                    ),
                    array(
                        'title'    => 'OmniSound Cinema Foley & Audio FX Vault',
                        'category' => 'Entertainment',
                        'format'   => '96kHz / 24-Bit WAV',
                        'rating'   => '4.96',
                        'reviews'  => '92',
                        'price'    => 29.00,
                        'orig'     => 65.00,
                        'desc'     => 'Over 4,500 lossless cinematic sound effects, sci-fi UI beeps, ambient soundscapes, and impacts.',
                        'color'    => 'linear-gradient(135deg, #291a14, #170d08)',
                    ),
                    array(
                        'title'    => 'NeoUI Mobile Design System (iOS & Android)',
                        'category' => 'Design Resources',
                        'format'   => 'Figma Component Library',
                        'rating'   => '4.97',
                        'reviews'  => '164',
                        'price'    => 38.00,
                        'orig'     => 76.00,
                        'desc'     => 'Over 650 auto-layout components, light & dark variants, and typography tokens.',
                        'color'    => 'linear-gradient(135deg, #1a2233, #0c121d)',
                    ),
                );
                foreach ( $curated_deals as $item ) {
                    dmp_render_curated_card( $item, '-50% OFF' );
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     6. POPULAR THIS WEEK (High Demand & Community Momentum)
     ========================================================================= -->
<section id="popular-section" class="section shelf-section">
    <div class="site-container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Community favorites</span>
                <h2>Popular This Week</h2>
                <p>The highest volume digital licenses and assets purchased by builders, creators, and teams in the last 7 days.</p>
            </div>
            <a class="outline-btn" href="<?php echo esc_url( $products_url ); ?>"><?php esc_html_e( 'View leaderboard →', 'digital-marketplace' ); ?></a>
        </div>

        <div class="products-grid four">
            <?php
            $popular_query = new WP_Query( array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 4,
                'meta_key'       => '_product_review_count',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
            ) );

            if ( $popular_query->have_posts() ) {
                $badges = array( '🔥 #1 Trend', '🔥 Selling Fast', '🔥 High Demand', 'Popular' );
                $i = 0;
                while ( $popular_query->have_posts() ) {
                    $popular_query->the_post();
                    digital_marketplace_render_card( get_the_ID(), $badges[ $i % 4 ] );
                    $i++;
                }
                wp_reset_postdata();
            } else {
                $curated_popular = array(
                    array(
                        'title'    => 'Enterprise REST API Security & Rate-Limiter SDK',
                        'category' => 'Software & Tools',
                        'format'   => 'Node.js / Python / Go',
                        'rating'   => '4.97',
                        'reviews'  => '245',
                        'price'    => 65.00,
                        'orig'     => 99.00,
                        'desc'     => 'Token-bucket algorithmic rate limiter with Redis backend, IP reputation scoring, and DDoS prevention.',
                        'color'    => 'linear-gradient(135deg, #172b38, #0a151d)',
                    ),
                    array(
                        'title'    => 'StreamMaster Pro 4K Live Broadcast Encoder',
                        'category' => 'Subscriptions',
                        'format'   => 'Lifetime Commercial Key',
                        'rating'   => '4.93',
                        'reviews'  => '188',
                        'price'    => 49.00,
                        'orig'     => 85.00,
                        'desc'     => 'Hardware-accelerated 4K multi-destination live streaming software for Windows & macOS.',
                        'color'    => 'linear-gradient(135deg, #241a2e, #110b17)',
                    ),
                    array(
                        'title'    => 'VectorCraft 2,400 Hand-Drawn Minimalist Icons',
                        'category' => 'Design Resources',
                        'format'   => 'SVG / React Icons / Figma',
                        'rating'   => '4.95',
                        'reviews'  => '312',
                        'price'    => 32.00,
                        'orig'     => 55.00,
                        'desc'     => 'Consistent 24px grid vector icons across 18 business, technology, and navigation categories.',
                        'color'    => 'linear-gradient(135deg, #162a22, #0a1712)',
                    ),
                    array(
                        'title'    => 'CloudShield Enterprise Wildcard SSL Certificate',
                        'category' => 'Hosting & Domains',
                        'format'   => '2-Year License Key',
                        'rating'   => '4.99',
                        'reviews'  => '270',
                        'price'    => 45.00,
                        'orig'     => 89.00,
                        'desc'     => 'Full 256-bit encryption with unlimited sub-domains, instantaneous validation, and $1.5M warranty.',
                        'color'    => 'linear-gradient(135deg, #2d2315, #161008)',
                    ),
                );
                $badges = array( '🔥 #1 Trend', '🔥 Selling Fast', '🔥 High Demand', 'Popular' );
                foreach ( $curated_popular as $i => $item ) {
                    dmp_render_curated_card( $item, $badges[ $i % 4 ] );
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     7. NEW ARRIVALS (Fresh Releases & Framework Updates)
     ========================================================================= -->
<section id="new-arrivals-section" class="section shelf-section">
    <div class="site-container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Fresh releases</span>
                <h2>New Arrivals</h2>
                <p>Newly audited digital products, recent version updates, and freshly published tools.</p>
            </div>
            <a class="outline-btn" href="<?php echo esc_url( $products_url ); ?>"><?php esc_html_e( 'Browse newest →', 'digital-marketplace' ); ?></a>
        </div>

        <div class="products-grid four">
            <?php
            $new_query = new WP_Query( array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 4,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );

            if ( $new_query->have_posts() ) {
                while ( $new_query->have_posts() ) {
                    $new_query->the_post();
                    digital_marketplace_render_card( get_the_ID(), '✨ New Arrival' );
                }
                wp_reset_postdata();
            } else {
                $curated_new = array(
                    array(
                        'title'    => 'PromptForge AI Fine-Tuning & Evaluation Suite',
                        'category' => 'Software & Tools',
                        'format'   => 'Python / Web GUI',
                        'rating'   => '5.0',
                        'reviews'  => '24',
                        'price'    => 59.00,
                        'orig'     => 89.00,
                        'desc'     => 'Benchmark LLM responses, manage prompt system versions, and track hallucination rates.',
                        'color'    => 'linear-gradient(135deg, #182834, #0b151c)',
                    ),
                    array(
                        'title'    => 'SaaS Billing & Invoicing Automation Microservice',
                        'category' => 'Business Tools',
                        'format'   => 'TypeScript / Docker',
                        'rating'   => '4.94',
                        'reviews'  => '31',
                        'price'    => 44.00,
                        'orig'     => 69.00,
                        'desc'     => 'Automated tax calculations, VAT validation, PDF invoice generator, and dunning webhooks.',
                        'color'    => 'linear-gradient(135deg, #2a1b18, #160d0b)',
                    ),
                    array(
                        'title'    => 'HyperTone 80s Synthwave Synth VST Plugin',
                        'category' => 'Entertainment',
                        'format'   => 'VST3 / AU / AAX',
                        'rating'   => '4.96',
                        'reviews'  => '42',
                        'price'    => 35.00,
                        'orig'     => 60.00,
                        'desc'     => 'Authentic analog modeled subtractive synthesizer with 180 vintage presets and tape chorus.',
                        'color'    => 'linear-gradient(135deg, #24142f, #120919)',
                    ),
                    array(
                        'title'    => 'StaticDeploy Edge DNS & Global CDN Manager',
                        'category' => 'Hosting & Domains',
                        'format'   => 'CLI / REST API',
                        'rating'   => '4.90',
                        'reviews'  => '19',
                        'price'    => 29.00,
                        'orig'     => 49.00,
                        'desc'     => 'Deploy static frontends and preview builds across 300+ global edge nodes in under 2 seconds.',
                        'color'    => 'linear-gradient(135deg, #132a26, #091714)',
                    ),
                );
                foreach ( $curated_new as $item ) {
                    dmp_render_curated_card( $item, '✨ New Release' );
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================================================
     8. CATEGORY SHELF: SUBSCRIPTIONS & MEMBERSHIPS
     ========================================================================= -->
<?php
dmp_render_category_shelf(
    'subscriptions',
    'Subscriptions & Memberships',
    'Recurring access',
    'Curated premium memberships, streaming passes, and annual developer platforms.',
    array(
        array(
            'title'    => 'CloudVPN Ultra Developer Pass (1-Year Key)',
            'category' => 'Subscriptions',
            'format'   => 'Multi-Device License',
            'rating'   => '4.94',
            'reviews'  => '115',
            'price'    => 45.00,
            'orig'     => 79.00,
            'desc'     => 'Unlimited high-speed wireguard tunnels, dedicated IP, and zero activity logs.',
            'color'    => 'linear-gradient(135deg, #142834, #0b161d)',
        ),
        array(
            'title'    => 'AudioStream Pro Hi-Fi Creator Membership',
            'category' => 'Subscriptions',
            'format'   => '12-Month Passcode',
            'rating'   => '4.91',
            'reviews'  => '86',
            'price'    => 39.00,
            'orig'     => 69.00,
            'desc'     => 'Royalty-free commercial music licensing for YouTube, podcasts, games, and advertisements.',
            'color'    => 'linear-gradient(135deg, #271932, #130a1c)',
        ),
        array(
            'title'    => 'DevVault Private Code Repository & CI/CD Pro',
            'category' => 'Subscriptions',
            'format'   => 'Annual Team License',
            'rating'   => '4.98',
            'reviews'  => '140',
            'price'    => 69.00,
            'orig'     => 119.00,
            'desc'     => 'Self-hostable git repository server with embedded Docker registry and build runners.',
            'color'    => 'linear-gradient(135deg, #172820, #0a1711)',
        ),
        array(
            'title'    => 'SecureBackup 2TB Encrypted Cloud Storage Pass',
            'category' => 'Subscriptions',
            'format'   => 'Account Activation Token',
            'rating'   => '4.89',
            'reviews'  => '94',
            'price'    => 34.00,
            'orig'     => 58.00,
            'desc'     => 'End-to-end zero-knowledge encrypted cloud backup with automated delta synchronization.',
            'color'    => 'linear-gradient(135deg, #2a2014, #151009)',
        ),
    ),
    'Subscription',
    'subscriptions-section'
);
?>

<!-- =========================================================================
     9. CATEGORY SHELF: SOFTWARE & TOOLS
     ========================================================================= -->
<?php
dmp_render_category_shelf(
    'software',
    'Software & Developer Tools',
    'Productivity & utilities',
    'Native applications, command-line utilities, automation scripts, and developer toolkits.',
    array(
        array(
            'title'    => 'SQLStudio Native PostgreSQL & MySQL GUI Client',
            'category' => 'Software & Tools',
            'format'   => 'macOS / Windows / Linux',
            'rating'   => '4.97',
            'reviews'  => '174',
            'price'    => 49.00,
            'orig'     => 79.00,
            'desc'     => 'Blazing fast native database client with visual query builder and SSH tunneling.',
            'color'    => 'linear-gradient(135deg, #152634, #0a141d)',
        ),
        array(
            'title'    => 'TerminalCraft GPU-Accelerated Shell Companion',
            'category' => 'Software & Tools',
            'format'   => 'Rust Binary',
            'rating'   => '4.95',
            'reviews'  => '122',
            'price'    => 28.00,
            'orig'     => 45.00,
            'desc'     => 'Modern terminal emulator featuring splits, tabs, AI auto-completions, and 24-bit RGB colors.',
            'color'    => 'linear-gradient(135deg, #241731, #11091a)',
        ),
        array(
            'title'    => 'AutoScrape Distributed Web Extraction Toolkit',
            'category' => 'Software & Tools',
            'format'   => 'Python 3 / CLI',
            'rating'   => '4.92',
            'reviews'  => '98',
            'price'    => 39.00,
            'orig'     => 69.00,
            'desc'     => 'Headless browser automation framework with automatic captcha resolution and proxy rotation.',
            'color'    => 'linear-gradient(135deg, #142a22, #091712)',
        ),
        array(
            'title'    => 'API Inspector Pro Mocking & Performance Tester',
            'category' => 'Software & Tools',
            'format'   => 'Desktop App / Electron',
            'rating'   => '4.88',
            'reviews'  => '85',
            'price'    => 35.00,
            'orig'     => 59.00,
            'desc'     => 'Intercept, debug, mock, and stress-test HTTP, gRPC, and WebSocket network calls.',
            'color'    => 'linear-gradient(135deg, #2b1f14, #171008)',
        ),
    ),
    'Software',
    'software-section'
);
?>

<!-- =========================================================================
     10. CATEGORY SHELF: HOSTING & DOMAINS
     ========================================================================= -->
<?php
dmp_render_category_shelf(
    'hosting',
    'Hosting, Domains & Cloud Services',
    'Infrastructure',
    'Cloud compute nodes, private VPS licenses, multi-domain SSL certs, and DNS managers.',
    array(
        array(
            'title'    => 'CloudVPS NVMe 8GB Dedicated RAM Compute Instance',
            'category' => 'Hosting & Domains',
            'format'   => '1-Year Pre-paid Server',
            'rating'   => '4.96',
            'reviews'  => '148',
            'price'    => 69.00,
            'orig'     => 120.00,
            'desc'     => 'High-frequency AMD EPYC processors with 160GB NVMe storage and root SSH control.',
            'color'    => 'linear-gradient(135deg, #132430, #09121a)',
        ),
        array(
            'title'    => 'Enterprise Wildcard Multi-SAN SSL Certificate',
            'category' => 'Hosting & Domains',
            'format'   => 'Instant CSR Generation',
            'rating'   => '4.99',
            'reviews'  => '210',
            'price'    => 42.00,
            'orig'     => 75.00,
            'desc'     => 'Secures primary domain and unlimited second-level subdomains with 99.9% browser trust.',
            'color'    => 'linear-gradient(135deg, #22142e, #100818)',
        ),
        array(
            'title'    => 'DNSShield Anycast Global Geo-DNS Network Pass',
            'category' => 'Hosting & Domains',
            'format'   => 'Annual Service License',
            'rating'   => '4.93',
            'reviews'  => '72',
            'price'    => 29.00,
            'orig'     => 49.00,
            'desc'     => 'Sub-10ms global DNS lookup resolution with health-check failover and DDoS scrubbing.',
            'color'    => 'linear-gradient(135deg, #13271f, #081611)',
        ),
        array(
            'title'    => 'ObjectStorage 5TB S3-Compatible Storage Bucket',
            'category' => 'Hosting & Domains',
            'format'   => '1-Year Storage Key',
            'rating'   => '4.91',
            'reviews'  => '89',
            'price'    => 48.00,
            'orig'     => 85.00,
            'desc'     => 'S3 API compatible object storage with 99.999999999% durability and zero egress fees.',
            'color'    => 'linear-gradient(135deg, #291e13, #150f08)',
        ),
    ),
    'Hosting',
    'hosting-section'
);
?>

<!-- =========================================================================
     11. CATEGORY SHELF: ENTERTAINMENT
     ========================================================================= -->
<?php
dmp_render_category_shelf(
    'entertainment',
    'Entertainment & Media',
    'Sound & gaming',
    'Lossless audio soundscapes, game engine 3D kits, music stems, and digital streaming passes.',
    array(
        array(
            'title'    => 'Cyberpunk 2088 Sci-Fi Audio FX & Synthwave Library',
            'category' => 'Entertainment',
            'format'   => 'Lossless WAV / Kontakt',
            'rating'   => '4.97',
            'reviews'  => '165',
            'price'    => 35.00,
            'orig'     => 60.00,
            'desc'     => 'Over 800 futuristic mechanical sounds, weapon blasts, vehicle engines, and synth loops.',
            'color'    => 'linear-gradient(135deg, #28142a, #140816)',
        ),
        array(
            'title'    => 'LowPoly Fantasy Medieval RPG Game Asset Kit',
            'category' => 'Entertainment',
            'format'   => 'FBX / Blender / Unreal',
            'rating'   => '4.94',
            'reviews'  => '132',
            'price'    => 42.00,
            'orig'     => 75.00,
            'desc'     => 'Modular castles, character rigs, weapons, and particle effects optimized for mobile and VR.',
            'color'    => 'linear-gradient(135deg, #152433, #09131c)',
        ),
        array(
            'title'    => 'GamePass Plus 12-Month Digital Cloud Gaming Code',
            'category' => 'Entertainment',
            'format'   => 'Redeemable Key',
            'rating'   => '4.90',
            'reviews'  => '95',
            'price'    => 59.00,
            'orig'     => 99.00,
            'desc'     => 'Instant access to over 300 AAA titles with cloud ray-tracing streaming on any browser.',
            'color'    => 'linear-gradient(135deg, #13271d, #08150f)',
        ),
        array(
            'title'    => 'Cinematic Orchestral Trailer Stems & Midi Pack',
            'category' => 'Entertainment',
            'format'   => '24-Bit WAV / MIDI',
            'rating'   => '4.93',
            'reviews'  => '79',
            'price'    => 29.00,
            'orig'     => 52.00,
            'desc'     => '12 complete dramatic orchestral arrangements broken into separated instrument multitracks.',
            'color'    => 'linear-gradient(135deg, #281c12, #140d07)',
        ),
    ),
    'Entertainment',
    'entertainment-section'
);
?>

<!-- =========================================================================
     12. CATEGORY SHELF: DESIGN RESOURCES
     ========================================================================= -->
<?php
dmp_render_category_shelf(
    'design',
    'Design Resources & UI Kits',
    'Creative assets',
    'Modern Figma design systems, 3D icon sets, vector illustrations, and responsive layouts.',
    array(
        array(
            'title'    => 'Quantum Design System for Web & Mobile Applications',
            'category' => 'Design Resources',
            'format'   => 'Figma Auto-Layout 5.0',
            'rating'   => '4.99',
            'reviews'  => '340',
            'price'    => 49.00,
            'orig'     => 89.00,
            'desc'     => 'Massive library of 1,500+ scalable components, responsive grids, and design tokens.',
            'color'    => 'linear-gradient(135deg, #152535, #0a131e)',
        ),
        array(
            'title'    => 'Dimension 3D Clay Claymorphic Icon Collection',
            'category' => 'Design Resources',
            'format'   => 'Blender / PNG / GLTF',
            'rating'   => '4.96',
            'reviews'  => '180',
            'price'    => 36.00,
            'orig'     => 65.00,
            'desc'     => '320 beautifully rendered clay-style 3D illustrations with customizable materials.',
            'color'    => 'linear-gradient(135deg, #26162a, #120916)',
        ),
        array(
            'title'    => 'Editorial Typography & Branding Mockup Bundle',
            'category' => 'Design Resources',
            'format'   => 'PSD / Smart Objects',
            'rating'   => '4.92',
            'reviews'  => '118',
            'price'    => 32.00,
            'orig'     => 55.00,
            'desc'     => 'Photorealistic print, magazine, and packaging mockups with natural daylight reflections.',
            'color'    => 'linear-gradient(135deg, #142821, #081611)',
        ),
        array(
            'title'    => 'WireframeKit Flowchart & User Journey Builder',
            'category' => 'Design Resources',
            'format'   => 'Figma / Sketch / FigJam',
            'rating'   => '4.91',
            'reviews'  => '95',
            'price'    => 26.00,
            'orig'     => 45.00,
            'desc'     => 'Rapid UX ideation toolkit with hundreds of ready-to-wire cards, flows, and sitemap connectors.',
            'color'    => 'linear-gradient(135deg, #2b2114, #161008)',
        ),
    ),
    'Design Kit',
    'design-section'
);
?>

<!-- =========================================================================
     13. CATEGORY SHELF: BUSINESS TOOLS
     ========================================================================= -->
<?php
dmp_render_category_shelf(
    'business',
    'Business & Finance Tools',
    'Operations & legal',
    'Financial forecasting models, startup pitch decks, contracts, and CRM templates.',
    array(
        array(
            'title'    => 'Complete SaaS Venture Capital Pitch Deck Template',
            'category' => 'Business Tools',
            'format'   => 'Figma / Keynote / PPT',
            'rating'   => '4.98',
            'reviews'  => '215',
            'price'    => 45.00,
            'orig'     => 79.00,
            'desc'     => 'Tested structure based on decks that raised over $80M in Seed and Series A funding.',
            'color'    => 'linear-gradient(135deg, #172836, #0a151e)',
        ),
        array(
            'title'    => 'Universal Software Development & SLA Agreement Pack',
            'category' => 'Business Tools',
            'format'   => 'DOCX / PDF / Notion',
            'rating'   => '4.94',
            'reviews'  => '140',
            'price'    => 49.00,
            'orig'     => 89.00,
            'desc'     => 'Attorney-vetted master service agreements, statement of work templates, and NDA forms.',
            'color'    => 'linear-gradient(135deg, #27182e, #130a18)',
        ),
        array(
            'title'    => 'Notion Enterprise Workspace Operating System',
            'category' => 'Business Tools',
            'format'   => 'Notion Duplicate Link',
            'rating'   => '4.95',
            'reviews'  => '280',
            'price'    => 39.00,
            'orig'     => 69.00,
            'desc'     => 'All-in-one company wiki, sprint tracker, client CRM, invoice log, and goal system.',
            'color'    => 'linear-gradient(135deg, #142820, #091611)',
        ),
        array(
            'title'    => 'E-Commerce CFO Financial Model & Inventory Planner',
            'category' => 'Business Tools',
            'format'   => 'Google Sheets / Excel',
            'rating'   => '4.91',
            'reviews'  => '98',
            'price'    => 35.00,
            'orig'     => 62.00,
            'desc'     => 'Automated cash conversion cycle, unit economics, reorder triggers, and ad spend ROI models.',
            'color'    => 'linear-gradient(135deg, #2b2014, #150f08)',
        ),
    ),
    'Business',
    'business-section'
);
?>

<!-- =========================================================================
     14. VALUE STRIP & AUDIENCE USE CASES
     ========================================================================= -->
<section class="value-strip">
    <div class="site-container value-grid">
        <div>
            <i class="value-icon">✓</i>
            <div>
                <strong>Secure Checkout</strong>
                <small>Stripe, PayPal & Crypto support with instant transaction receipts.</small>
            </div>
        </div>
        <div>
            <i class="value-icon">↯</i>
            <div>
                <strong>Instant Access</strong>
                <small>License keys and files available immediately upon payment completion.</small>
            </div>
        </div>
        <div>
            <i class="value-icon">▣</i>
            <div>
                <strong>Curated Quality</strong>
                <small>Every asset and code module is manually vetted for performance and security.</small>
            </div>
        </div>
        <div>
            <i class="value-icon">◉</i>
            <div>
                <strong>24/7 Human Help</strong>
                <small>Prompt technical assistance with license activation and digital downloads.</small>
            </div>
        </div>
    </div>
</section>

<section class="promo-section">
    <div class="site-container">
        <div class="promo-card">
            <div>
                <span class="promo-badge">BUYER GUARANTEE</span>
                <h2>Clean digital goods. Zero guesswork.</h2>
                <p>Browse detailed specifications, customer reviews, verified compatibility tags, and clear licensing terms before every digital purchase.</p>
                <a class="light-btn" href="<?php echo esc_url( $products_url ); ?>"><?php esc_html_e( 'Browse full catalog →', 'digital-marketplace' ); ?></a>
            </div>
            <div class="promo-art">
                <div class="promo-window"></div>
                <div class="promo-window"></div>
                <div class="promo-percent">FAST<small>DELIVERY</small></div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     15. FREQUENTLY ASKED QUESTIONS
     ========================================================================= -->
<section class="faq-section">
    <div class="site-container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Everything you need to know</span>
                <h2>Frequently Asked Questions</h2>
            </div>
        </div>
        <div class="faq-grid">
            <details class="faq-item">
                <summary>How are my recommendations generated?</summary>
                <p>Recommendations are dynamically tailored based on your active category selections, search queries, and recently viewed digital assets. No tracking cookies are sold to third parties.</p>
            </details>
            <details class="faq-item">
                <summary>Where is my Recently Viewed history stored?</summary>
                <p>Your history is saved locally in your browser session. You can review your recently inspected products anytime from the homepage, or clear your history with a single click.</p>
            </details>
            <details class="faq-item">
                <summary>When do I receive my digital product?</summary>
                <p>Immediately after checkout! Your license keys, download links, and access credentials appear on the order confirmation screen and in your customer account dashboard.</p>
            </details>
            <details class="faq-item">
                <summary>What payment methods are supported?</summary>
                <p>Our checkout supports major credit cards, Apple Pay, Google Pay, standard payments, and multi-cryptocurrency options (BTC, ETH, SOL, USDT, USDC).</p>
            </details>
        </div>
    </div>
</section>

<?php get_footer(); ?>

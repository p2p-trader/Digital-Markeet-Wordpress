<?php
/**
 * The template for displaying the Front Page (Home)
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="home-page-container">

    <!-- Hero Section: The Bold Memorable Moment -->
    <section id="hero-section" class="hero-section">
        <div class="site-container">
            <div class="hero-grid-layout">
                
                <!-- Left: Studio Manifesto & Search -->
                <div class="hero-content-col">
                    <div class="hero-pill">
                        <span class="hero-pill-indicator"></span>
                        <span><?php esc_html_e( 'Curated Engineering & Design Asset Studio', 'digital-marketplace' ); ?></span>
                    </div>

                    <h1 class="hero-title">
                        <?php esc_html_e( 'Production-ready design systems, boilerplates, and developer toolkits.', 'digital-marketplace' ); ?>
                    </h1>

                    <p class="hero-sub">
                        <?php esc_html_e( 'Engineered for high-velocity teams. Access audited UI component kits, full-stack Next.js and React boilerplates, and typography systems with instant cryptographic settlement and perpetual commercial rights.', 'digital-marketplace' ); ?>
                    </p>

                    <!-- Search Form -->
                    <form role="search" method="get" class="hero-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <span class="hero-search-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </span>
                        <input 
                            type="search" 
                            name="s" 
                            placeholder="<?php esc_attr_e( 'Search by framework, stack, or asset type (e.g. Next.js, Figma, Tailwind)...', 'digital-marketplace' ); ?>" 
                        />
                        <input type="hidden" name="post_type" value="product" />
                        <button type="submit" class="hero-search-btn">
                            <?php esc_html_e( 'Search Assets', 'digital-marketplace' ); ?>
                        </button>
                    </form>

                    <!-- Quick Technology Filter Tags -->
                    <div class="hero-quick-tags">
                        <span class="quick-tags-label"><?php esc_html_e( 'Popular Stacks:', 'digital-marketplace' ); ?></span>
                        <a href="<?php echo esc_url( home_url( '/products?s=nextjs' ) ); ?>" class="quick-tag-chip">Next.js 15</a>
                        <a href="<?php echo esc_url( home_url( '/products?s=figma' ) ); ?>" class="quick-tag-chip">Figma Variables</a>
                        <a href="<?php echo esc_url( home_url( '/products?s=tailwind' ) ); ?>" class="quick-tag-chip">Tailwind v4</a>
                        <a href="<?php echo esc_url( home_url( '/products?s=typescript' ) ); ?>" class="quick-tag-chip">TypeScript</a>
                    </div>

                    <!-- Verified Perks Row -->
                    <div class="hero-perks">
                        <div class="perk-item">
                            <span class="icon">✓</span>
                            <span><?php esc_html_e( 'Audited Code & Tokens', 'digital-marketplace' ); ?></span>
                        </div>
                        <div class="perk-item">
                            <span class="icon">✓</span>
                            <span><?php esc_html_e( 'Instant ZIP & Repo Access', 'digital-marketplace' ); ?></span>
                        </div>
                        <div class="perk-item">
                            <span class="icon">✓</span>
                            <span><?php esc_html_e( 'Perpetual Commercial Rights', 'digital-marketplace' ); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Right: Bold Visual Moment - Architectural Asset Dossier Card -->
                <div class="hero-blueprint-col" aria-hidden="true">
                    <div class="hero-blueprint-card">
                        <div class="blueprint-card-header">
                            <div class="blueprint-window-dots">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="blueprint-file-tab">
                                <span class="tab-dot"></span>
                                <span class="tab-filename">apex-system-v2.4.zip</span>
                            </div>
                            <span class="blueprint-badge-verified">Verified 100%</span>
                        </div>

                        <div class="blueprint-meta-bar">
                            <div class="blueprint-meta-stat">
                                <span class="meta-label">Package Size</span>
                                <span class="meta-val">48.6 MB</span>
                            </div>
                            <div class="blueprint-meta-stat">
                                <span class="meta-label">Components</span>
                                <span class="meta-val">1,420+</span>
                            </div>
                            <div class="blueprint-meta-stat">
                                <span class="meta-label">Target Runtime</span>
                                <span class="meta-val">React 19 / TS 5.4</span>
                            </div>
                        </div>

                        <div class="blueprint-code-preview">
                            <div class="code-line"><span class="code-comment">// Verified Production Stack Manifest</span></div>
                            <div class="code-line"><span class="code-kw">import</span> { Button, Dialog, DataTable } <span class="code-kw">from</span> <span class="code-str">'@apex/core'</span>;</div>
                            <div class="code-line"><span class="code-kw">export default function</span> <span class="code-fn">AppShell</span>({ children }: LayoutProps) {</div>
                            <div class="code-line code-indent"><span class="code-kw">return</span> &lt;<span class="code-tag">ThemeProvider</span> tokens={systemTokens}&gt;{children}&lt;/<span class="code-tag">ThemeProvider</span>&gt;;</div>
                            <div class="code-line">}</div>
                        </div>

                        <div class="blueprint-specs-checklist">
                            <div class="blueprint-spec-item">
                                <span class="spec-check">✓</span>
                                <span class="spec-text">WCAG 2.1 AAA Accessibility Tree Validated</span>
                            </div>
                            <div class="blueprint-spec-item">
                                <span class="spec-check">✓</span>
                                <span class="spec-text">Zero External Runtime Dependencies</span>
                            </div>
                            <div class="blueprint-spec-item">
                                <span class="spec-check">✓</span>
                                <span class="spec-text">Figma Auto-Layout 5.0 + Variables Synced</span>
                            </div>
                        </div>

                        <div class="blueprint-card-footer">
                            <div class="blueprint-price-tag">
                                <span class="price-val">$59.00</span>
                                <span class="price-type">Commercial Tier</span>
                            </div>
                            <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="blueprint-cta-btn">
                                <span>Inspect Live Asset</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Categories Section: Disciplined Technical Directory -->
    <section id="categories-section" class="categories-section">
        <div class="site-container">
            <div class="section-header">
                <div>
                    <span class="section-kicker"><?php esc_html_e( 'Asset Taxonomy', 'digital-marketplace' ); ?></span>
                    <h2 class="section-title"><?php esc_html_e( 'Explore by Category', 'digital-marketplace' ); ?></h2>
                    <p class="section-sub"><?php esc_html_e( 'Organized directories of software components, UI frameworks, and creative utilities.', 'digital-marketplace' ); ?></p>
                </div>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="section-more-link">
                    <span><?php esc_html_e( 'View All Categories', 'digital-marketplace' ); ?></span>
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="categories-grid">
                <?php
                $terms = get_terms( array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                    'number'     => 6,
                ) );

                $default_icons = array(
                    'ui'        => '🎨',
                    'design'    => '🎨',
                    'dev'       => '💻',
                    'code'      => '💻',
                    'font'      => '🔤',
                    'type'      => '🔤',
                    'audio'     => '🎵',
                    'sound'     => '🎵',
                    '3d'        => '🧊',
                    'icon'      => '🧊',
                    'template'  => '📈',
                    'product'   => '📦',
                );

                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
                    foreach ( $terms as $term ) :
                        $icon = '📦';
                        foreach ( $default_icons as $keyword => $emoji ) {
                            if ( stripos( $term->slug, $keyword ) !== false || stripos( $term->name, $keyword ) !== false ) {
                                $icon = $emoji;
                                break;
                            }
                        }
                        $count_text = sprintf(
                            /* translators: %d: number of products */
                            _n( '%d asset', '%d assets', $term->count, 'digital-marketplace' ),
                            $term->count
                        );
                        ?>
                        <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="category-card">
                            <div class="category-card-icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></div>
                            <div class="category-card-body">
                                <span class="category-card-title"><?php echo esc_html( $term->name ); ?></span>
                                <span class="category-card-count"><?php echo esc_html( $count_text ); ?></span>
                            </div>
                            <span class="category-card-arrow" aria-hidden="true">→</span>
                        </a>
                    <?php endforeach;
                else : 
                    // Clean starter layout when taxonomy terms have not yet been seeded
                    $starter_categories = array(
                        array( 'name' => __( 'UI & Design Kits', 'digital-marketplace' ), 'icon' => '🎨', 'slug' => 'ui-design-kits', 'count' => '24 items' ),
                        array( 'name' => __( 'Developer Boilerplates', 'digital-marketplace' ), 'icon' => '💻', 'slug' => 'developer-boilerplates', 'count' => '18 items' ),
                        array( 'name' => __( 'Fonts & Typography', 'digital-marketplace' ), 'icon' => '🔤', 'slug' => 'fonts-typography', 'count' => '12 items' ),
                        array( 'name' => __( 'Audio & SFX Packs', 'digital-marketplace' ), 'icon' => '🎵', 'slug' => 'audio-sfx-packs', 'count' => '9 items' ),
                        array( 'name' => __( '3D Assets & Icons', 'digital-marketplace' ), 'icon' => '🧊', 'slug' => '3d-assets-icons', 'count' => '15 items' ),
                        array( 'name' => __( 'Productivity Templates', 'digital-marketplace' ), 'icon' => '📈', 'slug' => 'productivity-templates', 'count' => '14 items' ),
                    );
                    foreach ( $starter_categories as $cat ) :
                        $cat_url = add_query_arg( 'product_cat', $cat['slug'], home_url( '/products' ) );
                    ?>
                        <a href="<?php echo esc_url( $cat_url ); ?>" class="category-card">
                            <div class="category-card-icon" aria-hidden="true"><?php echo esc_html( $cat['icon'] ); ?></div>
                            <div class="category-card-body">
                                <span class="category-card-title"><?php echo esc_html( $cat['name'] ); ?></span>
                                <span class="category-card-count"><?php echo esc_html( $cat['count'] ); ?></span>
                            </div>
                            <span class="category-card-arrow" aria-hidden="true">→</span>
                        </a>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products Dynamic Query -->
    <section id="featured-products-section" class="featured-products-section">
        <div class="site-container">
            <div class="section-header">
                <div>
                    <span class="section-kicker"><?php esc_html_e( 'Curated Drops', 'digital-marketplace' ); ?></span>
                    <h2 class="section-title"><?php esc_html_e( 'Featured Digital Assets', 'digital-marketplace' ); ?></h2>
                    <p class="section-sub"><?php esc_html_e( 'Audited templates and UI systems with instant download tokens and commercial rights.', 'digital-marketplace' ); ?></p>
                </div>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-sm">
                    <?php esc_html_e( 'Browse All Assets', 'digital-marketplace' ); ?>
                </a>
            </div>

            <div class="products-grid">
                <?php
                // Dynamic WordPress query for products marked as featured
                $featured_query = new WP_Query( array(
                    'post_type'      => 'product',
                    'posts_per_page' => 4,
                    'meta_query'     => array(
                        'relation' => 'OR',
                        array(
                            'key'     => '_product_is_featured',
                            'value'   => '1',
                            'compare' => '=',
                        ),
                        array(
                            'key'     => '_featured', // WooCommerce compatibility
                            'value'   => 'yes',
                            'compare' => '=',
                        ),
                    ),
                ) );

                if ( ! $featured_query->have_posts() ) {
                    // Fallback to any recent products if none explicitly marked featured
                    $featured_query = new WP_Query( array(
                        'post_type'      => 'product',
                        'posts_per_page' => 4,
                    ) );
                }

                if ( $featured_query->have_posts() ) :
                    while ( $featured_query->have_posts() ) : $featured_query->the_post();
                        $price          = digital_marketplace_get_price( get_the_ID() );
                        $original_price = get_post_meta( get_the_ID(), '_product_original_price', true );
                        $rating         = get_post_meta( get_the_ID(), '_product_rating', true );
                        $review_count   = get_post_meta( get_the_ID(), '_product_review_count', true );
                        $file_format    = get_post_meta( get_the_ID(), '_product_file_format', true );
                        $terms          = get_the_terms( get_the_ID(), 'product_cat' );
                        $category_name  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Digital Asset';
                        ?>
                        <article id="product-card-<?php the_ID(); ?>" class="product-card">
                            <a href="<?php the_permalink(); ?>" class="product-card-thumb">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'marketplace-card' ); ?>
                                <?php else : ?>
                                    <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=800&q=80" alt="<?php the_title_attribute(); ?>" />
                                <?php endif; ?>
                                <span class="product-badge-cat"><?php echo esc_html( $category_name ); ?></span>
                                <span class="product-badge-featured"><?php esc_html_e( 'Verified', 'digital-marketplace' ); ?></span>
                            </a>
                            <div class="product-card-body">
                                <div class="product-card-meta">
                                    <span class="product-author-tag"><?php esc_html_e( 'By', 'digital-marketplace' ); ?> <?php the_author(); ?></span>
                                    <?php if ( $rating ) : ?>
                                        <div class="product-card-rating">
                                            <span class="star-rating">★ <?php echo esc_html( $rating ); ?></span>
                                            <?php if ( $review_count ) : ?>
                                                <span class="rating-count">(<?php echo esc_html( $review_count ); ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="product-card-rating">
                                            <span class="verified-tag">✓ <?php esc_html_e( 'Audited', 'digital-marketplace' ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <h3 class="product-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <p class="product-card-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 14 ) ); ?></p>

                                <div class="product-card-footer">
                                    <div class="product-price-box">
                                        <span class="product-price">$<?php echo esc_html( number_format( floatval( $price ), 2 ) ); ?></span>
                                        <?php if ( $original_price ) : ?>
                                            <span class="product-price-orig">$<?php echo esc_html( number_format( floatval( $original_price ), 2 ) ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                        <?php esc_html_e( 'Inspect Asset →', 'digital-marketplace' ); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <!-- Clean starter card placeholder if database is freshly initialized -->
                    <div class="products-empty-state">
                        <h4><?php esc_html_e( 'Asset Catalog Ready', 'digital-marketplace' ); ?></h4>
                        <p>
                            <?php esc_html_e( 'Create your first product in WordPress Admin > Products to populate this section dynamically.', 'digital-marketplace' ); ?>
                        </p>
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="btn btn-primary btn-sm">
                            <?php esc_html_e( 'Add Product in Admin', 'digital-marketplace' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Engineering & Quality Standards Matrix (Disciplined replacement for generic banner) -->
    <section id="trust-banner-section" class="standards-matrix-section">
        <div class="site-container">
            <div class="standards-matrix-header">
                <span class="section-kicker"><?php esc_html_e( 'Verification Guarantee', 'digital-marketplace' ); ?></span>
                <h2 class="standards-matrix-title"><?php esc_html_e( 'Studio Quality Standards for Production Builds', 'digital-marketplace' ); ?></h2>
                <p class="standards-matrix-sub"><?php esc_html_e( 'Every item in the marketplace is held to strict engineering criteria before listing.', 'digital-marketplace' ); ?></p>
            </div>

            <div class="standards-grid">
                <div class="standard-card">
                    <div class="standard-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                    </div>
                    <h3 class="standard-title"><?php esc_html_e( 'Clean Code Audit', 'digital-marketplace' ); ?></h3>
                    <p class="standard-desc"><?php esc_html_e( 'Zero spaghetti code or undocumented props. All boilerplates feature semantic file hierarchies, linting rules, and strict typing.', 'digital-marketplace' ); ?></p>
                </div>

                <div class="standard-card">
                    <div class="standard-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </div>
                    <h3 class="standard-title"><?php esc_html_e( 'Instant Delivery Tokens', 'digital-marketplace' ); ?></h3>
                    <p class="standard-desc"><?php esc_html_e( 'No waiting for email receipts. Tokenized download packages and private repo links are issued in real-time upon cryptographic confirmation.', 'digital-marketplace' ); ?></p>
                </div>

                <div class="standard-card">
                    <div class="standard-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3 class="standard-title"><?php esc_html_e( 'Commercial Project License', 'digital-marketplace' ); ?></h3>
                    <p class="standard-desc"><?php esc_html_e( 'Build unlimited personal and client projects without recurring seat fees or distribution royalties.', 'digital-marketplace' ); ?></p>
                </div>

                <div class="standard-card">
                    <div class="standard-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                        </svg>
                    </div>
                    <h3 class="standard-title"><?php esc_html_e( 'Lifetime Version Upgrades', 'digital-marketplace' ); ?></h3>
                    <p class="standard-desc"><?php esc_html_e( 'Re-download newer releases directly from your account dashboard as frameworks and design tools evolve.', 'digital-marketplace' ); ?></p>
                </div>
            </div>

            <div class="standards-action-wrap">
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-primary btn-lg">
                    <?php esc_html_e( 'Explore All Digital Assets →', 'digital-marketplace' ); ?>
                </a>
            </div>
        </div>
    </section>

</div>

<?php get_footer(); ?>

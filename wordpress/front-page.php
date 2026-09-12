<?php
/**
 * The template for displaying the Front Page (Home)
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="home-page-container">

    <!-- Hero Section -->
    <section id="hero-section" class="hero-section">
        <div class="site-container">
            <div class="hero-pill">
                <span>✨</span>
                <span><?php esc_html_e( 'Curated Digital Marketplace for Creators & Engineers', 'digital-marketplace' ); ?></span>
            </div>

            <h1 class="hero-title">
                <?php esc_html_e( 'Craft better products with premium digital assets.', 'digital-marketplace' ); ?>
            </h1>

            <p class="hero-sub">
                <?php esc_html_e( 'Browse world-class UI design systems, full-stack boilerplates, high-fidelity 3D packs, and productivity templates with instant delivery and commercial licenses.', 'digital-marketplace' ); ?>
            </p>

            <form role="search" method="get" class="hero-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span style="color: var(--text-muted); font-size: 1rem; margin-right: 0.5rem;">🔍</span>
                <input 
                    type="search" 
                    name="s" 
                    placeholder="<?php esc_attr_e( 'Search templates, fonts, design kits, boilerplates...', 'digital-marketplace' ); ?>" 
                />
                <input type="hidden" name="post_type" value="product" />
                <button type="submit" class="hero-search-btn">
                    <?php esc_html_e( 'Find Assets', 'digital-marketplace' ); ?>
                </button>
            </form>

            <div class="hero-perks">
                <div class="perk-item">
                    <span class="icon">✓</span>
                    <span><?php esc_html_e( 'Verified Code & Files', 'digital-marketplace' ); ?></span>
                </div>
                <div class="perk-item">
                    <span class="icon">✓</span>
                    <span><?php esc_html_e( 'Instant Digital Downloads', 'digital-marketplace' ); ?></span>
                </div>
                <div class="perk-item">
                    <span class="icon">✓</span>
                    <span><?php esc_html_e( 'Commercial Project Rights', 'digital-marketplace' ); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories-section" style="padding: 3.5rem 0 1rem;">
        <div class="site-container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e( 'Explore Categories', 'digital-marketplace' ); ?></h2>
                    <p class="section-sub"><?php esc_html_e( 'Find exactly what you need for your next build', 'digital-marketplace' ); ?></p>
                </div>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" style="font-size: 0.85rem; font-weight: 700; color: var(--text-main);">
                    <?php esc_html_e( 'View All →', 'digital-marketplace' ); ?>
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
                            <div class="category-card-icon"><?php echo esc_html( $icon ); ?></div>
                            <span class="category-card-title"><?php echo esc_html( $term->name ); ?></span>
                            <span class="category-card-count"><?php echo esc_html( $count_text ); ?></span>
                        </a>
                    <?php endforeach;
                else : 
                    // Clean starter layout when taxonomy terms have not yet been seeded
                    $starter_categories = array(
                        array( 'name' => __( 'UI & Design Kits', 'digital-marketplace' ), 'icon' => '🎨', 'slug' => 'ui-design-kits' ),
                        array( 'name' => __( 'Developer Boilerplates', 'digital-marketplace' ), 'icon' => '💻', 'slug' => 'developer-boilerplates' ),
                        array( 'name' => __( 'Fonts & Typography', 'digital-marketplace' ), 'icon' => '🔤', 'slug' => 'fonts-typography' ),
                        array( 'name' => __( 'Audio & SFX Packs', 'digital-marketplace' ), 'icon' => '🎵', 'slug' => 'audio-sfx-packs' ),
                        array( 'name' => __( '3D Assets & Icons', 'digital-marketplace' ), 'icon' => '🧊', 'slug' => '3d-assets-icons' ),
                        array( 'name' => __( 'Productivity Templates', 'digital-marketplace' ), 'icon' => '📈', 'slug' => 'productivity-templates' ),
                    );
                    foreach ( $starter_categories as $cat ) :
                        $cat_url = add_query_arg( 'product_cat', $cat['slug'], home_url( '/products' ) );
                    ?>
                        <a href="<?php echo esc_url( $cat_url ); ?>" class="category-card">
                            <div class="category-card-icon"><?php echo esc_html( $cat['icon'] ); ?></div>
                            <span class="category-card-title"><?php echo esc_html( $cat['name'] ); ?></span>
                            <span class="category-card-count"><?php esc_html_e( 'Explore category', 'digital-marketplace' ); ?></span>
                        </a>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products Dynamic Query -->
    <section id="featured-products-section" style="padding: 2rem 0;">
        <div class="site-container">
            <div class="section-header">
                <div>
                    <span style="display: inline-block; padding: 0.2rem 0.6rem; background-color: var(--color-accent-bg); color: var(--color-accent-dark); font-size: 0.725rem; font-weight: 700; border-radius: var(--radius-sm); margin-bottom: 0.35rem;">
                        <?php esc_html_e( 'Staff Picks', 'digital-marketplace' ); ?>
                    </span>
                    <h2 class="section-title"><?php esc_html_e( 'Featured Products', 'digital-marketplace' ); ?></h2>
                    <p class="section-sub"><?php esc_html_e( 'Top-rated digital tools loved by creators worldwide', 'digital-marketplace' ); ?></p>
                </div>
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-secondary btn-sm">
                    <?php esc_html_e( 'Browse Full Catalog', 'digital-marketplace' ); ?>
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
                                <span class="product-badge-featured"><?php esc_html_e( 'Featured', 'digital-marketplace' ); ?></span>
                            </a>
                            <div class="product-card-body">
                                <div class="product-card-meta">
                                    <span><?php esc_html_e( 'By', 'digital-marketplace' ); ?> <?php the_author(); ?></span>
                                    <?php if ( $rating ) : ?>
                                        <div class="product-card-rating">
                                            <span>★ <?php echo esc_html( $rating ); ?></span>
                                            <?php if ( $review_count ) : ?>
                                                <span style="color: var(--text-light);">(<?php echo esc_html( $review_count ); ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="product-card-rating">
                                            <span style="color: #059669; font-size: 0.75rem; font-weight: 600;">✓ <?php esc_html_e( 'Verified', 'digital-marketplace' ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <h3 class="product-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="product-card-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 14 ) ); ?></p>
                                <div class="product-card-footer">
                                    <div class="product-price-box">
                                        <span class="product-price">$<?php echo esc_html( $price ); ?></span>
                                        <?php if ( $original_price ) : ?>
                                            <span class="product-price-orig">$<?php echo esc_html( $original_price ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                        <?php esc_html_e( 'Details →', 'digital-marketplace' ); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <!-- Sample Card Placeholder if database is freshly initialized -->
                    <div style="grid-column: 1 / -1; padding: 2.5rem; background: #fff; border-radius: var(--radius-lg); border: 1px dashed var(--border-subtle); text-align: center;">
                        <h4 style="font-weight: 700; margin-bottom: 0.5rem;"><?php esc_html_e( 'Ready for your Products', 'digital-marketplace' ); ?></h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                            <?php esc_html_e( 'Create your first product in WordPress Admin > Products > Add New to have it appear dynamically here.', 'digital-marketplace' ); ?>
                        </p>
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="btn btn-primary btn-sm">
                            <?php esc_html_e( 'Add First Product in WP Admin', 'digital-marketplace' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Creator Trust Banner -->
    <div class="site-container">
        <section id="trust-banner-section" class="trust-banner">
            <span style="display: inline-block; padding: 0.25rem 0.75rem; background-color: rgba(245, 158, 11, 0.2); color: #fbbf24; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">
                <?php esc_html_e( 'Creator Marketplace Guarantee', 'digital-marketplace' ); ?>
            </span>
            <h2><?php esc_html_e( 'Built by developers & designers, for developers & designers.', 'digital-marketplace' ); ?></h2>
            <p><?php esc_html_e( 'Never start from a blank canvas again. Every asset is strictly audited for clean structure, standard naming conventions, and instant production integration.', 'digital-marketplace' ); ?></p>
            <div class="trust-banner-actions">
                <a href="<?php echo esc_url( home_url( '/products' ) ); ?>" class="btn btn-accent btn-lg">
                    <?php esc_html_e( 'Explore All Products', 'digital-marketplace' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/login' ) ); ?>" class="btn btn-secondary btn-lg" style="background-color: #292524; color: #fff; border-color: #44403c;">
                    <?php esc_html_e( 'Join as Customer', 'digital-marketplace' ); ?>
                </a>
            </div>
        </section>
    </div>

</div>

<?php get_footer(); ?>

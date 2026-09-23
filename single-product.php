<?php
/**
 * The template for displaying a single product
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $product_id     = get_the_ID();
    $price          = digital_marketplace_get_price( $product_id );
    $original_price = get_post_meta( $product_id, '_product_original_price', true );
    $rating         = get_post_meta( $product_id, '_product_rating', true );
    $review_count   = get_post_meta( $product_id, '_product_review_count', true );
    $file_format    = get_post_meta( $product_id, '_product_file_format', true );
    $file_size      = get_post_meta( $product_id, '_product_file_size', true );
    $raw_features   = get_post_meta( $product_id, '_product_features', true );
    
    // Parse features from lines
    $features = array();
    if ( ! empty( $raw_features ) ) {
        $features = array_filter( array_map( 'trim', explode( "\n", $raw_features ) ) );
    }
    if ( empty( $features ) ) {
        $features = array(
            __( 'Instant digital delivery with persistent account access', 'digital-marketplace' ),
            __( 'Full commercial license for unlimited client and personal builds', 'digital-marketplace' ),
            __( 'Organized folders, well-documented components, and clean design tokens', 'digital-marketplace' ),
            __( 'Free version upgrades and future framework updates included', 'digital-marketplace' ),
        );
    }

    $terms         = get_the_terms( $product_id, 'product_cat' );
    $cat_name      = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : __( 'Digital Asset', 'digital-marketplace' );
    $cat_link      = ( $terms && ! is_wp_error( $terms ) ) ? get_term_link( $terms[0] ) : home_url( '/products' );
    $cart_page_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart' );
    $checkout_url  = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout' );
?>

<div id="product-single-view" class="product-single-container">
    <div class="site-container">

        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb-bar" aria-label="<?php esc_attr_e( 'Breadcrumb', 'digital-marketplace' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumb-link"><?php esc_html_e( 'Home', 'digital-marketplace' ); ?></a>
            <span class="breadcrumb-separator" aria-hidden="true">/</span>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>" class="breadcrumb-link"><?php esc_html_e( 'Catalog', 'digital-marketplace' ); ?></a>
            <span class="breadcrumb-separator" aria-hidden="true">/</span>
            <a href="<?php echo esc_url( $cat_link ); ?>" class="breadcrumb-link"><?php echo esc_html( $cat_name ); ?></a>
            <span class="breadcrumb-separator" aria-hidden="true">/</span>
            <span class="breadcrumb-current"><?php the_title(); ?></span>
        </nav>

        <!-- Product Analytics Tracking Data for Recently Viewed -->
        <div id="product-page-analytics" style="display:none;"
            data-id="<?php echo esc_attr( $product_id ); ?>"
            data-title="<?php echo esc_attr( get_the_title() ); ?>"
            data-url="<?php echo esc_url( get_permalink() ); ?>"
            data-price="<?php echo esc_attr( number_format( (float) $price, 2 ) ); ?>"
            data-orig-price="<?php echo esc_attr( $original_price ? number_format( (float) $original_price, 2 ) : '' ); ?>"
            data-category="<?php echo esc_attr( $cat_name ); ?>"
            data-rating="<?php echo esc_attr( $rating ?: '4.9' ); ?>"
            data-format="<?php echo esc_attr( $file_format ?: 'Instant access' ); ?>"
            data-thumb="<?php echo esc_url( has_post_thumbnail() ? get_the_post_thumbnail_url( $product_id, 'marketplace-card' ) : '' ); ?>"
        ></div>

        <!-- Product Presentation Layout -->
        <div class="product-single-layout">
            
            <!-- Left: Gallery Showcase & Documentation -->
            <div class="product-gallery-column">
                <div class="product-gallery-main">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'marketplace-gallery', array( 'id' => 'main-gallery-image' ) ); ?>
                    <?php else : ?>
                        <img id="main-gallery-image" src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=1200&q=80" alt="<?php the_title_attribute(); ?>" />
                    <?php endif; ?>
                </div>

                <!-- Gallery Thumbnails Strip -->
                <div class="product-thumbs-strip">
                    <?php
                    $thumb_src = has_post_thumbnail() ? get_the_post_thumbnail_url( $product_id, 'marketplace-gallery' ) : 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=1200&q=80';
                    ?>
                    <button type="button" class="product-thumb-item active" data-full-image="<?php echo esc_url( $thumb_src ); ?>" aria-label="<?php esc_attr_e( 'Preview 1', 'digital-marketplace' ); ?>">
                        <img src="<?php echo esc_url( $thumb_src ); ?>" alt="<?php the_title_attribute(); ?>" />
                    </button>
                    <!-- Alternate angle mock thumbs for interactive gallery -->
                    <button type="button" class="product-thumb-item" data-full-image="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80" aria-label="<?php esc_attr_e( 'Preview 2', 'digital-marketplace' ); ?>">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=200&q=80" alt="Preview 2" />
                    </button>
                    <button type="button" class="product-thumb-item" data-full-image="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80" aria-label="<?php esc_attr_e( 'Preview 3', 'digital-marketplace' ); ?>">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80" alt="Preview 3" />
                    </button>
                </div>

                <!-- Product Full Description / Documentation -->
                <div class="product-overview-panel">
                    <div class="overview-header">
                        <span class="section-kicker"><?php esc_html_e( 'Architecture & Spec', 'digital-marketplace' ); ?></span>
                        <h2 class="overview-title">
                            <?php esc_html_e( 'Asset Overview & Documentation', 'digital-marketplace' ); ?>
                        </h2>
                    </div>
                    <div class="overview-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- Right: Purchasing Box & Specs -->
            <div class="product-sidebar-column">
                <div class="product-specs-box">
                    <div class="product-header-tags">
                        <span class="product-badge-cat">
                            <?php echo esc_html( $cat_name ); ?>
                        </span>
                        <span class="product-badge-verified">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <?php esc_html_e( 'Audited Code', 'digital-marketplace' ); ?>
                        </span>
                    </div>

                    <h1 class="product-detail-title">
                        <?php the_title(); ?>
                    </h1>

                    <div class="product-rating-meta">
                        <div class="star-rating-chip">
                            <span class="star-icon">★</span>
                            <span class="rating-num"><?php echo esc_html( $rating ? $rating : '4.9' ); ?></span>
                        </div>
                        <span class="meta-dot" aria-hidden="true">•</span>
                        <span class="review-count-text"><?php echo esc_html( $review_count ? $review_count : '84' ); ?> <?php esc_html_e( 'verified licenses deployed', 'digital-marketplace' ); ?></span>
                    </div>

                    <div class="product-single-price">
                        <span class="price-current">$<?php echo esc_html( number_format( floatval( $price ), 2 ) ); ?></span>
                        <?php if ( $original_price ) : ?>
                            <span class="price-original">
                                $<?php echo esc_html( number_format( floatval( $original_price ), 2 ) ); ?>
                            </span>
                            <span class="price-discount-pill">
                                <?php
                                    $orig_val = floatval( $original_price );
                                    $curr_val = floatval( $price );
                                    $savings_pct = ( $orig_val > $curr_val && $orig_val > 0 ) ? round( ( ( $orig_val - $curr_val ) / $orig_val ) * 100 ) : 0;
                                    echo '-' . esc_html( $savings_pct ) . '%';
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Quantity Stepper -->
                    <div class="license-qty-section">
                        <div class="qty-label-row">
                            <label class="qty-label" for="license-qty-input">
                                <?php esc_html_e( 'License Seats / Quantity', 'digital-marketplace' ); ?>
                            </label>
                            <span class="qty-hint"><?php esc_html_e( 'Single seat per team member', 'digital-marketplace' ); ?></span>
                        </div>
                        <div class="qty-stepper">
                            <button type="button" class="qty-btn qty-btn-minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'digital-marketplace' ); ?>">−</button>
                            <span class="qty-val">1</span>
                            <button type="button" class="qty-btn qty-btn-plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'digital-marketplace' ); ?>">+</button>
                        </div>
                    </div>

                    <!-- Action Buttons with DMC AJAX Hooks -->
                    <div class="product-actions-block">
                        <button type="button" class="btn btn-primary btn-block btn-lg dmc-add-to-cart-btn" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                            <span><?php esc_html_e( 'Add to Cart', 'digital-marketplace' ); ?></span>
                        </button>
                        <button type="button" class="btn btn-accent btn-block btn-lg dmc-buy-now-btn" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                            <span><?php esc_html_e( 'Instant Buy with Crypto', 'digital-marketplace' ); ?></span>
                        </button>
                    </div>

                    <!-- Verified Features Checklist -->
                    <div class="included-manifest-section">
                        <h4 class="manifest-heading">
                            <?php esc_html_e( 'What is Included in Download Package', 'digital-marketplace' ); ?>
                        </h4>
                        <ul class="product-features-list">
                            <?php foreach ( $features as $feat ) : ?>
                                <li>
                                    <span class="check-icon" aria-hidden="true">✓</span>
                                    <span><?php echo esc_html( $feat ); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Technical Specifications -->
                    <div class="specs-grid">
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'Archive Format', 'digital-marketplace' ); ?></span>
                            <span class="spec-value spec-mono"><?php echo esc_html( $file_format ? $file_format : 'ZIP / Multi-Source' ); ?></span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'Package Size', 'digital-marketplace' ); ?></span>
                            <span class="spec-value spec-mono"><?php echo esc_html( $file_size ? $file_size : '52.4 MB' ); ?></span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'Commercial Rights', 'digital-marketplace' ); ?></span>
                            <span class="spec-value spec-success"><?php esc_html_e( 'Perpetual Commercial License', 'digital-marketplace' ); ?></span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'Fulfillment Method', 'digital-marketplace' ); ?></span>
                            <span class="spec-value"><?php esc_html_e( 'Direct ZIP & Repo Token', 'digital-marketplace' ); ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Related Products Section -->
        <?php
        $related_args = array(
            'post_type'      => 'product',
            'posts_per_page' => 3,
            'post__not_in'   => array( $product_id ),
        );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $related_args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $terms[0]->term_id,
                ),
            );
        }
        $related_query = new WP_Query( $related_args );

        if ( $related_query->have_posts() ) : ?>
            <div class="related-products-section">
                <div class="section-header">
                    <div>
                        <span class="section-kicker"><?php esc_html_e( 'Complementary Tools', 'digital-marketplace' ); ?></span>
                        <h3 class="section-title"><?php esc_html_e( 'Related Production Assets', 'digital-marketplace' ); ?></h3>
                    </div>
                </div>
                <div class="products-grid">
                    <?php while ( $related_query->have_posts() ) : $related_query->the_post();
                        $rel_price = digital_marketplace_get_price( get_the_ID() );
                    ?>
                        <article class="product-card">
                            <a href="<?php the_permalink(); ?>" class="product-card-thumb">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'marketplace-card' ); ?>
                                <?php else : ?>
                                    <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=800&q=80" alt="<?php the_title_attribute(); ?>" />
                                <?php endif; ?>
                            </a>
                            <div class="product-card-body">
                                <h4 class="product-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                <div class="product-card-footer">
                                    <span class="product-price">$<?php echo esc_html( number_format( floatval( $rel_price ), 2 ) ); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-secondary btn-sm"><?php esc_html_e( 'Inspect Asset →', 'digital-marketplace' ); ?></a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>

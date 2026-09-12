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
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'digital-marketplace' ); ?></a>
            <span>/</span>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>"><?php esc_html_e( 'Products', 'digital-marketplace' ); ?></a>
            <span>/</span>
            <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
            <span>/</span>
            <span style="color: var(--text-main); font-weight: 600;"><?php the_title(); ?></span>
        </nav>

        <!-- Product Presentation Layout -->
        <div class="product-single-layout">
            
            <!-- Left: Gallery Showcase -->
            <div>
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
                    <button class="product-thumb-item active" data-full-image="<?php echo esc_url( $thumb_src ); ?>">
                        <img src="<?php echo esc_url( $thumb_src ); ?>" alt="<?php the_title_attribute(); ?>" />
                    </button>
                    <!-- Alternate angle mock thumbs for interactive gallery -->
                    <button class="product-thumb-item" data-full-image="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=200&q=80" alt="Preview 2" />
                    </button>
                    <button class="product-thumb-item" data-full-image="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80" alt="Preview 3" />
                    </button>
                </div>

                <!-- Product Full Description -->
                <div style="background: #fff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 2rem; margin-top: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1rem; color: var(--text-main);">
                        <?php esc_html_e( 'Product Overview & Specifications', 'digital-marketplace' ); ?>
                    </h3>
                    <div style="font-size: 0.925rem; color: var(--text-muted); line-height: 1.7;">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- Right: Purchasing Box & Specs -->
            <div>
                <div class="product-specs-box">
                    <span class="product-badge-cat" style="position: static; display: inline-block; margin-bottom: 0.75rem;">
                        <?php echo esc_html( $cat_name ); ?>
                    </span>

                    <h1 style="font-size: 1.75rem; font-weight: 900; line-height: 1.2; letter-spacing: -0.02em; color: var(--text-main);">
                        <?php the_title(); ?>
                    </h1>

                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; font-size: 0.85rem;">
                        <span style="color: var(--color-accent-dark); font-weight: 700;">★ <?php echo esc_html( $rating ? $rating : '4.9' ); ?></span>
                        <span style="color: var(--text-light);">•</span>
                        <span style="color: var(--text-muted);"><?php echo esc_html( $review_count ? $review_count : '84' ); ?> <?php esc_html_e( 'verified customer reviews', 'digital-marketplace' ); ?></span>
                    </div>

                    <div class="product-single-price">
                        <span>$<?php echo esc_html( $price ); ?></span>
                        <?php if ( $original_price ) : ?>
                            <span style="font-size: 1.15rem; color: var(--text-light); text-decoration: line-through;">
                                $<?php echo esc_html( $original_price ); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Quantity Stepper -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.4rem;">
                            <?php esc_html_e( 'License Quantity:', 'digital-marketplace' ); ?>
                        </label>
                        <div class="qty-stepper">
                            <button type="button" class="qty-btn qty-btn-minus">-</button>
                            <span class="qty-val">1</span>
                            <button type="button" class="qty-btn qty-btn-plus">+</button>
                        </div>
                    </div>

                    <!-- Action Buttons with DMC AJAX Hooks -->
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <button type="button" class="btn btn-primary btn-block btn-lg dmc-add-to-cart-btn" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                            🛍️ <?php esc_html_e( 'Add to Cart', 'digital-marketplace' ); ?>
                        </button>
                        <button type="button" class="btn btn-accent btn-block btn-lg dmc-buy-now-btn" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                            ⚡ <?php esc_html_e( 'Buy Now', 'digital-marketplace' ); ?>
                        </button>
                    </div>

                    <!-- Verified Features Checklist -->
                    <h4 style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-muted);">
                        <?php esc_html_e( 'What is Included', 'digital-marketplace' ); ?>
                    </h4>
                    <ul class="product-features-list">
                        <?php foreach ( $features as $feat ) : ?>
                            <li>
                                <span class="check-icon">✓</span>
                                <span><?php echo esc_html( $feat ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Technical Specifications -->
                    <div class="specs-grid">
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'File Format', 'digital-marketplace' ); ?></span>
                            <span class="spec-value"><?php echo esc_html( $file_format ? $file_format : 'ZIP / Multi-Source' ); ?></span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'Download Size', 'digital-marketplace' ); ?></span>
                            <span class="spec-value"><?php echo esc_html( $file_size ? $file_size : '52 MB' ); ?></span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'Commercial Rights', 'digital-marketplace' ); ?></span>
                            <span class="spec-value" style="color: var(--color-success);"><?php esc_html_e( 'Standard Commercial License', 'digital-marketplace' ); ?></span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label"><?php esc_html_e( 'Delivery Format', 'digital-marketplace' ); ?></span>
                            <span class="spec-value"><?php esc_html_e( 'Direct Instant Download', 'digital-marketplace' ); ?></span>
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
            <div style="border-top: 1px solid var(--border-subtle); padding-top: 3rem; margin-top: 2rem;">
                <h3 class="section-title" style="margin-bottom: 1.5rem;">
                    <?php esc_html_e( 'You May Also Need', 'digital-marketplace' ); ?>
                </h3>
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
                                    <span class="product-price">$<?php echo esc_html( $rel_price ); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-secondary btn-sm"><?php esc_html_e( 'View', 'digital-marketplace' ); ?></a>
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

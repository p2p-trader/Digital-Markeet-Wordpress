<?php
/**
 * The template for displaying product listing / archive pages
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="product-archive-container" class="archive-main-container">
    <div class="site-container">

        <!-- Archive Header -->
        <div class="archive-header-block">
            <div class="archive-kicker-row">
                <span class="section-kicker"><?php esc_html_e( 'Catalog Directory', 'digital-marketplace' ); ?></span>
                <?php if ( is_tax( 'product_cat' ) ) : ?>
                    <span class="archive-scope-pill"><?php esc_html_e( 'Category Filtered', 'digital-marketplace' ); ?></span>
                <?php elseif ( is_search() ) : ?>
                    <span class="archive-scope-pill"><?php esc_html_e( 'Search Filtered', 'digital-marketplace' ); ?></span>
                <?php endif; ?>
            </div>
            <h1 class="archive-title">
                <?php
                if ( is_search() ) {
                    /* translators: %s: search query */
                    printf( esc_html__( 'Search: "%s"', 'digital-marketplace' ), esc_html( get_search_query() ) );
                } elseif ( is_tax( 'product_cat' ) ) {
                    single_term_title();
                } else {
                    esc_html_e( 'All Marketplace Assets', 'digital-marketplace' );
                }
                ?>
            </h1>
            <p class="archive-description">
                <?php
                if ( is_tax( 'product_cat' ) ) {
                    $desc = term_description();
                    echo esc_html( $desc ? wp_strip_all_tags( $desc ) : __( 'Curated production files, component systems, and toolkits in this directory.', 'digital-marketplace' ) );
                } else {
                    esc_html_e( 'Discover verified developer boilerplates, UI kits, design systems, and icon suites with instant digital delivery and commercial rights.', 'digital-marketplace' );
                }
                ?>
            </p>
        </div>

        <!-- Filter & Search Controls Bar -->
        <div class="archive-controls-bar">
            
            <!-- Category Filter Pills -->
            <div class="archive-category-chips">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>" class="category-chip <?php echo ( ! is_tax( 'product_cat' ) && ! is_search() ) ? 'is-active' : ''; ?>">
                    <?php esc_html_e( 'All Assets', 'digital-marketplace' ); ?>
                </a>
                <?php
                $terms = get_terms( array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                ) );
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
                    foreach ( $terms as $term ) :
                        $is_current = is_tax( 'product_cat', $term->term_id );
                        ?>
                        <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="category-chip <?php echo $is_current ? 'is-active' : ''; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach;
                endif; ?>
            </div>

            <!-- Search Field -->
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="archive-search-form">
                <div class="archive-search-input-wrap">
                    <span class="search-icon-mini" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input 
                        type="search" 
                        name="s" 
                        placeholder="<?php esc_attr_e( 'Filter current catalog...', 'digital-marketplace' ); ?>" 
                        value="<?php echo esc_attr( get_search_query() ); ?>"
                        class="archive-search-input"
                    />
                    <input type="hidden" name="post_type" value="product" />
                    <button type="submit" class="archive-search-submit-btn" aria-label="<?php esc_attr_e( 'Submit search', 'digital-marketplace' ); ?>">
                        <span>→</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Product Grid Loop -->
        <?php if ( have_posts() ) : ?>
            <div class="products-grid">
                <?php while ( have_posts() ) : the_post();
                    $price          = digital_marketplace_get_price( get_the_ID() );
                    $original_price = get_post_meta( get_the_ID(), '_product_original_price', true );
                    $rating         = get_post_meta( get_the_ID(), '_product_rating', true );
                    $review_count   = get_post_meta( get_the_ID(), '_product_review_count', true );
                    $file_format    = get_post_meta( get_the_ID(), '_product_file_format', true );
                    $terms          = get_the_terms( get_the_ID(), 'product_cat' );
                    $cat_name       = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Digital Asset';
                    ?>
                    <article id="archive-product-<?php the_ID(); ?>" class="product-card">
                        <a href="<?php the_permalink(); ?>" class="product-card-thumb">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'marketplace-card' ); ?>
                            <?php else : ?>
                                <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=800&q=80" alt="<?php the_title_attribute(); ?>" />
                            <?php endif; ?>
                            <span class="product-badge-cat"><?php echo esc_html( $cat_name ); ?></span>
                            <?php if ( $file_format ) : ?>
                                <span class="product-badge-featured"><?php echo esc_html( $file_format ); ?></span>
                            <?php else : ?>
                                <span class="product-badge-featured"><?php esc_html_e( 'Verified', 'digital-marketplace' ); ?></span>
                            <?php endif; ?>
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

                            <h2 class="product-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

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
                <?php endwhile; ?>
            </div>

            <!-- Standard WordPress Pagination -->
            <div class="archive-pagination-wrap">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => __( '← Previous', 'digital-marketplace' ),
                    'next_text' => __( 'Next →', 'digital-marketplace' ),
                ) );
                ?>
            </div>

        <?php else : ?>
            <div class="archive-empty-state">
                <div class="empty-state-icon" aria-hidden="true">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                </div>
                <h3 class="empty-state-title"><?php esc_html_e( 'No Matching Assets Found', 'digital-marketplace' ); ?></h3>
                <p class="empty-state-desc">
                    <?php esc_html_e( 'We could not find any digital assets matching your active filters. Clear search or explore our complete catalog.', 'digital-marketplace' ); ?>
                </p>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>" class="btn btn-primary">
                    <?php esc_html_e( 'Reset Filters & View All', 'digital-marketplace' ); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>

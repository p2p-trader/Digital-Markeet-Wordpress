<?php
get_header();
$products_url = get_post_type_archive_link( 'product' ) ?: home_url( '/products' );
global $wp_query;
$current_tax = is_tax( 'product_cat' ) ? get_queried_object() : null;
$current_tax_slug = $current_tax ? $current_tax->slug : 'all';
?>
<div class="archive-main-container">
    <div class="site-container">
        <div class="archive-header-block">
            <div class="archive-kicker-row">
                <span class="section-kicker"><?php esc_html_e( 'Marketplace catalog', 'digital-marketplace' ); ?></span>
                <?php if ( is_search() ) : ?>
                    <span class="archive-scope-pill"><?php esc_html_e( 'Search results', 'digital-marketplace' ); ?></span>
                <?php endif; ?>
            </div>
            <h1 class="archive-title" id="archive-heading-title">
                <?php
                if ( is_search() ) :
                    printf( esc_html__( 'Results for “%s”', 'digital-marketplace' ), esc_html( get_search_query() ) );
                elseif ( is_tax( 'product_cat' ) ) :
                    single_term_title();
                else :
                    esc_html_e( 'Explore premium digital products', 'digital-marketplace' );
                endif;
                ?>
            </h1>
            <p class="archive-description">
                <?php esc_html_e( 'Discover professionally crafted themes, plugins, templates, software and creative resources with instant digital delivery.', 'digital-marketplace' ); ?>
            </p>
        </div>

        <div class="archive-controls-bar">
            <div class="archive-category-chips" id="catalog-category-chips" role="tablist">
                <a class="category-chip <?php echo ( 'all' === $current_tax_slug && ! is_search() ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $products_url ); ?>" data-category-slug="all" role="tab">
                    <?php esc_html_e( 'All products', 'digital-marketplace' ); ?>
                </a>
                <?php
                $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $is_curr = ( $current_tax && $current_tax->term_id === $term->term_id ) ? 'is-active' : '';
                        echo '<a class="category-chip ' . esc_attr( $is_curr ) . '" href="' . esc_url( get_term_link( $term ) ) . '" data-category-slug="' . esc_attr( $term->slug ) . '" role="tab">' . esc_html( $term->name ) . '</a>';
                    }
                }
                ?>
            </div>

            <div class="archive-filters-right">
                <div class="catalog-sort-wrap">
                    <label for="catalog-sort-select" class="catalog-sort-label"><?php esc_html_e( 'Sort by:', 'digital-marketplace' ); ?></label>
                    <select id="catalog-sort-select" class="catalog-sort-select" aria-label="<?php esc_attr_e( 'Sort products', 'digital-marketplace' ); ?>">
                        <option value="newest"><?php esc_html_e( 'Newest first', 'digital-marketplace' ); ?></option>
                        <option value="popular"><?php esc_html_e( 'Most popular', 'digital-marketplace' ); ?></option>
                        <option value="rating"><?php esc_html_e( 'Highest rated', 'digital-marketplace' ); ?></option>
                        <option value="price_asc"><?php esc_html_e( 'Price: Low to high', 'digital-marketplace' ); ?></option>
                        <option value="price_desc"><?php esc_html_e( 'Price: High to low', 'digital-marketplace' ); ?></option>
                    </select>
                </div>

                <form class="archive-search-form" id="archive-catalog-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="archive-search-input-wrap">
                        <input class="archive-search-input" id="archive-search-input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Filter catalog…', 'digital-marketplace' ); ?>" autocomplete="off">
                        <input type="hidden" name="post_type" value="product">
                        <button class="archive-search-submit-btn" type="submit" aria-label="<?php esc_attr_e( 'Search', 'digital-marketplace' ); ?>">⌕</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="archive-status-row">
            <span class="catalog-count-badge" id="catalog-count-badge">
                <?php printf( esc_html__( 'Showing %d products', 'digital-marketplace' ), (int) $wp_query->found_posts ); ?>
            </span>
            <button type="button" class="catalog-clear-filters-btn" id="catalog-clear-filters-btn" style="display: none;">
                <?php esc_html_e( 'Clear active filters ×', 'digital-marketplace' ); ?>
            </button>
        </div>

        <div class="catalog-content-wrapper" id="catalog-content-wrapper">
            <div class="catalog-loading-overlay" id="catalog-loading-overlay" aria-hidden="true">
                <div class="catalog-spinner"></div>
            </div>

            <div id="catalog-results-target">
                <?php if ( have_posts() ) : ?>
                    <div class="products-grid premium-grid" id="catalog-products-grid">
                        <?php
                        while ( have_posts() ) :
                            the_post();
                            $id         = get_the_ID();
                            $price      = digital_marketplace_get_price( $id );
                            $orig       = get_post_meta( $id, '_product_original_price', true );
                            $rating     = get_post_meta( $id, '_product_rating', true ) ?: '4.9';
                            $reviews    = get_post_meta( $id, '_product_review_count', true ) ?: '85';
                            $terms      = get_the_terms( $id, 'product_cat' );
                            $cat        = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Digital Product';
                            $format     = get_post_meta( $id, '_product_file_format', true ) ?: 'Instant access';
                            ?>
                            <article class="market-card">
                                <a class="market-card-image" href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'marketplace-card' ); ?>
                                    <?php else : ?>
                                        <div class="image-placeholder">
                                            <span><?php echo esc_html( strtoupper( substr( get_the_title(), 0, 1 ) ) ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <span class="card-category"><?php echo esc_html( $cat ); ?></span>
                                    <button class="wishlist" type="button" aria-label="<?php esc_attr_e( 'Add to wishlist', 'digital-marketplace' ); ?>">♡</button>
                                </a>
                                <div class="market-card-body">
                                    <div class="card-meta">
                                        <span><?php echo esc_html( $format ); ?></span>
                                        <span>★ <?php echo esc_html( $rating ); ?> (<?php echo esc_html( $reviews ); ?>)</span>
                                    </div>
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 12 ) ); ?></p>
                                    <div class="card-buy">
                                        <div>
                                            <strong>$<?php echo esc_html( number_format( (float) $price, 2 ) ); ?></strong>
                                            <?php if ( $orig ) : ?>
                                                <del>$<?php echo esc_html( number_format( (float) $orig, 2 ) ); ?></del>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php the_permalink(); ?>"><?php esc_html_e( 'View product', 'digital-marketplace' ); ?></a>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <div class="archive-pagination-wrap">
                        <?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => '← Previous', 'next_text' => 'Next →' ) ); ?>
                    </div>
                <?php else : ?>
                    <div class="archive-empty-state">
                        <div class="empty-state-icon">⌕</div>
                        <h2 class="empty-state-title"><?php esc_html_e( 'No products found', 'digital-marketplace' ); ?></h2>
                        <p class="empty-state-desc"><?php esc_html_e( 'Try another search or browse the full catalog.', 'digital-marketplace' ); ?></p>
                        <a class="btn btn-primary" href="<?php echo esc_url( $products_url ); ?>"><?php esc_html_e( 'View all products', 'digital-marketplace' ); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>

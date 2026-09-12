<?php
/**
 * The template for displaying product listing / archive pages
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="product-archive-container" style="padding: 2.5rem 0 5rem;">
    <div class="site-container">

        <!-- Archive Header -->
        <div style="margin-bottom: 2rem;">
            <h1 class="section-title">
                <?php
                if ( is_search() ) {
                    /* translators: %s: search query */
                    printf( esc_html__( 'Search Results for: "%s"', 'digital-marketplace' ), esc_html( get_search_query() ) );
                } elseif ( is_tax( 'product_cat' ) ) {
                    single_term_title();
                } else {
                    esc_html_e( 'All Marketplace Products', 'digital-marketplace' );
                }
                ?>
            </h1>
            <p class="section-sub">
                <?php
                if ( is_tax( 'product_cat' ) ) {
                    echo esc_html( term_description() ? term_description() : __( 'Explore assets in this category.', 'digital-marketplace' ) );
                } else {
                    esc_html_e( 'Discover high quality templates, boilerplates, and creative assets with immediate digital fulfillment.', 'digital-marketplace' );
                }
                ?>
            </p>
        </div>

        <!-- Filter & Search Controls Bar -->
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center; background: #fff; padding: 1rem 1.25rem; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); margin-bottom: 2rem;">
            
            <!-- Category Filter Pills -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>" class="btn btn-sm <?php echo ! is_tax( 'product_cat' ) ? 'btn-primary' : 'btn-secondary'; ?>">
                    <?php esc_html_e( 'All Categories', 'digital-marketplace' ); ?>
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
                        <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="btn btn-sm <?php echo $is_current ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach;
                endif; ?>
            </div>

            <!-- Search Field -->
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; gap: 0.5rem;">
                <input 
                    type="search" 
                    name="s" 
                    placeholder="<?php esc_attr_e( 'Filter products...', 'digital-marketplace' ); ?>" 
                    value="<?php echo esc_attr( get_search_query() ); ?>"
                    class="form-control"
                    style="padding: 0.4rem 0.75rem; font-size: 0.825rem; min-width: 180px;"
                />
                <input type="hidden" name="post_type" value="product" />
                <button type="submit" class="btn btn-secondary btn-sm">🔍</button>
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
                    $cat_name       = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Asset';
                    ?>
                    <article id="archive-product-<?php the_ID(); ?>" class="product-card">
                        <a href="<?php the_permalink(); ?>" class="product-card-thumb">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'marketplace-card' ); ?>
                            <?php else : ?>
                                <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=800&q=80" alt="<?php the_title_attribute(); ?>" />
                            <?php endif; ?>
                            <span class="product-badge-cat"><?php echo esc_html( $cat_name ); ?></span>
                        </a>
                        <div class="product-card-body">
                            <div class="product-card-meta">
                                <span><?php echo esc_html( $file_format ? $file_format : 'Digital Files' ); ?></span>
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

                            <h2 class="product-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <p class="product-card-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?></p>

                            <div class="product-card-footer">
                                <div class="product-price-box">
                                    <span class="product-price">$<?php echo esc_html( $price ); ?></span>
                                    <?php if ( $original_price ) : ?>
                                        <span class="product-price-orig">$<?php echo esc_html( $original_price ); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <?php esc_html_e( 'View Details', 'digital-marketplace' ); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Standard WordPress Pagination -->
            <div style="margin-top: 3rem; text-align: center;">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => __( '← Previous', 'digital-marketplace' ),
                    'next_text' => __( 'Next →', 'digital-marketplace' ),
                ) );
                ?>
            </div>

        <?php else : ?>
            <div style="padding: 4rem 2rem; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border-subtle); text-align: center;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;"><?php esc_html_e( 'No Products Found', 'digital-marketplace' ); ?></h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                    <?php esc_html_e( 'No digital assets matched your filter criteria. Try clearing search filters.', 'digital-marketplace' ); ?>
                </p>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>" class="btn btn-primary btn-sm">
                    <?php esc_html_e( 'View All Products', 'digital-marketplace' ); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>

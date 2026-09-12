<?php
/**
 * The main template file
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-default-index" style="padding: 3rem 0 5rem;">
    <div class="site-container">

        <h1 class="section-title" style="margin-bottom: 2rem;">
            <?php single_post_title(); ?>
        </h1>

        <?php if ( have_posts() ) : ?>
            <div class="products-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'product-card' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="product-card-thumb">
                                <?php the_post_thumbnail( 'marketplace-card' ); ?>
                            </a>
                        <?php endif; ?>
                        <div class="product-card-body">
                            <h2 class="product-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="product-card-desc">
                                <?php the_excerpt(); ?>
                            </div>
                            <div class="product-card-footer">
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <?php esc_html_e( 'Read More →', 'digital-marketplace' ); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No content available.', 'digital-marketplace' ); ?></p>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>

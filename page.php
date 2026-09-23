<?php
/**
 * The template for displaying all standard pages
 *
 * @package Digital_Marketplace
 */

get_header(); ?>

<div id="marketplace-single-page" style="padding: 3rem 0 5rem;">
    <div class="site-container" style="max-width: 820px;">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article-content' ); ?>>
                
                <header class="page-header" style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-subtle);">
                    <h1 class="page-title" style="font-size: 2.25rem; font-weight: 900; letter-spacing: -0.03em; color: var(--text-main); margin-bottom: 0.5rem; line-height: 1.2;">
                        <?php the_title(); ?>
                    </h1>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        <?php 
                        /* translators: %s: last updated date */
                        printf( esc_html__( 'Last updated: %s', 'digital-marketplace' ), esc_html( get_the_modified_date() ) ); 
                        ?>
                    </div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="page-featured-media" style="margin-bottom: 2rem; border-radius: var(--radius-lg); overflow: hidden;">
                        <?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: auto; display: block;' ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content typography-content" style="line-height: 1.75; font-size: 1rem; color: var(--text-main);">
                    <?php
                    the_content();

                    wp_link_pages( array(
                        'before' => '<div class="page-links" style="margin-top: 2rem; font-weight: 700;">' . esc_html__( 'Pages:', 'digital-marketplace' ),
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>

            </article>

        <?php endwhile; ?>

    </div>
</div>

<?php get_footer(); ?>

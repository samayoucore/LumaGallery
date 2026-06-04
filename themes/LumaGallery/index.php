<?php
/**
 * Fallback template — handles any unmatched request.
 * In practice, front-page.php, page.php, single.php etc. take priority.
 */

get_header();
?>

<main class="luma-main luma-main--default" id="main-content">

    <section class="luma-section luma-section--spacious">
        <div class="luma-container">

            <?php if ( have_posts() ) : ?>

                <header class="luma-archive-header">
                    <h1 class="luma-heading luma-heading--display">
                        <?php
                        if ( is_home() && ! is_front_page() ) {
                            single_post_title();
                        } elseif ( is_archive() ) {
                            the_archive_title();
                        } elseif ( is_search() ) {
                            printf(
                                /* translators: %s: search query */
                                esc_html__( 'Search results for: %s', 'luma-gallery' ),
                                '<span>' . get_search_query() . '</span>'
                            );
                        } else {
                            esc_html_e( 'Latest', 'luma-gallery' );
                        }
                        ?>
                    </h1>
                </header>

                <div class="luma-post-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article <?php post_class( 'luma-post-card' ); ?>>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>" class="luma-post-card__image-link">
                                    <?php the_post_thumbnail( 'luma-artwork-card', [ 'class' => 'luma-post-card__image' ] ); ?>
                                </a>
                            <?php endif; ?>
                            <div class="luma-post-card__body">
                                <h2 class="luma-post-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="luma-post-card__excerpt"><?php the_excerpt(); ?></div>
                                <a href="<?php the_permalink(); ?>" class="luma-button luma-button--ghost">
                                    <?php esc_html_e( 'Read more', 'luma-gallery' ); ?>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php the_posts_pagination( [
                    'prev_text' => '&larr; ' . esc_html__( 'Previous', 'luma-gallery' ),
                    'next_text' => esc_html__( 'Next', 'luma-gallery' ) . ' &rarr;',
                    'class'     => 'luma-pagination',
                ] ); ?>

            <?php else : ?>

                <div class="luma-empty-state">
                    <p class="luma-empty-state__text"><?php esc_html_e( 'Nothing found here yet.', 'luma-gallery' ); ?></p>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="luma-button luma-button--primary">
                        <?php esc_html_e( 'Back to Gallery', 'luma-gallery' ); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>

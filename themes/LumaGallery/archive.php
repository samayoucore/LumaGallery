<?php
/**
 * Archive template — categories, tags, dates, authors.
 */

get_header();
?>

<main class="luma-main luma-main--archive" id="main-content">

    <header class="luma-page-hero luma-page-hero--simple">
        <div class="luma-container">
            <?php the_archive_title( '<h1 class="luma-heading luma-heading--display">', '</h1>' ); ?>
            <?php the_archive_description( '<p class="luma-hero__sub">', '</p>' ); ?>
        </div>
    </header>

    <section class="luma-section luma-section--spacious">
        <div class="luma-container">

            <?php if ( have_posts() ) : ?>

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
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                    'class'     => 'luma-pagination',
                ] ); ?>

            <?php else : ?>

                <div class="luma-empty-state">
                    <p class="luma-empty-state__text"><?php esc_html_e( 'No posts found.', 'luma-gallery' ); ?></p>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="luma-button luma-button--primary">
                        <?php esc_html_e( 'Back to Gallery', 'luma-gallery' ); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>

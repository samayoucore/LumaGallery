<?php
/**
 * Archive: Journal (luma_artist_post).
 *
 * Editorial archive — featured story, topic filter bar and a post grid.
 * Falls back to demo editorial cards when no real stories exist yet.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Collect real posts from the main query ────────────────────────────────────
$real_posts = [];
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        $real_posts[] = get_post();
    }
    wp_reset_postdata();
}
$use_demo = empty( $real_posts );

$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

// ── Build the featured + grid view models ─────────────────────────────────────
$featured   = null;
$grid_posts = [];

if ( $use_demo ) {
    $demo = function_exists( 'luma_get_demo_artist_posts' ) ? luma_get_demo_artist_posts() : [];
    if ( ! empty( $demo ) ) {
        $featured   = $demo[0];
        $grid_posts = array_slice( $demo, 1 );
    }
} else {
    $views = array_map( 'luma_core_normalize_post', $real_posts );
    if ( $paged <= 1 ) {
        $featured   = $views[0];
        $grid_posts = array_slice( $views, 1 );
    } else {
        $grid_posts = $views;
    }
}

// ── Topic filter options (real terms, else defaults) ──────────────────────────
$topics      = [];
$topic_terms = get_terms( [ 'taxonomy' => 'luma_post_topic', 'hide_empty' => false ] );
if ( ! is_wp_error( $topic_terms ) && ! empty( $topic_terms ) ) {
    foreach ( $topic_terms as $term ) {
        $topics[ $term->slug ] = $term->name;
    }
} else {
    $topics = [
        'artist-stories' => __( 'Artist Stories', 'luma-gallery' ),
        'exhibitions'    => __( 'Exhibitions', 'luma-gallery' ),
        'collecting-art' => __( 'Collecting Art', 'luma-gallery' ),
        'studio-notes'   => __( 'Studio Notes', 'luma-gallery' ),
    ];
}
?>

<main class="luma-main luma-page luma-page--journal" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-journal-hero">
        <div class="luma-container">
            <p class="luma-journal-hero__eyebrow"><?php esc_html_e( 'The Luma Journal', 'luma-gallery' ); ?></p>
            <h1 class="luma-journal-hero__title"><?php esc_html_e( 'Journal', 'luma-gallery' ); ?></h1>
            <p class="luma-journal-hero__subtitle">
                <?php esc_html_e( 'Stories from artists, exhibitions, and the world of contemporary art.', 'luma-gallery' ); ?>
            </p>
        </div>
        <div class="luma-journal-hero__word js-distort-text" aria-hidden="true">JOURNAL</div>
    </section>

    <?php if ( $featured ) : ?>
        <!-- ─── Featured story ───────────────────────────────────────────── -->
        <section class="luma-journal-featured-wrap">
            <div class="luma-container">
                <article class="luma-journal-featured" data-reveal>
                    <a href="<?php echo esc_url( $featured['url'] ); ?>" class="luma-journal-featured__media">
                        <?php if ( ! empty( $featured['image'] ) ) : ?>
                            <img src="<?php echo esc_url( $featured['image'] ); ?>" alt="<?php echo esc_attr( $featured['title'] ); ?>">
                        <?php else : ?>
                            <span class="luma-journal-featured__placeholder" style="background:<?php echo esc_attr( $featured['gradient'] ); ?>;" aria-hidden="true"></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $featured['topic'] ) ) : ?>
                            <span class="luma-journal-featured__topic"><?php echo esc_html( $featured['topic'] ); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="luma-journal-featured__body">
                        <p class="luma-journal-featured__label"><?php esc_html_e( 'Featured story', 'luma-gallery' ); ?></p>
                        <h2 class="luma-journal-featured__title">
                            <a href="<?php echo esc_url( $featured['url'] ); ?>"><?php echo esc_html( $featured['title'] ); ?></a>
                        </h2>
                        <p class="luma-journal-featured__excerpt"><?php echo esc_html( $featured['excerpt'] ); ?></p>
                        <div class="luma-journal-featured__meta">
                            <?php if ( ! empty( $featured['date'] ) ) : ?>
                                <span class="luma-journal-featured__date"><?php echo esc_html( $featured['date'] ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $featured['artist'] ) ) : ?>
                                <span class="luma-journal-featured__artist">
                                    <?php if ( ! empty( $featured['artist_url'] ) ) : ?>
                                        <a href="<?php echo esc_url( $featured['artist_url'] ); ?>"><?php echo esc_html( $featured['artist'] ); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html( $featured['artist'] ); ?>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo esc_url( $featured['url'] ); ?>" class="luma-button luma-button--primary luma-journal-featured__cta">
                            <?php esc_html_e( 'Read story', 'luma-gallery' ); ?>
                        </a>
                    </div>
                </article>
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Filter bar ───────────────────────────────────────────────────── -->
    <section class="luma-journal-filterbar">
        <div class="luma-container">
            <div class="luma-journal-filters" role="group" aria-label="<?php esc_attr_e( 'Filter stories by topic', 'luma-gallery' ); ?>">
                <button class="luma-mood-chip js-journal-filter is-active" data-topic="all" aria-pressed="true"><?php esc_html_e( 'All', 'luma-gallery' ); ?></button>
                <?php foreach ( $topics as $slug => $label ) : ?>
                    <button class="luma-mood-chip js-journal-filter" data-topic="<?php echo esc_attr( $slug ); ?>" aria-pressed="false"><?php echo esc_html( $label ); ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ─── Posts grid ───────────────────────────────────────────────────── -->
    <section class="luma-journal-body">
        <div class="luma-container">

            <?php if ( $featured || ! empty( $grid_posts ) ) : ?>
                <div class="luma-post-grid js-journal-grid" data-reveal-children="60">
                    <?php foreach ( $grid_posts as $i => $view ) : ?>
                        <?php
                        $view['index'] = $i;
                        get_template_part( 'template-parts/journal-card', null, $view );
                        ?>
                    <?php endforeach; ?>
                </div>

                <!-- Empty state for active filter -->
                <div class="luma-empty-state js-journal-empty" hidden>
                    <p class="luma-empty-state__text"><?php esc_html_e( 'No stories in this topic yet.', 'luma-gallery' ); ?></p>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'luma_artist_post' ) ?: home_url( '/journal/' ) ); ?>" class="luma-button luma-button--ghost"><?php esc_html_e( 'View all stories', 'luma-gallery' ); ?></a>
                </div>
            <?php else : ?>
                <div class="luma-empty-state">
                    <p class="luma-empty-state__text"><?php esc_html_e( 'No stories have been published yet.', 'luma-gallery' ); ?></p>
                    <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="luma-button luma-button--primary"><?php esc_html_e( 'Explore the Gallery', 'luma-gallery' ); ?></a>
                </div>
            <?php endif; ?>

            <?php if ( ! $use_demo ) : ?>
                <?php
                the_posts_pagination( [
                    'mid_size'  => 1,
                    'prev_text' => __( 'Previous', 'luma-gallery' ),
                    'next_text' => __( 'Next', 'luma-gallery' ),
                    'class'     => 'luma-pagination',
                ] );
                ?>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>

<?php
/**
 * Search results — Phase 7A.
 *
 * Editorial results page (not the default WP list). Works whether or not the
 * CPTs have real posts, and never assumes WooCommerce is active. Each result
 * is labelled by post type; thumbnails fall back to a placeholder gradient.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$search_query = get_search_query( false ); // raw; escaped at output
$has_query    = '' !== trim( (string) $search_query );
$total        = isset( $GLOBALS['wp_query']->found_posts ) ? (int) $GLOBALS['wp_query']->found_posts : 0;

// Presentational post-type → label map (no business logic).
$luma_type_labels = [
    'luma_artwork'     => __( 'Artwork', 'luma-gallery' ),
    'luma_artist'      => __( 'Artist', 'luma-gallery' ),
    'luma_exhibition'  => __( 'Exhibition', 'luma-gallery' ),
    'luma_artist_post' => __( 'Journal', 'luma-gallery' ),
    'page'             => __( 'Page', 'luma-gallery' ),
    'post'             => __( 'Article', 'luma-gallery' ),
];
?>

<main class="luma-search" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-search__hero">
        <span class="luma-search__word" aria-hidden="true">SEARCH</span>
        <div class="luma-container">
            <p class="luma-search__eyebrow"><?php esc_html_e( 'Search Luma', 'luma-gallery' ); ?></p>
            <h1 class="luma-search__title">
                <?php
                if ( $has_query ) {
                    /* translators: %s: search query */
                    printf( esc_html__( 'Results for “%s”', 'luma-gallery' ), esc_html( $search_query ) );
                } else {
                    esc_html_e( 'Search the gallery', 'luma-gallery' );
                }
                ?>
            </h1>
            <p class="luma-search__subtitle">
                <?php esc_html_e( 'Find artworks, artists, exhibitions, and journal stories.', 'luma-gallery' ); ?>
            </p>

            <form class="luma-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <label class="luma-search__label" for="luma-search-field"><?php esc_html_e( 'Search the gallery', 'luma-gallery' ); ?></label>
                <input
                    class="luma-input luma-search__field"
                    type="search"
                    id="luma-search-field"
                    name="s"
                    value="<?php echo esc_attr( $search_query ); ?>"
                    placeholder="<?php esc_attr_e( 'Search artworks, artists, journal…', 'luma-gallery' ); ?>"
                    autocomplete="off"
                >
                <button type="submit" class="luma-button luma-button--accent luma-search__submit"><?php esc_html_e( 'Search', 'luma-gallery' ); ?></button>
            </form>
        </div>
    </section>

    <!-- ─── Body ─────────────────────────────────────────────────────────── -->
    <section class="luma-search__body">
        <div class="luma-container">

            <?php if ( have_posts() ) : ?>

                <p class="luma-search__count" role="status">
                    <?php
                    /* translators: %s: number of results */
                    printf( esc_html( _n( '%s result found', '%s results found', $total, 'luma-gallery' ) ), esc_html( number_format_i18n( $total ) ) );
                    ?>
                </p>

                <div class="luma-search__results">
                    <?php
                    $luma_i = 0;
                    while ( have_posts() ) :
                        the_post();
                        $pt        = get_post_type();
                        $pt_label  = $luma_type_labels[ $pt ] ?? '';
                        if ( '' === $pt_label ) {
                            $obj      = get_post_type_object( $pt );
                            $pt_label = ( $obj && isset( $obj->labels->singular_name ) ) ? $obj->labels->singular_name : __( 'Result', 'luma-gallery' );
                        }
                        $is_dated = in_array( $pt, [ 'post', 'luma_artist_post' ], true );
                        $excerpt  = wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 24, '…' );
                        ?>
                        <article <?php post_class( 'luma-search__item' ); ?>>
                            <a class="luma-search__item-media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'luma-artwork-card', [ 'class' => 'luma-search__item-img', 'alt' => '' ] ); ?>
                                <?php else : ?>
                                    <span class="luma-search__item-fallback" style="background:<?php echo esc_attr( luma_get_placeholder_gradient( $luma_i ) ); ?>;"></span>
                                <?php endif; ?>
                            </a>
                            <div class="luma-search__item-body">
                                <span class="luma-search__item-type"><?php echo esc_html( $pt_label ); ?></span>
                                <h2 class="luma-search__item-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <?php if ( $excerpt ) : ?>
                                    <p class="luma-search__item-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                <?php endif; ?>
                                <div class="luma-search__item-foot">
                                    <?php if ( $is_dated ) : ?>
                                        <time class="luma-search__item-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                    <?php endif; ?>
                                    <a class="luma-search__item-cta" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Open', 'luma-gallery' ); ?> &rarr;</a>
                                </div>
                            </div>
                        </article>
                        <?php
                        $luma_i++;
                    endwhile;
                    ?>
                </div>

                <?php the_posts_pagination( [
                    'prev_text' => '&larr; ' . esc_html__( 'Previous', 'luma-gallery' ),
                    'next_text' => esc_html__( 'Next', 'luma-gallery' ) . ' &rarr;',
                    'class'     => 'luma-pagination',
                ] ); ?>

            <?php else : ?>

                <div class="luma-search__empty">
                    <p class="luma-search__empty-eyebrow"><?php esc_html_e( 'No results found', 'luma-gallery' ); ?></p>
                    <h2 class="luma-search__empty-title"><?php esc_html_e( 'Nothing found', 'luma-gallery' ); ?></h2>
                    <p class="luma-search__empty-text"><?php esc_html_e( 'Try a different keyword or explore curated sections instead.', 'luma-gallery' ); ?></p>
                    <div class="luma-search__empty-actions">
                        <a class="luma-button luma-button--accent" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Explore Gallery', 'luma-gallery' ); ?></a>
                        <a class="luma-button luma-button--ghost" href="<?php echo esc_url( home_url( '/artists/' ) ); ?>"><?php esc_html_e( 'Meet Artists', 'luma-gallery' ); ?></a>
                        <a class="luma-button luma-button--ghost" href="<?php echo esc_url( home_url( '/ai-curator/' ) ); ?>"><?php esc_html_e( 'AI Curator', 'luma-gallery' ); ?></a>
                    </div>

                    <div class="luma-search__suggest">
                        <a class="luma-search__suggest-card" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>">
                            <span class="luma-search__suggest-label"><?php esc_html_e( 'Browse', 'luma-gallery' ); ?></span>
                            <span class="luma-search__suggest-title"><?php esc_html_e( 'Gallery', 'luma-gallery' ); ?></span>
                        </a>
                        <a class="luma-search__suggest-card" href="<?php echo esc_url( home_url( '/exhibitions/' ) ); ?>">
                            <span class="luma-search__suggest-label"><?php esc_html_e( 'Visit', 'luma-gallery' ); ?></span>
                            <span class="luma-search__suggest-title"><?php esc_html_e( 'Exhibitions', 'luma-gallery' ); ?></span>
                        </a>
                        <a class="luma-search__suggest-card" href="<?php echo esc_url( home_url( '/journal/' ) ); ?>">
                            <span class="luma-search__suggest-label"><?php esc_html_e( 'Read', 'luma-gallery' ); ?></span>
                            <span class="luma-search__suggest-title"><?php esc_html_e( 'Journal', 'luma-gallery' ); ?></span>
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>

<?php
/**
 * Template Name: Artists
 *
 * Artists listing page — /artists/
 * Always shows content: real posts → demo plugin data → hardcoded fallback.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── 1. Try real luma_artist posts ─────────────────────────────────────────────
$artists_query = new WP_Query( [
    'post_type'      => 'luma_artist',
    'posts_per_page' => 24,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
$has_real = $artists_query->have_posts();

// ── 2. Demo data — always available regardless of plugin state ────────────────
$gradients = [
    'linear-gradient(135deg,#2C2C2A,#4A4845)',
    'linear-gradient(160deg,#3A3530,#5C5550)',
    'linear-gradient(120deg,#1E2A3A,#2E4A5A)',
    'linear-gradient(145deg,#2A1E1A,#4A3530)',
    'linear-gradient(135deg,#1A2A1A,#3A4A3A)',
    'linear-gradient(150deg,#2A2A1A,#4A4A2A)',
];

$demo_artists = [
    [ 'id' => 0, 'name' => 'Elena Morozova', 'location' => 'Moscow, Russia',   'bio' => 'Painter of light and quiet domestic interiors. Working in oil on linen.',         'artworks' => 14, 'gradient' => $gradients[0], 'url' => home_url('/artists/') ],
    [ 'id' => 0, 'name' => 'Victor Hale',    'location' => 'London, UK',       'bio' => 'Urban landscapes and atmospheric blues. Works with acrylic and ink.',              'artworks' => 9,  'gradient' => $gradients[1], 'url' => home_url('/artists/') ],
    [ 'id' => 0, 'name' => 'Mira Solen',     'location' => 'Paris, France',    'bio' => 'Abstract painter focused on emotional color fields and gestural marks.',            'artworks' => 11, 'gradient' => $gradients[2], 'url' => home_url('/artists/') ],
    [ 'id' => 0, 'name' => 'Anna Weiss',     'location' => 'Berlin, Germany',  'bio' => 'Minimalist print-maker exploring form, pattern and negative space.',              'artworks' => 7,  'gradient' => $gradients[3], 'url' => home_url('/artists/') ],
    [ 'id' => 0, 'name' => 'Daniel Arno',    'location' => 'New York, USA',    'bio' => 'Figurative work between surrealism and memory — oil and charcoal.',               'artworks' => 18, 'gradient' => $gradients[4], 'url' => home_url('/artists/') ],
    [ 'id' => 0, 'name' => 'Sofia Lumen',    'location' => 'Milan, Italy',     'bio' => 'Light and glass studies in watercolor. Quiet, luminous, meditative.',             'artworks' => 6,  'gradient' => $gradients[5], 'url' => home_url('/artists/') ],
];

// Use plugin-enriched demo data if available, otherwise use hardcoded
if ( ! $has_real && function_exists( 'luma_get_demo_artists' ) ) {
    $plugin_data = luma_get_demo_artists( 24 );
    if ( ! empty( $plugin_data ) ) {
        $demo_artists = $plugin_data;
    }
}

// If real posts exist, build the same array structure from DB
$artists = [];
if ( $has_real ) {
    foreach ( $artists_query->posts as $i => $post ) {
        $artists[] = [
            'id'       => $post->ID,
            'name'     => get_the_title( $post->ID ),
            'location' => get_post_meta( $post->ID, 'artist_location',    true ) ?: '',
            'bio'      => get_post_meta( $post->ID, 'artist_short_bio',   true ) ?: '',
            'artworks' => (int) get_post_meta( $post->ID, 'artist_artworks_count', true ),
            'gradient' => function_exists( 'luma_get_placeholder_gradient' ) ? luma_get_placeholder_gradient( $i ) : $gradients[ $i % count( $gradients ) ],
            'url'      => get_permalink( $post->ID ) ?: '#',
        ];
    }
    wp_reset_postdata();
} else {
    $artists = $demo_artists;
}

$featured      = ! empty( $artists ) ? $artists[0] : null;
$style_filters = [ 'Abstract', 'Figurative', 'Landscape', 'Minimalist', 'Expressionist', 'Mixed Media' ];
?>

<main class="luma-main" id="main-content">

    <!-- ─── Page hero ─────────────────────────────────────────────────────── -->
    <section class="luma-page-hero luma-page-hero--artists" aria-label="<?php esc_attr_e( 'Artists', 'luma-gallery' ); ?>">
        <div class="luma-container">
            <div class="luma-page-hero__inner">
                <p class="luma-page-hero__eyebrow"><?php esc_html_e( 'Luma Gallery', 'luma-gallery' ); ?></p>
                <h1 class="luma-page-hero__title"><?php esc_html_e( 'Artists', 'luma-gallery' ); ?></h1>
                <p class="luma-page-hero__subtitle"><?php esc_html_e( 'Meet the artists behind the works', 'luma-gallery' ); ?></p>
            </div>
        </div>
        <div class="luma-page-hero__word js-distort-text" aria-hidden="true">ARTISTS</div>
    </section>

    <!-- ─── Featured artist ───────────────────────────────────────────────── -->
    <?php if ( $featured ) : ?>
        <section class="luma-artists-featured" aria-label="<?php esc_attr_e( 'Featured artist', 'luma-gallery' ); ?>">
            <div class="luma-artists-featured__inner">
                <div class="luma-artists-featured__text">
                    <p class="luma-artists-featured__label"><?php esc_html_e( 'Featured Artist', 'luma-gallery' ); ?></p>
                    <h2 class="luma-artists-featured__name"><?php echo esc_html( $featured['name'] ); ?></h2>
                    <?php if ( ! empty( $featured['location'] ) ) : ?>
                        <p class="luma-artists-featured__location">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?php echo esc_html( $featured['location'] ); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ( ! empty( $featured['bio'] ) ) : ?>
                        <p class="luma-artists-featured__bio"><?php echo esc_html( wp_trim_words( $featured['bio'], 30 ) ); ?></p>
                    <?php endif; ?>
                    <div class="luma-artists-featured__actions">
                        <a href="<?php echo esc_url( $featured['url'] ?? '#' ); ?>" class="luma-button luma-button--ghost-light">
                            <?php esc_html_e( 'View Profile', 'luma-gallery' ); ?>
                        </a>
                        <?php if ( ( $featured['id'] ?? 0 ) > 0 ) : ?>
                            <button class="luma-button luma-button--ghost-light js-toggle-favorite"
                                data-artist-id="<?php echo esc_attr( $featured['id'] ); ?>"
                                data-type="artist"
                                aria-pressed="false">
                                <?php esc_html_e( 'Follow', 'luma-gallery' ); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="luma-artists-featured__image">
                    <?php if ( ( $featured['id'] ?? 0 ) > 0 && has_post_thumbnail( $featured['id'] ) ) : ?>
                        <?php echo get_the_post_thumbnail( $featured['id'], 'luma-artist-cover', [ 'alt' => esc_attr( $featured['name'] ) ] ); ?>
                    <?php else : ?>
                        <div style="background:<?php echo esc_attr( $featured['gradient'] ); ?>;width:100%;height:100%;min-height:400px;display:flex;align-items:center;justify-content:center;">
                            <span style="font-family:var(--luma-font-heading,Georgia,serif);font-size:clamp(60px,12vw,140px);font-weight:300;color:rgba(255,255,255,.18);"><?php echo esc_html( mb_substr( $featured['name'], 0, 1 ) ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Toolbar: search + style filter ──────────────────────────────── -->
    <div class="luma-artists-toolbar">
        <div class="luma-artists-toolbar__inner">
            <div class="luma-artists-search">
                <svg class="luma-artists-search__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="search" class="luma-input luma-artists-search__input js-artist-search"
                    placeholder="<?php esc_attr_e( 'Search artists…', 'luma-gallery' ); ?>" autocomplete="off"
                    aria-label="<?php esc_attr_e( 'Search artists', 'luma-gallery' ); ?>">
            </div>
            <div class="luma-style-filter" role="group" aria-label="<?php esc_attr_e( 'Filter by style', 'luma-gallery' ); ?>">
                <span class="luma-style-filter__label"><?php esc_html_e( 'Style:', 'luma-gallery' ); ?></span>
                <button class="luma-style-chip is-active js-style-chip" data-style=""><?php esc_html_e( 'All', 'luma-gallery' ); ?></button>
                <?php foreach ( $style_filters as $s ) : ?>
                    <button class="luma-style-chip js-style-chip" data-style="<?php echo esc_attr( $s ); ?>"><?php echo esc_html( $s ); ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ─── Artists grid ──────────────────────────────────────────────────── -->
    <section class="luma-artists-body">
        <div class="luma-artists-header">
            <h2 class="luma-artists-header__title"><?php esc_html_e( 'All Artists', 'luma-gallery' ); ?></h2>
            <span class="luma-artists-header__count js-artists-count">
                <?php
                $total = count( $artists );
                printf( esc_html( _n( '%d artist', '%d artists', $total, 'luma-gallery' ) ), esc_html( $total ) );
                ?>
            </span>
        </div>
        <div class="luma-artists-grid js-artists-grid" data-reveal-children="55">
            <?php foreach ( $artists as $i => $a ) :
                get_template_part( 'template-parts/artist-card', null, [
                    'post_id'        => $a['id']       ?? 0,
                    'name'           => $a['name']     ?? '',
                    'location'       => $a['location'] ?? '',
                    'short_bio'      => $a['bio']      ?? '',
                    'artworks_count' => $a['artworks'] ?? 0,
                    'gradient'       => $a['gradient'] ?? '',
                    'index'          => $i,
                ] );
            endforeach; ?>
        </div>
    </section>

    <!-- ─── CTA: Become an Artist ─────────────────────────────────────────── -->
    <section class="luma-artists-cta">
        <div class="luma-artists-cta__inner">
            <p class="luma-artists-cta__eyebrow"><?php esc_html_e( 'Join Luma Gallery', 'luma-gallery' ); ?></p>
            <h2 class="luma-artists-cta__title"><?php esc_html_e( 'Become an Artist', 'luma-gallery' ); ?></h2>
            <p class="luma-artists-cta__text"><?php esc_html_e( 'Share your work with a curated audience of collectors and art lovers. Apply to join our roster of independent artists.', 'luma-gallery' ); ?></p>
            <a href="<?php echo esc_url( home_url( '/apply/' ) ); ?>" class="luma-button luma-button--primary luma-button--lg">
                <?php esc_html_e( 'Apply Now', 'luma-gallery' ); ?>
            </a>
        </div>
    </section>

</main>

<?php get_footer(); ?>

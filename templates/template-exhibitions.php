<?php
/**
 * Template Name: Exhibitions
 *
 * Exhibitions listing page — /exhibitions/
 * Always shows content: real posts → demo plugin data → hardcoded fallback.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── 1. Try real luma_exhibition posts ─────────────────────────────────────────
$exh_query = new WP_Query( [
    'post_type'      => 'luma_exhibition',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
$has_real = $exh_query->have_posts();

// ── 2. Hardcoded demo data — always available ─────────────────────────────────
$gradients = [
    'linear-gradient(120deg,#1E2A3A,#2E4A5A)',
    'linear-gradient(135deg,#2A2A1A,#4A4A2A)',
    'linear-gradient(145deg,#2A1E1A,#4A3530)',
    'linear-gradient(150deg,#1A2A1A,#3A4A3A)',
];

$demo_exhibitions = [
    [
        'id'          => 0,
        'title'       => 'Night Silence',
        'subtitle'    => 'Current Exhibition',
        'curator'     => 'A meditative journey through after-hours landscapes and the weight of quiet.',
        'works_count' => 12,
        'mood_tags'   => [ 'dark', 'atmospheric', 'calm' ],
        'gradient'    => $gradients[0],
        'url'         => home_url( '/exhibitions/' ),
    ],
    [
        'id'          => 0,
        'title'       => 'Soft Architecture',
        'subtitle'    => 'Ongoing',
        'curator'     => 'Interior spaces rendered in muted oil — form meets feeling.',
        'works_count' => 8,
        'mood_tags'   => [ 'minimal', 'calm' ],
        'gradient'    => $gradients[1],
        'url'         => home_url( '/exhibitions/' ),
    ],
    [
        'id'          => 0,
        'title'       => 'Rooms of Light',
        'subtitle'    => 'Curated Collection',
        'curator'     => 'Domestic interiors painted with a reverence for the everyday and the luminous.',
        'works_count' => 10,
        'mood_tags'   => [ 'bright', 'romantic' ],
        'gradient'    => $gradients[2],
        'url'         => home_url( '/exhibitions/' ),
    ],
    [
        'id'          => 0,
        'title'       => 'Between Memory and Color',
        'subtitle'    => 'Archive',
        'curator'     => 'A chromatic investigation of recollection and abstraction.',
        'works_count' => 14,
        'mood_tags'   => [ 'melancholic', 'abstract' ],
        'gradient'    => $gradients[3],
        'url'         => home_url( '/exhibitions/' ),
    ],
];

// Use plugin-enriched demo data if available
if ( ! $has_real && function_exists( 'luma_get_demo_exhibitions' ) ) {
    $plugin_data = luma_get_demo_exhibitions( 12 );
    if ( ! empty( $plugin_data ) ) {
        // Plugin uses 'curator' key — normalize to match our structure
        $demo_exhibitions = [];
        foreach ( $plugin_data as $ex ) {
            $demo_exhibitions[] = [
                'id'          => $ex['id']          ?? 0,
                'title'       => $ex['title']        ?? '',
                'subtitle'    => $ex['subtitle']     ?? '',
                'curator'     => $ex['curator']      ?? '',
                'works_count' => $ex['works_count']  ?? 0,
                'mood_tags'   => $ex['mood_tags']    ?? [],
                'gradient'    => $ex['gradient']     ?? '',
                'url'         => $ex['url']          ?? home_url( '/exhibitions/' ),
            ];
        }
    }
}

// If real posts exist, build array from DB
$exhibitions = [];
if ( $has_real ) {
    foreach ( $exh_query->posts as $i => $post ) {
        $mood_terms = wp_get_post_terms( $post->ID, 'luma_mood', [ 'fields' => 'names' ] );
        $exhibitions[] = [
            'id'          => $post->ID,
            'title'       => get_the_title( $post->ID ),
            'subtitle'    => get_post_meta( $post->ID, 'exhibition_subtitle',     true ) ?: '',
            'curator'     => get_post_meta( $post->ID, 'exhibition_curator_note', true ) ?: '',
            'works_count' => (int) get_post_meta( $post->ID, 'exhibition_works_count', true ),
            'mood_tags'   => is_wp_error( $mood_terms ) ? [] : $mood_terms,
            'gradient'    => isset( $gradients[ $i % count( $gradients ) ] ) ? $gradients[ $i % count( $gradients ) ] : '',
            'url'         => get_permalink( $post->ID ) ?: '#',
        ];
    }
    wp_reset_postdata();
} else {
    $exhibitions = $demo_exhibitions;
}

$featured     = ! empty( $exhibitions ) ? $exhibitions[0] : null;
$mood_filters = [ 'Calm', 'Dark', 'Melancholic', 'Romantic', 'Bright', 'Abstract', 'Atmospheric', 'Minimal' ];
?>

<main class="luma-main" id="main-content">

    <!-- ─── Page hero ─────────────────────────────────────────────────────── -->
    <section class="luma-page-hero luma-page-hero--exhibitions" aria-label="<?php esc_attr_e( 'Exhibitions', 'luma-gallery' ); ?>">
        <div class="luma-container">
            <div class="luma-page-hero__inner">
                <p class="luma-page-hero__eyebrow"><?php esc_html_e( 'Luma Gallery', 'luma-gallery' ); ?></p>
                <h1 class="luma-page-hero__title"><?php esc_html_e( 'Curated Exhibitions', 'luma-gallery' ); ?></h1>
                <p class="luma-page-hero__subtitle"><?php esc_html_e( 'Explore artworks through mood, theme, and visual storytelling', 'luma-gallery' ); ?></p>
            </div>
        </div>
        <div class="luma-page-hero__word" aria-hidden="true">EXHIBITIONS</div>
    </section>

    <!-- ─── Featured exhibition ───────────────────────────────────────────── -->
    <?php if ( $featured ) : ?>
        <section class="luma-exhibitions-featured" aria-label="<?php esc_attr_e( 'Featured exhibition', 'luma-gallery' ); ?>">

            <?php if ( ( $featured['id'] ?? 0 ) > 0 && has_post_thumbnail( $featured['id'] ) ) : ?>
                <div class="luma-exhibitions-featured__cover">
                    <?php echo get_the_post_thumbnail( $featured['id'], 'luma-exhibition-cover', [ 'alt' => '' ] ); ?>
                </div>
            <?php else : ?>
                <div style="position:absolute;inset:0;background:<?php echo esc_attr( $featured['gradient'] ); ?>;opacity:.35;"></div>
            <?php endif; ?>

            <div class="luma-exhibitions-featured__gradient" aria-hidden="true"></div>

            <div class="luma-exhibitions-featured__content">
                <p class="luma-exhibitions-featured__eyebrow"><?php esc_html_e( 'Now Showing', 'luma-gallery' ); ?></p>
                <h2 class="luma-exhibitions-featured__title"><?php echo esc_html( $featured['title'] ); ?></h2>
                <?php if ( ! empty( $featured['subtitle'] ) ) : ?>
                    <p class="luma-exhibitions-featured__subtitle"><?php echo esc_html( $featured['subtitle'] ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $featured['curator'] ) ) : ?>
                    <p class="luma-exhibitions-featured__curator"><?php echo esc_html( wp_trim_words( $featured['curator'], 28 ) ); ?></p>
                <?php endif; ?>
                <div class="luma-exhibitions-featured__meta">
                    <?php if ( ! empty( $featured['works_count'] ) ) : ?>
                        <span class="luma-badge luma-badge--tag">
                            <?php printf( esc_html( _n( '%d work', '%d works', $featured['works_count'], 'luma-gallery' ) ), esc_html( $featured['works_count'] ) ); ?>
                        </span>
                    <?php endif; ?>
                    <?php foreach ( array_slice( $featured['mood_tags'] ?? [], 0, 3 ) as $tag ) : ?>
                        <span class="luma-badge luma-badge--mood"><?php echo esc_html( $tag ); ?></span>
                    <?php endforeach; ?>
                    <a href="<?php echo esc_url( $featured['url'] ?? '#' ); ?>" class="luma-button luma-button--ghost-light">
                        <?php esc_html_e( 'Enter Exhibition', 'luma-gallery' ); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Mood filter bar ───────────────────────────────────────────────── -->
    <nav class="luma-exhibitions-filter" aria-label="<?php esc_attr_e( 'Filter by mood', 'luma-gallery' ); ?>">
        <div class="luma-exhibitions-filter__inner">
            <span class="luma-exhibitions-filter__label"><?php esc_html_e( 'Mood:', 'luma-gallery' ); ?></span>
            <button class="luma-mood-chip is-active js-mood-chip" data-mood=""><?php esc_html_e( 'All', 'luma-gallery' ); ?></button>
            <?php foreach ( $mood_filters as $mood ) : ?>
                <button class="luma-mood-chip js-mood-chip" data-mood="<?php echo esc_attr( strtolower( $mood ) ); ?>"><?php echo esc_html( $mood ); ?></button>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- ─── Exhibitions grid ─────────────────────────────────────────────── -->
    <section class="luma-exhibitions-body">
        <div class="luma-exhibitions-body__header">
            <h2 class="luma-exhibitions-body__title"><?php esc_html_e( 'All Exhibitions', 'luma-gallery' ); ?></h2>
            <span class="luma-exhibitions-body__count">
                <?php
                $total = count( $exhibitions );
                printf( esc_html( _n( '%d exhibition', '%d exhibitions', $total, 'luma-gallery' ) ), esc_html( $total ) );
                ?>
            </span>
        </div>
        <div class="luma-exhibitions-grid js-exhibitions-grid">
            <?php foreach ( $exhibitions as $i => $ex ) :
                get_template_part( 'template-parts/exhibition-card', null, [
                    'post_id'      => $ex['id']          ?? 0,
                    'title'        => $ex['title']        ?? '',
                    'subtitle'     => $ex['subtitle']     ?? '',
                    'curator_note' => $ex['curator']      ?? '',
                    'works_count'  => $ex['works_count']  ?? 0,
                    'mood_tags'    => $ex['mood_tags']    ?? [],
                    'gradient'     => $ex['gradient']     ?? '',
                    'index'        => $i,
                ] );
            endforeach; ?>
        </div>
    </section>

    <!-- ─── CTA: Start Gallery Walk ───────────────────────────────────────── -->
    <section class="luma-exhibitions-cta">
        <div class="luma-exhibitions-cta__inner">
            <p class="luma-exhibitions-cta__eyebrow"><?php esc_html_e( 'Immersive Experience', 'luma-gallery' ); ?></p>
            <h2 class="luma-exhibitions-cta__title"><?php esc_html_e( 'Start Gallery Walk', 'luma-gallery' ); ?></h2>
            <p class="luma-exhibitions-cta__text"><?php esc_html_e( 'Step into a curated sequence of artworks, guided by curator notes and mood. An immersive alternative to browsing.', 'luma-gallery' ); ?></p>
            <button class="luma-button luma-button--ghost-light luma-button--lg js-gallery-walk-start">
                <?php esc_html_e( 'Begin Gallery Walk', 'luma-gallery' ); ?>
            </button>
        </div>
    </section>

</main>

<?php get_footer(); ?>

<?php
/**
 * Single Exhibition — luma_exhibition CPT.
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! have_posts() ) {
    get_footer();
    return;
}

the_post();
$post_id = get_the_ID();

// Meta
$title        = get_the_title();
$subtitle     = get_post_meta( $post_id, 'exhibition_subtitle',     true ) ?: '';
$curator_note = get_post_meta( $post_id, 'exhibition_curator_note', true ) ?: '';
$intro_text   = get_the_content();
$works_count  = (int) get_post_meta( $post_id, 'exhibition_works_count', true );
$mood_tags    = wp_get_post_terms( $post_id, 'luma_mood', [ 'fields' => 'names' ] );
$mood_tags    = is_wp_error( $mood_tags ) ? [] : $mood_tags;
$cover_src    = get_the_post_thumbnail_url( $post_id, 'luma-exhibition-cover' ) ?: '';
$gradient     = function_exists( 'luma_get_placeholder_gradient' ) ? luma_get_placeholder_gradient( 0 ) : '';

// Artworks in this exhibition
$artwork_ids = (array) get_post_meta( $post_id, 'exhibition_artwork_ids', true );
$cpt = class_exists( 'WooCommerce' ) ? 'product' : 'luma_artwork';

if ( ! empty( $artwork_ids ) ) {
    $artworks_q = new WP_Query( [
        'post_type'      => $cpt,
        'post__in'       => $artwork_ids,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'post__in',
    ] );
    $has_real_artworks = $artworks_q->have_posts();
} else {
    $has_real_artworks = false;
    $artworks_q        = new WP_Query( [ 'post_type' => $cpt, 'posts_per_page' => 0 ] );
}

// Demo artworks fallback
$demo_artworks = [];
if ( ! $has_real_artworks && function_exists( 'luma_get_demo_artworks' ) ) {
    $demo_artworks = luma_get_demo_artworks( 6 );
    if ( ! $works_count ) $works_count = count( $demo_artworks );
}

// Demo commentary blocks
$commentary_blocks = [
    [
        'label'  => __( 'Opening Work', 'luma-gallery' ),
        'title'  => ( $demo_artworks[0]['title'] ?? $title ),
        'artist' => ( $demo_artworks[0]['artist'] ?? '' ),
        'text'   => __( 'The exhibition opens with a work that sets the emotional key for everything that follows. A quiet confrontation with the infinite — light held in suspension, the room altered by its presence.', 'luma-gallery' ),
    ],
    [
        'label'  => __( 'Midpoint', 'luma-gallery' ),
        'title'  => ( $demo_artworks[2]['title'] ?? '' ),
        'artist' => ( $demo_artworks[2]['artist'] ?? '' ),
        'text'   => __( 'At the heart of the exhibition, the works enter into dialogue. Color becomes architecture; silence becomes narrative. The viewer is invited to pause, to look longer, to resist conclusion.', 'luma-gallery' ),
    ],
    [
        'label'  => __( 'Closing', 'luma-gallery' ),
        'title'  => ( $demo_artworks[4]['title'] ?? '' ),
        'artist' => ( $demo_artworks[4]['artist'] ?? '' ),
        'text'   => __( 'The final work resolves without resolution — an open chord. It asks the visitor to carry something unnamed back into the world.', 'luma-gallery' ),
    ],
];
?>

<main class="luma-main" id="main-content">

    <!-- ─── Exhibition hero ───────────────────────────────────────────────── -->
    <section class="luma-exhibition-hero" aria-label="<?php echo esc_attr( $title ); ?>">

        <?php if ( $cover_src ) : ?>
            <div class="luma-exhibition-hero__cover">
                <img src="<?php echo esc_url( $cover_src ); ?>" alt="" aria-hidden="true">
            </div>
        <?php else : ?>
            <div class="luma-exhibition-hero__cover-placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;position:absolute;inset:0;opacity:.3;"></div>
        <?php endif; ?>

        <div class="luma-exhibition-hero__gradient" aria-hidden="true"></div>

        <div class="luma-exhibition-hero__content">
            <p class="luma-exhibition-hero__eyebrow">
                <?php echo $subtitle ? esc_html( $subtitle ) : esc_html__( 'Exhibition', 'luma-gallery' ); ?>
            </p>
            <h1 class="luma-exhibition-hero__title"><?php echo esc_html( $title ); ?></h1>
            <?php if ( $subtitle && $subtitle !== get_the_title() ) : ?>
                <p class="luma-exhibition-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
            <?php if ( $curator_note ) : ?>
                <blockquote class="luma-exhibition-hero__curator">
                    <?php echo esc_html( wp_trim_words( $curator_note, 30 ) ); ?>
                </blockquote>
            <?php endif; ?>
            <div class="luma-exhibition-hero__meta">
                <?php if ( $works_count ) : ?>
                    <span class="luma-badge luma-badge--tag">
                        <?php printf( esc_html( _n( '%d work', '%d works', $works_count, 'luma-gallery' ) ), esc_html( $works_count ) ); ?>
                    </span>
                <?php endif; ?>
                <?php foreach ( array_slice( $mood_tags, 0, 4 ) as $tag ) : ?>
                    <span class="luma-badge luma-badge--mood"><?php echo esc_html( $tag ); ?></span>
                <?php endforeach; ?>
                <button class="luma-button luma-button--ghost-light luma-button--sm js-gallery-walk-start">
                    <?php esc_html_e( 'Start Gallery Walk', 'luma-gallery' ); ?>
                </button>
            </div>
        </div>

    </section>

    <!-- ─── Intro text ────────────────────────────────────────────────────── -->
    <?php if ( $intro_text ) : ?>
        <div class="luma-exhibition-intro">
            <?php echo wp_kses_post( wpautop( $intro_text ) ); ?>
        </div>
    <?php elseif ( $curator_note ) : ?>
        <div class="luma-exhibition-intro">
            <p><?php echo esc_html( $curator_note ); ?></p>
        </div>
    <?php endif; ?>

    <!-- ─── Artworks grid ─────────────────────────────────────────────────── -->
    <section class="luma-exhibition-artworks">
        <div class="luma-exhibition-artworks__header">
            <h2 class="luma-exhibition-artworks__title"><?php esc_html_e( 'Works in this Exhibition', 'luma-gallery' ); ?></h2>
            <span class="luma-exhibition-artworks__count">
                <?php $count = $has_real_artworks ? $artworks_q->found_posts : count( $demo_artworks );
                printf( esc_html( _n( '%d work', '%d works', $count, 'luma-gallery' ) ), esc_html( $count ) ); ?>
            </span>
        </div>
        <div class="luma-exhibition-artworks__grid">
            <?php if ( $has_real_artworks ) :
                while ( $artworks_q->have_posts() ) : $artworks_q->the_post();
                    $pid  = get_the_ID();
                    $meta = function_exists( 'luma_get_artwork_meta' ) ? luma_get_artwork_meta( $pid ) : [];
                    get_template_part( 'template-parts/artwork-card', null, [
                        'post_id'     => $pid,
                        'artist_name' => $meta['artist'] ?? '',
                        'price'       => $meta['price']  ?? '',
                        'status'      => $meta['status'] ?? 'available',
                        'index'       => $artworks_q->current_post,
                    ] );
                endwhile;
                wp_reset_postdata();
            else :
                foreach ( $demo_artworks as $i => $aw ) :
                    get_template_part( 'template-parts/artwork-card', null, [
                        'post_id'     => 0,
                        'title'       => $aw['title']    ?? '',
                        'artist_name' => $aw['artist']   ?? '',
                        'price'       => $aw['price']    ?? '',
                        'status'      => $aw['status']   ?? 'available',
                        'gradient'    => $aw['gradient'] ?? '',
                        'index'       => $i,
                    ] );
                endforeach;
            endif; ?>
        </div>
    </section>

    <!-- ─── Curator commentary ────────────────────────────────────────────── -->
    <section class="luma-exhibition-commentary" aria-label="<?php esc_attr_e( 'Curator commentary', 'luma-gallery' ); ?>">
        <?php foreach ( $commentary_blocks as $block ) : if ( empty( $block['title'] ) ) continue; ?>
            <div class="luma-commentary-block">
                <div>
                    <p class="luma-commentary-block__artwork-label"><?php echo esc_html( $block['label'] ); ?></p>
                    <h3 class="luma-commentary-block__artwork-title"><?php echo esc_html( $block['title'] ); ?></h3>
                    <?php if ( ! empty( $block['artist'] ) ) : ?>
                        <p class="luma-commentary-block__artwork-artist"><?php echo esc_html( $block['artist'] ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="luma-commentary-block__text">
                    <p><?php echo esc_html( $block['text'] ); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- ─── CTA: Start Gallery Walk ───────────────────────────────────────── -->
    <section class="luma-exhibition-cta">
        <div class="luma-exhibition-cta__inner">
            <p class="luma-exhibition-cta__eyebrow"><?php esc_html_e( 'Immersive Mode', 'luma-gallery' ); ?></p>
            <h2 class="luma-exhibition-cta__title"><?php esc_html_e( 'Start Gallery Walk', 'luma-gallery' ); ?></h2>
            <p class="luma-exhibition-cta__text"><?php esc_html_e( 'Experience this exhibition in full-screen mode, guided by curator notes and mood transitions.', 'luma-gallery' ); ?></p>
            <button class="luma-button luma-button--ghost-light luma-button--lg js-gallery-walk-start">
                <?php esc_html_e( 'Begin Gallery Walk', 'luma-gallery' ); ?>
            </button>
        </div>
    </section>

</main>

<?php get_footer(); ?>

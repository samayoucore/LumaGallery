<?php
/**
 * Hero — editorial brutalist layout.
 *
 * Full-width visual layers (outside container):
 *   __bg (z:0) → __object-wrap sculpture (z:1) → __glass panel (z:2) → __circle (z:3)
 *
 * Container-bound content (inside __content-wrap, max-width 1440px, centred):
 *   __word--top / __word--bottom / __intro  →  all at z:4
 *
 * Image: set via Customizer "luma_hero_image_url", fallback to bundled PNG.
 */

$hero_image     = get_theme_mod( 'luma_hero_image_url' );
$hero_image_alt = get_theme_mod( 'luma_hero_image_alt', __( 'Featured artwork', 'luma-gallery' ) );

if ( ! $hero_image ) {
    $fallback = get_theme_file_path( 'assets/images/hero/hero-item.png' );
    if ( file_exists( $fallback ) ) {
        $hero_image = get_theme_file_uri( 'assets/images/hero/hero-item.png' );
    }
}
?>

<section class="luma-hero" id="hero" aria-label="<?php esc_attr_e( 'Hero', 'luma-gallery' ); ?>">

    <!-- ─── Full-width visual layers ─────────────────────────────────────── -->

    <!-- z:0 — white background -->
    <div class="luma-hero__bg" aria-hidden="true"></div>

    <!-- z:1 — floating sculpture, bottom-anchored, behind glass -->
    <div class="luma-hero__object-wrap js-hero-object">
        <div class="luma-hero__object">
            <?php if ( $hero_image ) : ?>
                <img
                    src="<?php echo esc_url( $hero_image ); ?>"
                    alt="<?php echo esc_attr( $hero_image_alt ); ?>"
                    loading="eager"
                    decoding="async"
                    draggable="false"
                >
            <?php else : ?>
                <div class="luma-hero__object-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- z:2 — frosted glass panel, left side -->
    <div class="luma-hero__glass" aria-hidden="true"></div>

    <!-- z:3 — decorative circles, right side -->
    <div class="luma-hero__circle luma-hero__circle--lg js-hero-circle" aria-hidden="true"></div>
    <div class="luma-hero__circle luma-hero__circle--sm js-hero-circle" aria-hidden="true"></div>

    <!-- ─── Container-bound content layer (max-width matches site container) -->
    <div class="luma-hero__content-wrap">

        <!-- DISCOVER — ghost display word, top-left -->
        <div class="luma-hero__word luma-hero__word--top" aria-hidden="true">DISCOVER</div>

        <!-- GALLERY — dark display word, bottom-right, full visible -->
        <div class="luma-hero__word luma-hero__word--bottom" aria-hidden="true">GALLERY</div>

        <!-- Bottom-left intro block -->
        <div class="luma-hero__intro">
            <div class="luma-hero__intro-line" aria-hidden="true"></div>
            <p class="luma-hero__intro-label">
                Luma Gallery &mdash; <?php esc_html_e( 'Original Works', 'luma-gallery' ); ?>
            </p>
            <p class="luma-hero__intro-text">
                <?php esc_html_e( 'Explore immersive exhibitions, follow independent artists, and collect original artworks from a curated digital gallery.', 'luma-gallery' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="luma-hero__intro-cta">
                <?php esc_html_e( 'Enter the Gallery', 'luma-gallery' ); ?> &rarr;
            </a>
            <div class="luma-hero__intro-stats" aria-label="<?php esc_attr_e( 'Gallery statistics', 'luma-gallery' ); ?>">
                <span><strong data-count="120" data-suffix="+">120+</strong> <?php esc_html_e( 'Artworks', 'luma-gallery' ); ?></span>
                <span class="luma-hero__intro-sep" aria-hidden="true">/</span>
                <span><strong data-count="36">36</strong> <?php esc_html_e( 'Artists', 'luma-gallery' ); ?></span>
                <span class="luma-hero__intro-sep" aria-hidden="true">/</span>
                <span><strong data-count="8">8</strong> <?php esc_html_e( 'Exhibitions', 'luma-gallery' ); ?></span>
            </div>
        </div><!-- .luma-hero__intro -->

    </div><!-- .luma-hero__content-wrap -->

</section>

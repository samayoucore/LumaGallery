<?php
/**
 * Archive template for luma_artwork CPT — /gallery/ (archive URL).
 * Redirects to the Gallery page template if one exists, otherwise renders inline.
 */

defined( 'ABSPATH' ) || exit;

// If a page with the Gallery template exists, use that for the canonical URL
$gallery_page = get_posts( [
    'post_type'   => 'page',
    'meta_key'    => '_wp_page_template',
    'meta_value'  => 'templates/template-gallery.php',
    'numberposts' => 1,
    'post_status' => 'publish',
] );

if ( $gallery_page ) {
    wp_redirect( get_permalink( $gallery_page[0]->ID ), 301 );
    exit;
}

get_header();

$artworks   = function_exists( 'luma_get_demo_artworks' ) ? luma_get_demo_artworks( 12 ) : [];
$use_loop   = have_posts();
?>

<main class="luma-main luma-page luma-page--gallery" id="main-content">

    <section class="luma-page-hero luma-page-hero--gallery">
        <div class="luma-container">
            <div class="luma-page-hero__inner">
                <p class="luma-page-hero__eyebrow"><?php esc_html_e( 'Luma Gallery', 'luma-gallery' ); ?></p>
                <h1 class="luma-page-hero__title"><?php esc_html_e( 'Gallery', 'luma-gallery' ); ?></h1>
                <p class="luma-page-hero__subtitle"><?php esc_html_e( 'Browse curated artworks by mood, style, technique, and artist', 'luma-gallery' ); ?></p>
            </div>
        </div>
        <div class="luma-page-hero__word" aria-hidden="true">GALLERY</div>
    </section>

    <section class="luma-gallery-body">
        <div class="luma-container">
            <div class="luma-artwork-grid luma-artwork-grid--4col">
                <?php if ( $use_loop ) :
                    while ( have_posts() ) : the_post();
                        $pid  = get_the_ID();
                        $meta = function_exists( 'luma_get_artwork_meta' ) ? luma_get_artwork_meta( $pid ) : [];
                        get_template_part( 'template-parts/artwork-card', null, [
                            'post_id'     => $pid,
                            'artist_name' => $meta['artist'] ?? '',
                            'price'       => $meta['price']  ?? '',
                            'status'      => $meta['status'] ?? 'available',
                            'index'       => $wp_query->current_post,
                        ] );
                    endwhile;
                else :
                    foreach ( $artworks as $i => $aw ) :
                        get_template_part( 'template-parts/artwork-card', null, [
                            'post_id'     => $aw['id']       ?? 0,
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
            <?php the_posts_pagination( [ 'prev_text' => '&larr;', 'next_text' => '&rarr;', 'class' => 'luma-pagination' ] ); ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>

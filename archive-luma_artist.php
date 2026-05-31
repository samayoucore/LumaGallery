<?php
/**
 * Archive template for luma_artist CPT — /artists/
 */

defined( 'ABSPATH' ) || exit;

$artists_page = get_posts( [
    'post_type'   => 'page',
    'meta_key'    => '_wp_page_template',
    'meta_value'  => 'templates/template-artists.php',
    'numberposts' => 1,
    'post_status' => 'publish',
] );

if ( $artists_page ) {
    wp_redirect( get_permalink( $artists_page[0]->ID ), 301 );
    exit;
}

get_header();

$artists  = function_exists( 'luma_get_demo_artists' ) ? luma_get_demo_artists( 12 ) : [];
$use_loop = have_posts();
?>

<main class="luma-main" id="main-content">

    <section class="luma-page-hero luma-page-hero--artists">
        <div class="luma-container">
            <div class="luma-page-hero__inner">
                <p class="luma-page-hero__eyebrow"><?php esc_html_e( 'Luma Gallery', 'luma-gallery' ); ?></p>
                <h1 class="luma-page-hero__title"><?php esc_html_e( 'Artists', 'luma-gallery' ); ?></h1>
                <p class="luma-page-hero__subtitle"><?php esc_html_e( 'Meet the artists behind the works', 'luma-gallery' ); ?></p>
            </div>
        </div>
        <div class="luma-page-hero__word" aria-hidden="true">ARTISTS</div>
    </section>

    <section class="luma-artists-body">
        <div class="luma-artists-grid">
            <?php if ( $use_loop ) :
                while ( have_posts() ) : the_post();
                    $pid = get_the_ID();
                    get_template_part( 'template-parts/artist-card', null, [
                        'post_id'        => $pid,
                        'name'           => get_the_title(),
                        'location'       => get_post_meta( $pid, 'artist_location', true )  ?: '',
                        'short_bio'      => get_post_meta( $pid, 'artist_short_bio', true ) ?: '',
                        'artworks_count' => (int) get_post_meta( $pid, 'artist_artworks_count', true ),
                        'index'          => $wp_query->current_post,
                    ] );
                endwhile;
            else :
                foreach ( $artists as $i => $a ) :
                    get_template_part( 'template-parts/artist-card', null, [
                        'post_id'        => $a['id']       ?? 0,
                        'name'           => $a['name']     ?? '',
                        'location'       => $a['location'] ?? '',
                        'short_bio'      => $a['bio']      ?? '',
                        'artworks_count' => $a['artworks'] ?? 0,
                        'gradient'       => $a['gradient'] ?? '',
                        'index'          => $i,
                    ] );
                endforeach;
            endif; ?>
        </div>
        <?php the_posts_pagination( [ 'prev_text' => '&larr;', 'next_text' => '&rarr;', 'class' => 'luma-pagination' ] ); ?>
    </section>

</main>

<?php get_footer(); ?>

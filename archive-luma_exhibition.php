<?php
/**
 * Archive template for luma_exhibition CPT — /exhibitions/
 */

defined( 'ABSPATH' ) || exit;

$exh_page = get_posts( [
    'post_type'   => 'page',
    'meta_key'    => '_wp_page_template',
    'meta_value'  => 'templates/template-exhibitions.php',
    'numberposts' => 1,
    'post_status' => 'publish',
] );

if ( $exh_page ) {
    wp_redirect( get_permalink( $exh_page[0]->ID ), 301 );
    exit;
}

get_header();

$exhibitions = function_exists( 'luma_get_demo_exhibitions' ) ? luma_get_demo_exhibitions( 8 ) : [];
$use_loop    = have_posts();
?>

<main class="luma-main" id="main-content">

    <section class="luma-page-hero luma-page-hero--exhibitions">
        <div class="luma-container">
            <div class="luma-page-hero__inner">
                <p class="luma-page-hero__eyebrow"><?php esc_html_e( 'Luma Gallery', 'luma-gallery' ); ?></p>
                <h1 class="luma-page-hero__title"><?php esc_html_e( 'Exhibitions', 'luma-gallery' ); ?></h1>
                <p class="luma-page-hero__subtitle"><?php esc_html_e( 'Explore artworks through mood, theme, and visual storytelling', 'luma-gallery' ); ?></p>
            </div>
        </div>
        <div class="luma-page-hero__word" aria-hidden="true">EXHIBITIONS</div>
    </section>

    <section class="luma-exhibitions-body">
        <div class="luma-exhibitions-body__header">
            <h2 class="luma-exhibitions-body__title"><?php esc_html_e( 'All Exhibitions', 'luma-gallery' ); ?></h2>
        </div>
        <div class="luma-exhibitions-grid">
            <?php if ( $use_loop ) :
                while ( have_posts() ) : the_post();
                    $pid = get_the_ID();
                    get_template_part( 'template-parts/exhibition-card', null, [
                        'post_id'      => $pid,
                        'title'        => get_the_title(),
                        'subtitle'     => get_post_meta( $pid, 'exhibition_subtitle', true )     ?: '',
                        'curator_note' => get_post_meta( $pid, 'exhibition_curator_note', true ) ?: '',
                        'works_count'  => (int) get_post_meta( $pid, 'exhibition_works_count', true ),
                        'mood_tags'    => wp_get_post_terms( $pid, 'luma_mood', [ 'fields' => 'names' ] ) ?: [],
                        'index'        => $wp_query->current_post,
                    ] );
                endwhile;
            else :
                foreach ( $exhibitions as $i => $ex ) :
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
                endforeach;
            endif; ?>
        </div>
        <?php the_posts_pagination( [ 'prev_text' => '&larr;', 'next_text' => '&rarr;', 'class' => 'luma-pagination' ] ); ?>
    </section>

</main>

<?php get_footer(); ?>

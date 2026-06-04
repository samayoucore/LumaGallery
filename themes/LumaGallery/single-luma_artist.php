<?php
/**
 * Single Artist — luma_artist CPT.
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
$name        = get_the_title();
$location    = get_post_meta( $post_id, 'artist_location',    true ) ?: '';
$short_bio   = get_post_meta( $post_id, 'artist_short_bio',   true ) ?: get_the_excerpt();
$statement   = get_post_meta( $post_id, 'artist_statement',   true ) ?: '';
$biography   = get_the_content();
$aw_count    = (int) get_post_meta( $post_id, 'artist_artworks_count',   true );
$followers   = (int) get_post_meta( $post_id, 'artist_followers_count',  true );
$sold_count  = (int) get_post_meta( $post_id, 'artist_sold_count',       true );
$insta       = get_post_meta( $post_id, 'artist_instagram',  true ) ?: '';
$website     = get_post_meta( $post_id, 'artist_website',    true ) ?: '';
$cover_id    = (int) get_post_meta( $post_id, 'artist_cover_image_id',  true );
$cover_src   = $cover_id ? wp_get_attachment_image_url( $cover_id, 'luma-artist-cover' ) : '';
$avatar_src  = get_the_post_thumbnail_url( $post_id, 'luma-artist-avatar' ) ?: '';
$gradient    = function_exists( 'luma_get_placeholder_gradient' ) ? luma_get_placeholder_gradient( 0 ) : '';
$initial     = mb_substr( $name, 0, 1 );

// Artworks by this artist
$cpt = class_exists( 'WooCommerce' ) ? 'product' : 'luma_artwork';
$artworks_q = new WP_Query( [
    'post_type'      => $cpt,
    'posts_per_page' => 8,
    'post_status'    => 'publish',
    'meta_query'     => [[ 'key' => 'artwork_artist', 'value' => $name, 'compare' => '=' ]],
] );
$has_real_artworks = $artworks_q->have_posts();

// Demo artworks as fallback
$demo_artworks = [];
if ( ! $has_real_artworks && function_exists( 'luma_get_demo_artworks' ) ) {
    $demo_artworks = luma_get_demo_artworks( 4 );
}

// Similar artists (demo)
$similar_artists = function_exists( 'luma_get_demo_artists' ) ? luma_get_demo_artists( 3 ) : [];
$similar_artists = array_filter( $similar_artists, fn( $a ) => ( $a['id'] ?? 0 ) !== $post_id );
$similar_artists = array_values( $similar_artists );
?>

<main class="luma-main luma-artist-page" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-artist-hero" aria-label="<?php echo esc_attr( $name ); ?>">

        <?php if ( $cover_src ) : ?>
            <div class="luma-artist-hero__cover">
                <img src="<?php echo esc_url( $cover_src ); ?>" alt="" aria-hidden="true">
            </div>
        <?php else : ?>
            <div class="luma-artist-hero__cover-placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;position:absolute;inset:0;opacity:.3;"></div>
        <?php endif; ?>

        <div class="luma-artist-hero__gradient" aria-hidden="true"></div>

        <div class="luma-artist-hero__content">

            <?php if ( $avatar_src ) : ?>
                <div class="luma-artist-hero__avatar">
                    <img src="<?php echo esc_url( $avatar_src ); ?>" alt="<?php echo esc_attr( $name ); ?>">
                </div>
            <?php else : ?>
                <div class="luma-artist-hero__avatar-placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;">
                    <span><?php echo esc_html( $initial ); ?></span>
                </div>
            <?php endif; ?>

            <div class="luma-artist-hero__meta">
                <h1 class="luma-artist-hero__name"><?php echo esc_html( $name ); ?></h1>

                <?php if ( $location ) : ?>
                    <p class="luma-artist-hero__location">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo esc_html( $location ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $short_bio ) : ?>
                    <p class="luma-artist-hero__bio"><?php echo esc_html( wp_trim_words( $short_bio, 24 ) ); ?></p>
                <?php endif; ?>

                <div class="luma-artist-hero__stats">
                    <?php if ( $aw_count ) : ?>
                        <div class="luma-artist-hero__stat">
                            <strong><?php echo esc_html( $aw_count ); ?></strong>
                            <span><?php esc_html_e( 'Works', 'luma-gallery' ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ( $followers ) : ?>
                        <div class="luma-artist-hero__stat">
                            <strong><?php echo esc_html( number_format( $followers ) ); ?></strong>
                            <span><?php esc_html_e( 'Followers', 'luma-gallery' ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ( $sold_count ) : ?>
                        <div class="luma-artist-hero__stat">
                            <strong><?php echo esc_html( $sold_count ); ?></strong>
                            <span><?php esc_html_e( 'Sold', 'luma-gallery' ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="luma-artist-hero__actions">
                    <button class="luma-button luma-button--ghost-light js-toggle-favorite"
                        data-artist-id="<?php echo esc_attr( $post_id ); ?>"
                        data-type="artist"
                        aria-pressed="false">
                        <?php esc_html_e( 'Follow Artist', 'luma-gallery' ); ?>
                    </button>
                    <?php if ( $website ) : ?>
                        <a href="<?php echo esc_url( $website ); ?>" class="luma-button luma-button--ghost-light" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'Contact', 'luma-gallery' ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ( $insta || $website ) : ?>
                    <div class="luma-artist-hero__social">
                        <?php if ( $insta ) : ?>
                            <a href="https://instagram.com/<?php echo esc_attr( ltrim( $insta, '@' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ( $website ) : ?>
                            <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Website', 'luma-gallery' ); ?>">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div><!-- .luma-artist-hero__meta -->

        </div><!-- .luma-artist-hero__content -->

    </section>

    <!-- ─── Body: bio + statement ─────────────────────────────────────────── -->
    <div class="luma-artist-body">

        <div class="luma-artist-main">

            <?php if ( $biography ) : ?>
                <div class="luma-artist-section">
                    <h2 class="luma-artist-section__title"><?php esc_html_e( 'Biography', 'luma-gallery' ); ?></h2>
                    <?php echo wp_kses_post( $biography ); ?>
                </div>
            <?php endif; ?>

            <?php if ( $statement ) : ?>
                <div class="luma-artist-section">
                    <h2 class="luma-artist-section__title"><?php esc_html_e( 'Artist Statement', 'luma-gallery' ); ?></h2>
                    <blockquote><p><?php echo wp_kses_post( $statement ); ?></p></blockquote>
                </div>
            <?php elseif ( ! $biography ) : ?>
                <div class="luma-artist-section">
                    <h2 class="luma-artist-section__title"><?php esc_html_e( 'About', 'luma-gallery' ); ?></h2>
                    <p><?php echo esc_html( $short_bio ?: __( 'Artist profile coming soon.', 'luma-gallery' ) ); ?></p>
                </div>
            <?php endif; ?>

        </div>

        <div class="luma-artist-aside">
            <!-- sidebar reserved for related posts / quick stats in future -->
        </div>

    </div><!-- .luma-artist-body -->

    <!-- ─── Artworks by this artist ───────────────────────────────────────── -->
    <section class="luma-artist-artworks">
        <div class="luma-artist-artworks__header">
            <h2 class="luma-artist-artworks__title">
                <?php printf( esc_html__( 'Artworks by %s', 'luma-gallery' ), esc_html( $name ) ); ?>
            </h2>
            <?php if ( $has_real_artworks ) : ?>
                <span class="luma-artist-artworks__count"><?php echo esc_html( $artworks_q->found_posts ); ?></span>
            <?php endif; ?>
        </div>
        <div class="luma-artist-artworks__grid">
            <?php if ( $has_real_artworks ) :
                while ( $artworks_q->have_posts() ) : $artworks_q->the_post();
                    $pid  = get_the_ID();
                    $meta = function_exists( 'luma_get_artwork_meta' ) ? luma_get_artwork_meta( $pid ) : [];
                    get_template_part( 'template-parts/artwork-card', null, [
                        'post_id'     => $pid,
                        'artist_name' => $name,
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
                        'artist_name' => $name,
                        'price'       => $aw['price']    ?? '',
                        'status'      => $aw['status']   ?? 'available',
                        'gradient'    => $aw['gradient'] ?? '',
                        'index'       => $i,
                    ] );
                endforeach;
            endif; ?>
        </div>
    </section>

    <!-- ─── Similar artists ──────────────────────────────────────────────── -->
    <?php if ( ! empty( $similar_artists ) ) : ?>
        <section class="luma-artist-similar">
            <div class="luma-artist-similar__header">
                <h2 class="luma-artist-similar__title"><?php esc_html_e( 'Similar Artists', 'luma-gallery' ); ?></h2>
            </div>
            <div class="luma-artist-similar__grid">
                <?php foreach ( array_slice( $similar_artists, 0, 3 ) as $i => $sa ) :
                    get_template_part( 'template-parts/artist-card', null, [
                        'post_id'        => $sa['id']       ?? 0,
                        'name'           => $sa['name']     ?? '',
                        'location'       => $sa['location'] ?? '',
                        'short_bio'      => $sa['bio']      ?? '',
                        'artworks_count' => $sa['artworks'] ?? 0,
                        'gradient'       => $sa['gradient'] ?? '',
                        'index'          => $i,
                    ] );
                endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>

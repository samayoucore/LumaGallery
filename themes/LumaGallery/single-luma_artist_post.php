<?php
/**
 * Single: Journal post (luma_artist_post).
 *
 * Editorial article layout — hero, reading column, artist block, related
 * artworks and more stories. Degrades to demo data where relations are absent.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Fallback demo artworks for the "Related artworks" rail ────────────────────
$related_artworks = [
    [ 'title' => 'Silent Morning', 'artist' => 'Elena Morozova', 'price' => '€ 1 200', 'price_raw' => 1200, 'gradient' => 'linear-gradient(135deg,#2C2C2A,#4A4845)' ],
    [ 'title' => 'Soft Gravity',   'artist' => 'Anna Weiss',     'price' => '€ 750',   'price_raw' => 750,  'gradient' => 'linear-gradient(145deg,#2A1E1A,#4A3530)' ],
    [ 'title' => 'Blue Interior',  'artist' => 'Victor Hale',    'price' => '€ 980',   'price_raw' => 980,  'gradient' => 'linear-gradient(160deg,#1E2A3A,#2E4A5A)' ],
];

while ( have_posts() ) :
    the_post();

    $post_id = get_the_ID();

    // Topic
    $topic_label = '';
    $terms = get_the_terms( $post_id, 'luma_post_topic' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        $topic_label = $terms[0]->name;
    }

    // By-line / artist
    $artist = get_post_meta( $post_id, 'luma_post_artist', true );
    if ( ! $artist ) {
        $artist = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) );
    }
    $artist_bio = get_the_author_meta( 'description', (int) get_post_field( 'post_author', $post_id ) );
    if ( ! $artist_bio ) {
        $artist_bio = __( 'An artist featured in the Luma Gallery — exploring mood, material and quiet through original work.', 'luma-gallery' );
    }
    $artist_url     = home_url( '/artists/' );
    $artist_initial = $artist ? mb_substr( $artist, 0, 1 ) : 'L';

    $gradient = function_exists( 'luma_get_placeholder_gradient' ) ? luma_get_placeholder_gradient( $post_id ) : 'linear-gradient(135deg,#2C2C2A,#4A4845)';
    ?>

    <main class="luma-main luma-page luma-page--journal-single" id="main-content">

        <!-- ─── Hero ─────────────────────────────────────────────────────── -->
        <article class="luma-post">

            <header class="luma-post-hero">
                <div class="luma-container luma-container--narrow">
                    <?php if ( $topic_label ) : ?>
                        <span class="luma-post-hero__topic"><?php echo esc_html( $topic_label ); ?></span>
                    <?php endif; ?>
                    <h1 class="luma-post-hero__title"><?php the_title(); ?></h1>
                    <?php if ( has_excerpt() ) : ?>
                        <p class="luma-post-hero__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                    <?php endif; ?>
                    <div class="luma-post-hero__meta">
                        <span class="luma-post-hero__date"><?php echo esc_html( get_the_date() ); ?></span>
                        <?php if ( $artist ) : ?>
                            <span class="luma-post-hero__dot" aria-hidden="true">·</span>
                            <span class="luma-post-hero__author"><?php echo esc_html( $artist ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="luma-post-hero__media">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'luma-hero', [ 'class' => 'luma-post-hero__image' ] ); ?>
                    <?php else : ?>
                        <span class="luma-post-hero__placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;" aria-hidden="true"></span>
                    <?php endif; ?>
                </div>
            </header>

            <!-- ─── Article body ─────────────────────────────────────────── -->
            <div class="luma-post-content luma-container luma-container--narrow">
                <?php
                the_content();

                wp_link_pages( [
                    'before' => '<div class="luma-post-content__pages">',
                    'after'  => '</div>',
                ] );
                ?>
            </div>

            <!-- ─── Artist block ─────────────────────────────────────────── -->
            <aside class="luma-post-artist">
                <div class="luma-container luma-container--narrow">
                    <div class="luma-post-artist__inner">
                        <div class="luma-post-artist__avatar" style="background:<?php echo esc_attr( $gradient ); ?>;" aria-hidden="true">
                            <span class="luma-post-artist__initial"><?php echo esc_html( $artist_initial ); ?></span>
                        </div>
                        <div class="luma-post-artist__detail">
                            <p class="luma-post-artist__eyebrow"><?php esc_html_e( 'About the artist', 'luma-gallery' ); ?></p>
                            <h2 class="luma-post-artist__name"><?php echo esc_html( $artist ?: __( 'Luma Artist', 'luma-gallery' ) ); ?></h2>
                            <p class="luma-post-artist__bio"><?php echo esc_html( $artist_bio ); ?></p>
                            <a href="<?php echo esc_url( $artist_url ); ?>" class="luma-button luma-button--ghost luma-button--sm">
                                <?php esc_html_e( 'View Artist', 'luma-gallery' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

        </article>

        <!-- ─── Related artworks ─────────────────────────────────────────── -->
        <section class="luma-post-related">
            <div class="luma-container">
                <div class="luma-post-related__header">
                    <h2 class="luma-post-related__title"><?php esc_html_e( 'Related artworks', 'luma-gallery' ); ?></h2>
                    <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="luma-post-related__link"><?php esc_html_e( 'View all', 'luma-gallery' ); ?></a>
                </div>
                <div class="luma-artwork-grid luma-artwork-grid--3col">
                    <?php foreach ( $related_artworks as $i => $aw ) : ?>
                        <?php get_template_part( 'template-parts/artwork-card', null, [
                            'post_id'     => 0,
                            'title'       => $aw['title'],
                            'artist_name' => $aw['artist'],
                            'price'       => $aw['price'],
                            'price_raw'   => $aw['price_raw'],
                            'status'      => 'available',
                            'gradient'    => $aw['gradient'],
                            'index'       => $i,
                        ] ); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ─── More stories ─────────────────────────────────────────────── -->
        <?php
        $more_views = [];
        $more_q = new WP_Query( [
            'post_type'      => 'luma_artist_post',
            'posts_per_page' => 3,
            'post__not_in'   => [ $post_id ],
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        if ( $more_q->have_posts() ) {
            foreach ( $more_q->posts as $mp ) {
                $more_views[] = luma_core_normalize_post( $mp );
            }
            wp_reset_postdata();
        } elseif ( function_exists( 'luma_get_demo_artist_posts' ) ) {
            $demo = luma_get_demo_artist_posts();
            // Skip any demo entry sharing this title, then take 3.
            $current_title = get_the_title( $post_id );
            foreach ( $demo as $d ) {
                if ( $d['title'] !== $current_title ) {
                    $more_views[] = $d;
                }
                if ( count( $more_views ) >= 3 ) {
                    break;
                }
            }
        }
        ?>
        <?php if ( ! empty( $more_views ) ) : ?>
            <section class="luma-post-more">
                <div class="luma-container">
                    <h2 class="luma-post-more__title"><?php esc_html_e( 'More stories', 'luma-gallery' ); ?></h2>
                    <div class="luma-post-grid">
                        <?php foreach ( $more_views as $i => $view ) : ?>
                            <?php
                            $view['index'] = $i;
                            get_template_part( 'template-parts/journal-card', null, $view );
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- ─── Closing CTA ──────────────────────────────────────────────── -->
        <section class="luma-post-cta">
            <div class="luma-container">
                <div class="luma-post-cta__inner">
                    <h2 class="luma-post-cta__title"><?php esc_html_e( 'Discover original artworks', 'luma-gallery' ); ?></h2>
                    <p class="luma-post-cta__text"><?php esc_html_e( 'Browse the full collection of one-of-one works by contemporary artists.', 'luma-gallery' ); ?></p>
                    <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="luma-button luma-button--accent luma-button--lg">
                        <?php esc_html_e( 'Explore the Gallery', 'luma-gallery' ); ?>
                    </a>
                </div>
            </div>
        </section>

    </main>

<?php
endwhile;

get_footer();

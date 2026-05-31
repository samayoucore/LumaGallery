<?php
/**
 * Single Artwork — luma_artwork CPT.
 * Works without WooCommerce; graceful fallback for missing meta.
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
$meta         = function_exists( 'luma_get_artwork_meta' ) ? luma_get_artwork_meta( $post_id ) : [];
$title        = get_the_title();
$artist       = $meta['artist']    ?? '';
$price        = $meta['price']     ?? '';
$price_raw    = (float) get_post_meta( $post_id, class_exists( 'WooCommerce' ) ? '_price' : 'artwork_price', true );
$status       = $meta['status']    ?? 'available';
$year         = $meta['year']      ?? '';
$width        = $meta['width']     ?? '';
$height       = $meta['height']    ?? '';
$material     = $meta['material']  ?? '';
$technique    = $meta['technique'] ?? '';
$story        = $meta['story']     ?? '';
$description  = get_the_content();
$permalink    = get_permalink();
$thumb_src    = get_the_post_thumbnail_url( $post_id, 'luma-artwork-single' ) ?: '';
$gradient     = function_exists( 'luma_get_placeholder_gradient' ) ? luma_get_placeholder_gradient( 0 ) : '';
$dimensions   = function_exists( 'luma_format_dimensions' ) ? luma_format_dimensions( $width, $height ) : ( $width && $height ? esc_html( "$width × $height cm" ) : '' );

// Terms
$style_terms  = function_exists( 'luma_get_artwork_terms' ) ? luma_get_artwork_terms( $post_id, 'luma_style' ) : [];
$mood_terms   = function_exists( 'luma_get_artwork_terms' ) ? luma_get_artwork_terms( $post_id, 'luma_mood' )  : [];
$all_terms    = array_merge( $style_terms, $mood_terms );

// Status labels
$status_labels = [
    'available' => __( 'Available', 'luma-gallery' ),
    'reserved'  => __( 'Reserved',  'luma-gallery' ),
    'sold'      => __( 'Sold',      'luma-gallery' ),
];
$status_label = $status_labels[ $status ] ?? $status_labels['available'];

// Artist post (if exists)
$artist_posts = new WP_Query( [
    'post_type'      => 'luma_artist',
    'posts_per_page' => 1,
    'meta_key'       => 'artist_name',
    'meta_value'     => $artist,
    'post_status'    => 'publish',
] );
$artist_post = $artist_posts->have_posts() ? $artist_posts->posts[0] : null;
wp_reset_postdata();

// Similar artworks (demo)
$similar_artworks = function_exists( 'luma_get_demo_artworks' ) ? luma_get_demo_artworks( 4 ) : [];

// Recently viewed stored in localStorage — trigger via data attr
$recently_viewed_data = wp_json_encode( [
    'id'       => $post_id,
    'title'    => $title,
    'artist'   => $artist,
    'price'    => $price,
    'url'      => $permalink,
    'src'      => $thumb_src,
    'gradient' => $gradient,
] );
?>

<main class="luma-main" id="main-content" data-recently-viewed="<?php echo esc_attr( $recently_viewed_data ); ?>">

    <!-- ─── Hero: image + info ────────────────────────────────────────────── -->
    <div class="luma-artwork-hero">

        <!-- Image panel -->
        <div class="luma-artwork-hero__image-panel">
            <?php if ( $thumb_src ) : ?>
                <img src="<?php echo esc_url( $thumb_src ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="eager" decoding="async">
            <?php else : ?>
                <div class="luma-artwork-hero__placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;"></div>
            <?php endif; ?>
            <button class="luma-artwork-hero__zoom" aria-label="<?php esc_attr_e( 'Zoom image', 'luma-gallery' ); ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
            </button>
        </div>

        <!-- Info panel -->
        <div class="luma-artwork-hero__info">
            <p class="luma-artwork-hero__eyebrow"><?php esc_html_e( 'Original Artwork', 'luma-gallery' ); ?></p>
            <h1 class="luma-artwork-hero__title"><?php echo esc_html( $title ); ?></h1>
            <p class="luma-artwork-hero__artist">
                <?php esc_html_e( 'by', 'luma-gallery' ); ?>
                <?php if ( $artist_post ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $artist_post->ID ) ); ?>"><?php echo esc_html( $artist ); ?></a>
                <?php else : ?>
                    <?php echo esc_html( $artist ); ?>
                <?php endif; ?>
            </p>

            <?php if ( $price ) : ?>
                <p class="luma-artwork-hero__price"><?php echo esc_html( $price ); ?></p>
            <?php endif; ?>

            <div class="luma-artwork-hero__status">
                <span class="luma-badge luma-badge--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status_label ); ?></span>
            </div>

            <!-- Actions -->
            <div class="luma-artwork-hero__actions">
                <?php if ( $status === 'available' ) : ?>
                    <?php if ( luma_is_woocommerce_active() ) : ?>
                        <?php do_action( 'woocommerce_single_product_summary' ); ?>
                    <?php else : ?>
                        <button class="luma-button luma-button--accent js-add-to-cart"
                            data-artwork-id="<?php echo esc_attr( $post_id ); ?>"
                            data-price="<?php echo esc_attr( $price_raw ); ?>">
                            <?php esc_html_e( 'Add to Cart', 'luma-gallery' ); ?>
                        </button>
                        <button class="luma-button luma-button--primary js-add-to-cart"
                            data-artwork-id="<?php echo esc_attr( $post_id ); ?>"
                            data-price="<?php echo esc_attr( $price_raw ); ?>">
                            <?php esc_html_e( 'Buy Now', 'luma-gallery' ); ?>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <button class="luma-button luma-button--ghost js-toggle-favorite"
                    data-artwork-id="<?php echo esc_attr( $post_id ); ?>"
                    data-type="artwork"
                    aria-pressed="false"
                    aria-label="<?php esc_attr_e( 'Add to favorites', 'luma-gallery' ); ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <?php esc_html_e( 'Favorite', 'luma-gallery' ); ?>
                </button>

                <button class="luma-button luma-button--ghost js-view-in-room"
                    data-artwork-src="<?php echo esc_attr( $thumb_src ); ?>"
                    data-artwork-title="<?php echo esc_attr( $title ); ?>"
                    data-artwork-size="<?php echo esc_attr( $dimensions ); ?>"
                    data-artwork-url="<?php echo esc_url( $permalink ); ?>"
                    aria-label="<?php esc_attr_e( 'View in room', 'luma-gallery' ); ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    <?php esc_html_e( 'View in Room', 'luma-gallery' ); ?>
                </button>
            </div>

            <!-- Quick specs -->
            <dl class="luma-artwork-specs">
                <?php if ( $dimensions ) : ?>
                    <div class="luma-artwork-specs__item"><dt><?php esc_html_e( 'Size', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $dimensions ); ?></dd></div>
                <?php endif; ?>
                <?php if ( $technique ) : ?>
                    <div class="luma-artwork-specs__item"><dt><?php esc_html_e( 'Technique', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $technique ); ?></dd></div>
                <?php endif; ?>
                <?php if ( $material ) : ?>
                    <div class="luma-artwork-specs__item"><dt><?php esc_html_e( 'Material', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $material ); ?></dd></div>
                <?php endif; ?>
                <?php if ( $year ) : ?>
                    <div class="luma-artwork-specs__item"><dt><?php esc_html_e( 'Year', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $year ); ?></dd></div>
                <?php endif; ?>
            </dl>

        </div><!-- .luma-artwork-hero__info -->

    </div><!-- .luma-artwork-hero -->

    <!-- ─── Body sections ────────────────────────────────────────────────── -->
    <div class="luma-artwork-body">

        <div class="luma-artwork-body__main">

            <!-- Description -->
            <?php if ( $description ) : ?>
                <div class="luma-artwork-section">
                    <h2 class="luma-artwork-section__title"><?php esc_html_e( 'Description', 'luma-gallery' ); ?></h2>
                    <div class="luma-artwork-section__text">
                        <?php echo wp_kses_post( $description ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Story -->
            <?php if ( $story ) : ?>
                <div class="luma-artwork-section">
                    <h2 class="luma-artwork-section__title"><?php esc_html_e( 'Story Behind the Artwork', 'luma-gallery' ); ?></h2>
                    <div class="luma-artwork-section__text">
                        <p><?php echo wp_kses_post( $story ); ?></p>
                    </div>
                </div>
            <?php else : ?>
                <div class="luma-artwork-section">
                    <h2 class="luma-artwork-section__title"><?php esc_html_e( 'Story Behind the Artwork', 'luma-gallery' ); ?></h2>
                    <div class="luma-artwork-section__text">
                        <p><?php esc_html_e( 'Every work carries a world within it. This piece emerged from a moment of stillness — a quiet observation of light, form, and feeling that the artist chose to preserve. The process was unhurried: layer after layer, the composition found its own voice.', 'luma-gallery' ); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Details -->
            <div class="luma-artwork-section">
                <h2 class="luma-artwork-section__title"><?php esc_html_e( 'Details', 'luma-gallery' ); ?></h2>
                <dl class="luma-artwork-details">
                    <?php if ( $artist )    : ?><div class="luma-artwork-details__row"><dt><?php esc_html_e( 'Artist', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $artist ); ?></dd></div><?php endif; ?>
                    <?php if ( $year )      : ?><div class="luma-artwork-details__row"><dt><?php esc_html_e( 'Year', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $year ); ?></dd></div><?php endif; ?>
                    <?php if ( $technique ) : ?><div class="luma-artwork-details__row"><dt><?php esc_html_e( 'Technique', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $technique ); ?></dd></div><?php endif; ?>
                    <?php if ( $material )  : ?><div class="luma-artwork-details__row"><dt><?php esc_html_e( 'Material', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $material ); ?></dd></div><?php endif; ?>
                    <?php if ( $dimensions ): ?><div class="luma-artwork-details__row"><dt><?php esc_html_e( 'Dimensions', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $dimensions ); ?></dd></div><?php endif; ?>
                    <?php if ( $status )    : ?><div class="luma-artwork-details__row"><dt><?php esc_html_e( 'Status', 'luma-gallery' ); ?></dt><dd><?php echo esc_html( $status_label ); ?></dd></div><?php endif; ?>
                </dl>
            </div>

            <!-- Tags -->
            <?php if ( ! empty( $all_terms ) ) : ?>
                <div class="luma-artwork-section">
                    <h2 class="luma-artwork-section__title"><?php esc_html_e( 'Tags', 'luma-gallery' ); ?></h2>
                    <div class="luma-artwork-tags">
                        <?php foreach ( $all_terms as $term ) : ?>
                            <span class="luma-tag"><?php echo esc_html( $term ); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div><!-- .luma-artwork-body__main -->

        <!-- Aside: artist preview -->
        <div class="luma-artwork-body__aside">
            <?php if ( $artist ) : ?>
                <div class="luma-artwork-artist-preview">
                    <p class="luma-artwork-artist-preview__label"><?php esc_html_e( 'About the Artist', 'luma-gallery' ); ?></p>
                    <div class="luma-artwork-artist-preview__inner">
                        <div class="luma-artwork-artist-preview__avatar">
                            <?php if ( $artist_post && has_post_thumbnail( $artist_post->ID ) ) : ?>
                                <?php echo get_the_post_thumbnail( $artist_post->ID, 'thumbnail', [ 'alt' => esc_attr( $artist ) ] ); ?>
                            <?php else : ?>
                                <?php echo esc_html( mb_substr( $artist, 0, 1 ) ); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="luma-artwork-artist-preview__name">
                                <?php if ( $artist_post ) : ?>
                                    <a href="<?php echo esc_url( get_permalink( $artist_post->ID ) ); ?>"><?php echo esc_html( $artist ); ?></a>
                                <?php else : ?>
                                    <?php echo esc_html( $artist ); ?>
                                <?php endif; ?>
                            </h3>
                            <?php
                            $artist_location = $artist_post ? get_post_meta( $artist_post->ID, 'artist_location', true ) : '';
                            if ( $artist_location ) : ?>
                                <p class="luma-artwork-artist-preview__location"><?php echo esc_html( $artist_location ); ?></p>
                            <?php endif; ?>
                            <?php
                            $artist_bio = $artist_post ? ( get_post_meta( $artist_post->ID, 'artist_short_bio', true ) ?: get_the_excerpt( $artist_post->ID ) ) : '';
                            if ( $artist_bio ) : ?>
                                <p class="luma-artwork-artist-preview__bio"><?php echo esc_html( wp_trim_words( $artist_bio, 20 ) ); ?></p>
                            <?php endif; ?>
                            <?php if ( $artist_post ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $artist_post->ID ) ); ?>" class="luma-button luma-button--ghost luma-button--sm">
                                    <?php esc_html_e( 'View Profile', 'luma-gallery' ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- .luma-artwork-body -->

    <!-- ─── Similar artworks ──────────────────────────────────────────────── -->
    <?php if ( ! empty( $similar_artworks ) ) : ?>
        <section class="luma-artwork-similar">
            <div class="luma-artwork-similar__header">
                <h2 class="luma-artwork-similar__title"><?php esc_html_e( 'Similar Artworks', 'luma-gallery' ); ?></h2>
                <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="luma-button luma-button--ghost luma-button--sm">
                    <?php esc_html_e( 'View all', 'luma-gallery' ); ?>
                </a>
            </div>
            <div class="luma-artwork-similar__grid">
                <?php foreach ( $similar_artworks as $i => $aw ) :
                    if ( ( $aw['id'] ?? 0 ) === $post_id ) continue;
                    get_template_part( 'template-parts/artwork-card', null, [
                        'post_id'     => $aw['id']       ?? 0,
                        'title'       => $aw['title']    ?? '',
                        'artist_name' => $aw['artist']   ?? '',
                        'price'       => $aw['price']    ?? '',
                        'status'      => $aw['status']   ?? 'available',
                        'gradient'    => $aw['gradient'] ?? '',
                        'index'       => $i,
                    ] );
                endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- ─── Recently viewed (JS-populated) ───────────────────────────────── -->
    <section class="luma-recently-viewed" style="display:none;" id="recently-viewed">
        <div class="luma-recently-viewed__header">
            <h2 class="luma-recently-viewed__title"><?php esc_html_e( 'Recently Viewed', 'luma-gallery' ); ?></h2>
        </div>
        <div class="luma-recently-viewed__grid js-recently-viewed-grid"></div>
    </section>

    <?php get_template_part( 'template-parts/modal-view-in-room' ); ?>

</main>

<?php get_footer(); ?>

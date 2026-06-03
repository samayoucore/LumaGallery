<?php
/**
 * Artwork card partial.
 *
 * Accepts context via $args:
 *   - post_id      (int)    WP post ID
 *   - title        (string)
 *   - artist_name  (string)
 *   - price        (string) formatted price with currency
 *   - status       (string) available | reserved | sold
 *   - gradient     (string) fallback CSS gradient
 *   - index        (int)    card index for staggered animation
 *   - featured     (bool)   larger featured card
 */

$args       = $args ?? [];
$post_id    = (int) ( $args['post_id'] ?? get_the_ID() );
$title      = $args['title']       ?? get_the_title( $post_id );
$artist     = $args['artist_name'] ?? '';
$price      = $args['price']       ?? '';
$status     = $args['status']      ?? 'available';
$gradient   = $args['gradient']    ?? luma_get_placeholder_gradient( $args['index'] ?? 0 );
$index      = (int) ( $args['index'] ?? 0 );
$featured   = (bool) ( $args['featured'] ?? false );
$permalink  = get_permalink( $post_id ) ?: '#';

// Filter data attributes
$style     = $args['style']     ?? '';
$mood      = $args['mood']      ?? '';
$technique = $args['technique'] ?? '';
$color     = $args['color']     ?? '';
$room      = $args['room']      ?? '';   // optional — used by the AI Curator
$size      = $args['size']      ?? '';   // optional — used by the AI Curator
$price_raw = (float) ( $args['price_raw'] ?? 0 );
$date      = (int) ( $args['date'] ?? ( $post_id ? (int) get_post_time( 'U', false, $post_id ) : 0 ) );

// Stable favorite id: real post ID, or a slug for demo cards (post_id 0) so
// each demo artwork can be saved independently in localStorage.
$fav_id = $post_id > 0 ? (string) $post_id : 'demo-' . sanitize_title( $title . '-' . $artist );

// Demo-cart data
$thumb_url = ( $post_id && has_post_thumbnail( $post_id ) ) ? (string) get_the_post_thumbnail_url( $post_id, 'luma-artwork-card' ) : '';
$currency  = ( luma_is_woocommerce_active() && function_exists( 'get_woocommerce_currency_symbol' ) ) ? get_woocommerce_currency_symbol() : '€';

$status_labels = [
    'available' => __( 'Available', 'luma-gallery' ),
    'reserved'  => __( 'Reserved', 'luma-gallery' ),
    'sold'      => __( 'Sold', 'luma-gallery' ),
];
$status_label = $status_labels[ $status ] ?? $status_labels['available'];
?>

<article
    class="luma-artwork-card<?php echo $featured ? ' luma-artwork-card--featured' : ''; ?>"
    data-artwork-id="<?php echo esc_attr( $fav_id ); ?>"
    data-index="<?php echo esc_attr( $index ); ?>"
    data-title="<?php echo esc_attr( trim( $title . ' ' . $artist ) ); ?>"
    data-artist="<?php echo esc_attr( $artist ); ?>"
    data-price="<?php echo esc_attr( $price_raw ); ?>"
    data-style="<?php echo esc_attr( $style ); ?>"
    data-mood="<?php echo esc_attr( $mood ); ?>"
    data-technique="<?php echo esc_attr( $technique ); ?>"
    data-color="<?php echo esc_attr( $color ); ?>"
    data-room="<?php echo esc_attr( $room ); ?>"
    data-size="<?php echo esc_attr( $size ); ?>"
    data-status="<?php echo esc_attr( $status ); ?>"
    data-date="<?php echo esc_attr( $date ); ?>"
>

    <!-- Image area -->
    <div class="luma-artwork-card__image-wrap">

        <!-- Catalogue index (museum-style) -->
        <span class="luma-artwork-card__index" aria-hidden="true">N<?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>

        <a href="<?php echo esc_url( $permalink ); ?>" class="luma-artwork-card__image-link" tabindex="-1">
            <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                <?php echo get_the_post_thumbnail( $post_id, 'luma-artwork-card', [ 'class' => 'luma-artwork-card__image', 'alt' => esc_attr( $title ) ] ); ?>
            <?php else : ?>
                <div class="luma-artwork-card__placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;" aria-label="<?php echo esc_attr( $title ); ?>"></div>
            <?php endif; ?>
            <span class="luma-artwork-card__scrim" aria-hidden="true"></span>
        </a>

        <!-- Status badge -->
        <?php if ( $status !== 'available' ) : ?>
            <span class="luma-badge luma-badge--<?php echo esc_attr( $status ); ?> luma-artwork-card__status-badge">
                <?php echo esc_html( $status_label ); ?>
            </span>
        <?php endif; ?>

        <!-- Hover overlay -->
        <div class="luma-artwork-card__overlay" aria-hidden="true">
            <div class="luma-artwork-card__overlay-actions">
                <a href="<?php echo esc_url( $permalink ); ?>" class="luma-button luma-button--accent luma-button--sm">
                    <?php esc_html_e( 'View Artwork', 'luma-gallery' ); ?>
                </a>
                <?php if ( $status === 'available' ) : ?>
                    <button
                        type="button"
                        class="luma-button luma-button--ghost-light luma-button--sm js-add-to-cart"
                        data-cart-id="<?php echo esc_attr( $fav_id ); ?>"
                        data-title="<?php echo esc_attr( $title ); ?>"
                        data-artist="<?php echo esc_attr( $artist ); ?>"
                        data-price="<?php echo esc_attr( $price_raw ); ?>"
                        data-currency="<?php echo esc_attr( $currency ); ?>"
                        data-image="<?php echo esc_url( $thumb_url ); ?>"
                        data-gradient="<?php echo esc_attr( $gradient ); ?>"
                        data-url="<?php echo esc_url( $permalink ); ?>"
                        data-status="<?php echo esc_attr( $status ); ?>"
                        data-size="<?php echo esc_attr( $size ); ?>"
                        data-technique="<?php echo esc_attr( $technique ); ?>"
                        <?php echo luma_is_woocommerce_active() ? 'data-product-id="' . esc_attr( $post_id ) . '"' : ''; ?>
                    >
                        <?php esc_html_e( 'Add to Cart', 'luma-gallery' ); ?>
                    </button>
                <?php endif; ?>
                <button
                    class="luma-artwork-card__quick-view js-quick-view"
                    data-artwork-id="<?php echo esc_attr( $post_id ); ?>"
                    aria-label="<?php esc_attr_e( 'Quick view', 'luma-gallery' ); ?>"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Favorite button -->
        <button
            class="luma-artwork-card__favorite js-toggle-favorite"
            data-artwork-id="<?php echo esc_attr( $fav_id ); ?>"
            data-type="artwork"
            aria-label="<?php esc_attr_e( 'Add to favorites', 'luma-gallery' ); ?>"
            aria-pressed="false"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>

    </div><!-- .luma-artwork-card__image-wrap -->

    <!-- Card body — editorial caption -->
    <div class="luma-artwork-card__body">
        <?php if ( $artist ) : ?>
            <div class="luma-artwork-card__meta">
                <span class="luma-artwork-card__artist"><?php echo esc_html( $artist ); ?></span>
            </div>
        <?php endif; ?>
        <h3 class="luma-artwork-card__title">
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
        </h3>
        <div class="luma-artwork-card__footer">
            <?php if ( $price ) : ?>
                <span class="luma-artwork-card__price"><?php echo esc_html( $price ); ?></span>
            <?php endif; ?>
            <span class="luma-artwork-card__status luma-artwork-card__status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status_label ); ?></span>
        </div>
    </div><!-- .luma-artwork-card__body -->

</article>

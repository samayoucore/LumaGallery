<?php
/**
 * Template Name: Demo Cart
 *
 * localStorage-driven demo cart (no WooCommerce, no real payment). PHP renders
 * the shell + empty state; src/js/modules/demo-cart.js fills the item list and
 * order summary from `luma_demo_cart`.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Recommended demo artworks for the empty state.
$recommended = [
    [ 'title' => 'Silent Morning', 'artist' => 'Elena Morozova', 'price' => '€ 1 200', 'price_raw' => 1200, 'status' => 'available', 'gradient' => 'linear-gradient(135deg,#2C2C2A,#4A4845)', 'size' => '80 × 120 cm', 'technique' => 'Oil on canvas' ],
    [ 'title' => 'Soft Gravity',   'artist' => 'Anna Weiss',     'price' => '€ 750',   'price_raw' => 750,  'status' => 'available', 'gradient' => 'linear-gradient(145deg,#2A1E1A,#4A3530)', 'size' => '60 × 60 cm',  'technique' => 'Ink on paper' ],
    [ 'title' => 'Blue Interior',  'artist' => 'Victor Hale',    'price' => '€ 980',   'price_raw' => 980,  'status' => 'available', 'gradient' => 'linear-gradient(160deg,#1E2A3A,#2E4A5A)', 'size' => '70 × 90 cm',  'technique' => 'Acrylic' ],
    [ 'title' => 'Pale Garden',    'artist' => 'Elena Morozova', 'price' => '€ 890',   'price_raw' => 890,  'status' => 'available', 'gradient' => 'linear-gradient(125deg,#1E1A2A,#3A304A)', 'size' => '50 × 70 cm',  'technique' => 'Oil on linen' ],
];
?>

<main class="luma-cart js-cart-page" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-cart__hero">
        <span class="luma-cart__word" aria-hidden="true">COLLECT</span>
        <div class="luma-container">
            <p class="luma-cart__eyebrow"><?php esc_html_e( 'Demo Cart', 'luma-gallery' ); ?></p>
            <h1 class="luma-cart__title"><?php esc_html_e( 'Your Cart', 'luma-gallery' ); ?></h1>
            <p class="luma-cart__subtitle"><?php esc_html_e( 'Review selected artworks before the demo checkout.', 'luma-gallery' ); ?></p>
            <p class="luma-cart__disclaimer">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php esc_html_e( 'Demo cart. No real payment will be processed.', 'luma-gallery' ); ?>
            </p>
        </div>
    </section>

    <!-- ─── Body ─────────────────────────────────────────────────────────── -->
    <section class="luma-cart__body">
        <div class="luma-container">

            <!-- Filled by demo-cart.js when there are items -->
            <div class="luma-cart__layout js-cart-layout" hidden>
                <div class="luma-cart__items js-cart-items" aria-label="<?php esc_attr_e( 'Cart items', 'luma-gallery' ); ?>"></div>
                <aside class="luma-cart__summary js-cart-summary" aria-label="<?php esc_attr_e( 'Order summary', 'luma-gallery' ); ?>"></aside>
            </div>

            <!-- Empty state (default; hidden by JS when items exist) -->
            <div class="luma-cart__empty js-cart-empty">
                <div class="luma-cart__empty-inner">
                    <h2 class="luma-cart__empty-title"><?php esc_html_e( 'Your cart is empty', 'luma-gallery' ); ?></h2>
                    <p class="luma-cart__empty-text"><?php esc_html_e( 'Start collecting artworks from the gallery.', 'luma-gallery' ); ?></p>
                    <a class="luma-button luma-button--accent" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Explore Gallery', 'luma-gallery' ); ?></a>
                </div>

                <div class="luma-cart__recommended">
                    <p class="luma-cart__recommended-eyebrow"><?php esc_html_e( 'Start with these', 'luma-gallery' ); ?></p>
                    <div class="luma-artwork-grid luma-artwork-grid--4col">
                        <?php foreach ( $recommended as $i => $aw ) : ?>
                            <?php get_template_part( 'template-parts/artwork-card', null, [
                                'post_id'     => 0,
                                'title'       => $aw['title'],
                                'artist_name' => $aw['artist'],
                                'price'       => $aw['price'],
                                'price_raw'   => $aw['price_raw'],
                                'status'      => $aw['status'],
                                'gradient'    => $aw['gradient'],
                                'size'        => $aw['size'],
                                'technique'   => $aw['technique'],
                                'index'       => $i,
                            ] ); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div><!-- .luma-container -->
    </section>

</main>

<?php get_footer(); ?>

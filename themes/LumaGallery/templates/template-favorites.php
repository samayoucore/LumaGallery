<?php
/**
 * Template Name: Favorites
 *
 * Saved Collection — a personal, client-side gallery of saved artworks,
 * followed artists and saved exhibitions. The grids are rendered by
 * src/js/modules/favorites-page.js from the localStorage meta store; PHP only
 * ships the shell, the empty states and a recommended fallback.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Recommended demo artworks (shown when nothing is saved yet) ───────────────
$recommended = [
    [ 'title' => 'Silent Morning', 'artist' => 'Elena Morozova', 'price' => '€ 1 200', 'price_raw' => 1200, 'status' => 'available', 'mood' => 'Calm',     'color' => 'Neutral', 'gradient' => 'linear-gradient(135deg,#2C2C2A,#4A4845)' ],
    [ 'title' => 'Soft Gravity',   'artist' => 'Anna Weiss',     'price' => '€ 750',   'price_raw' => 750,  'status' => 'available', 'mood' => 'Calm',     'color' => 'Light',   'gradient' => 'linear-gradient(145deg,#2A1E1A,#4A3530)' ],
    [ 'title' => 'Blue Interior',  'artist' => 'Victor Hale',    'price' => '€ 980',   'price_raw' => 980,  'status' => 'available', 'mood' => 'Calm',     'color' => 'Cold',    'gradient' => 'linear-gradient(160deg,#1E2A3A,#2E4A5A)' ],
    [ 'title' => 'Pale Garden',    'artist' => 'Elena Morozova', 'price' => '€ 890',   'price_raw' => 890,  'status' => 'available', 'mood' => 'Bright',   'color' => 'Light',   'gradient' => 'linear-gradient(125deg,#1E1A2A,#3A304A)' ],
];

$tabs = [
    'artworks'    => [ 'label' => __( 'Artworks', 'luma-gallery' ),    'empty' => __( 'You haven’t saved any artworks yet.', 'luma-gallery' ),  'cta' => __( 'Explore Gallery', 'luma-gallery' ),      'cta_url' => home_url( '/gallery/' ) ],
    'artists'     => [ 'label' => __( 'Artists', 'luma-gallery' ),     'empty' => __( 'You haven’t followed any artists yet.', 'luma-gallery' ), 'cta' => __( 'Meet Artists', 'luma-gallery' ),         'cta_url' => home_url( '/artists/' ) ],
    'exhibitions' => [ 'label' => __( 'Exhibitions', 'luma-gallery' ), 'empty' => __( 'You haven’t saved any exhibitions yet.', 'luma-gallery' ), 'cta' => __( 'Explore Exhibitions', 'luma-gallery' ), 'cta_url' => home_url( '/exhibitions/' ) ],
];
?>

<main class="luma-main luma-page luma-page--favorites" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-favorites-hero" aria-label="<?php esc_attr_e( 'Saved Collection', 'luma-gallery' ); ?>">
        <div class="luma-container">
            <p class="luma-favorites-hero__eyebrow"><?php esc_html_e( 'Your Collection', 'luma-gallery' ); ?></p>
            <h1 class="luma-favorites-hero__title"><?php esc_html_e( 'Saved Collection', 'luma-gallery' ); ?></h1>
            <p class="luma-favorites-hero__subtitle">
                <?php esc_html_e( 'Your personal selection of artworks, artists, and exhibitions to revisit.', 'luma-gallery' ); ?>
            </p>
            <p class="luma-favorites-hero__note">
                <?php esc_html_e( 'Save artworks, follow artists, and build your own private gallery inside Luma.', 'luma-gallery' ); ?>
            </p>
            <p class="luma-favorites-hero__total">
                <span class="js-fav-total">0</span> <?php esc_html_e( 'items saved', 'luma-gallery' ); ?>
            </p>
        </div>
    </section>

    <!-- ─── Body (rendered client-side) ──────────────────────────────────── -->
    <section class="luma-favorites-body js-favorites-page">
        <div class="luma-container">

            <!-- Tabs -->
            <div class="luma-favorites-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Saved items', 'luma-gallery' ); ?>">
                <?php $first = true; foreach ( $tabs as $key => $tab ) : ?>
                    <button
                        class="luma-favorites-tab js-fav-tab<?php echo $first ? ' is-active' : ''; ?>"
                        role="tab"
                        id="fav-tab-<?php echo esc_attr( $key ); ?>"
                        aria-controls="fav-panel-<?php echo esc_attr( $key ); ?>"
                        aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
                        tabindex="<?php echo $first ? '0' : '-1'; ?>"
                        data-fav-tab="<?php echo esc_attr( $key ); ?>"
                    >
                        <span class="luma-favorites-tab__label"><?php echo esc_html( $tab['label'] ); ?></span>
                        <span class="luma-favorites-tab__count js-fav-count" data-fav-type="<?php echo esc_attr( $key ); ?>">0</span>
                    </button>
                <?php $first = false; endforeach; ?>
            </div>

            <!-- Panels -->
            <?php $first = true; foreach ( $tabs as $key => $tab ) :
                $grid_class = 'artworks' === $key ? 'luma-artwork-grid luma-artwork-grid--4col'
                            : ( 'artists' === $key ? 'luma-artist-grid luma-artist-grid--3col'
                            : 'luma-favorites-grid--exhibitions' );
            ?>
                <div
                    class="luma-favorites-panel js-fav-panel"
                    id="fav-panel-<?php echo esc_attr( $key ); ?>"
                    role="tabpanel"
                    aria-labelledby="fav-tab-<?php echo esc_attr( $key ); ?>"
                    data-fav-panel="<?php echo esc_attr( $key ); ?>"
                    <?php echo $first ? '' : 'hidden'; ?>
                >
                    <!-- Saved grid (filled by JS) -->
                    <div class="<?php echo esc_attr( $grid_class ); ?> js-fav-grid" hidden></div>

                    <!-- Empty state -->
                    <div class="luma-favorites-empty js-fav-empty">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        <p class="luma-favorites-empty__text"><?php echo esc_html( $tab['empty'] ); ?></p>
                        <a href="<?php echo esc_url( $tab['cta_url'] ); ?>" class="luma-button luma-button--primary">
                            <?php echo esc_html( $tab['cta'] ); ?>
                        </a>
                    </div>

                    <?php if ( 'artworks' === $key ) : ?>
                        <!-- Recommended (only when nothing saved) -->
                        <div class="luma-favorites-recommended js-fav-recommended">
                            <div class="luma-favorites-recommended__header">
                                <h2 class="luma-favorites-recommended__title"><?php esc_html_e( 'Start with these artworks', 'luma-gallery' ); ?></h2>
                                <p class="luma-favorites-recommended__sub"><?php esc_html_e( 'A few pieces our collectors keep coming back to.', 'luma-gallery' ); ?></p>
                            </div>
                            <div class="luma-artwork-grid luma-artwork-grid--4col">
                                <?php foreach ( $recommended as $i => $aw ) : ?>
                                    <?php get_template_part( 'template-parts/artwork-card', null, [
                                        'post_id'     => 0,
                                        'title'       => $aw['title'],
                                        'artist_name' => $aw['artist'],
                                        'price'       => $aw['price'],
                                        'price_raw'   => $aw['price_raw'],
                                        'status'      => $aw['status'],
                                        'mood'        => $aw['mood'],
                                        'color'       => $aw['color'],
                                        'gradient'    => $aw['gradient'],
                                        'index'       => $i,
                                    ] ); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php $first = false; endforeach; ?>

        </div><!-- .luma-container -->
    </section><!-- .luma-favorites-body -->

</main>

<?php get_footer(); ?>

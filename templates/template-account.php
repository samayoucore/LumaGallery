<?php
/**
 * Template Name: Account
 *
 * Demo collector dashboard. No real auth. PHP renders the shell; the data
 * (profile, stats, demo orders, saved artworks, followed artists, recently
 * viewed) is filled client-side by src/js/modules/account.js from localStorage.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sections = [
    [ 'key' => 'saved',   'sel' => 'js-account-saved',   'grid' => 'luma-artwork-grid luma-artwork-grid--4col', 'eyebrow' => __( 'Collection', 'luma-gallery' ),  'title' => __( 'Saved Artworks', 'luma-gallery' ),  'link' => '/favorites/', 'link_label' => __( 'All favorites', 'luma-gallery' ) ],
    [ 'key' => 'artists', 'sel' => 'js-account-artists', 'grid' => 'luma-artist-grid luma-artist-grid--3col',   'eyebrow' => __( 'People', 'luma-gallery' ),      'title' => __( 'Following', 'luma-gallery' ),        'link' => '/artists/',   'link_label' => __( 'Meet artists', 'luma-gallery' ) ],
    [ 'key' => 'recent',  'sel' => 'js-account-recent',  'grid' => 'luma-artwork-grid luma-artwork-grid--4col', 'eyebrow' => __( 'History', 'luma-gallery' ),     'title' => __( 'Recently Viewed', 'luma-gallery' ), 'link' => '/gallery/',   'link_label' => __( 'Explore gallery', 'luma-gallery' ) ],
];
?>

<main class="luma-account js-account-page" id="main-content">

    <!-- ─── Hero / profile ───────────────────────────────────────────────── -->
    <section class="luma-account__hero">
        <span class="luma-account__word" aria-hidden="true">ARCHIVE</span>
        <div class="luma-container">
            <div class="luma-account__profile">
                <span class="luma-account__avatar js-account-avatar" aria-hidden="true">G</span>
                <div class="luma-account__profile-info">
                    <p class="luma-account__eyebrow"><?php esc_html_e( 'Demo Account', 'luma-gallery' ); ?></p>
                    <h1 class="luma-account__name js-account-name"><?php esc_html_e( 'Guest Collector', 'luma-gallery' ); ?></h1>
                    <p class="luma-account__email js-account-email">collector@luma.demo</p>
                    <p class="luma-account__since">
                        <?php esc_html_e( 'Collecting since', 'luma-gallery' ); ?>
                        <span class="js-account-since"><?php echo esc_html( gmdate( 'Y' ) ); ?></span>
                    </p>
                </div>
            </div>

            <nav class="luma-account__links" aria-label="<?php esc_attr_e( 'Account shortcuts', 'luma-gallery' ); ?>">
                <a class="luma-account__link" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Gallery', 'luma-gallery' ); ?></a>
                <a class="luma-account__link" href="<?php echo esc_url( home_url( '/favorites/' ) ); ?>"><?php esc_html_e( 'Favorites', 'luma-gallery' ); ?></a>
                <a class="luma-account__link" href="<?php echo esc_url( home_url( '/ai-curator/' ) ); ?>"><?php esc_html_e( 'AI Curator', 'luma-gallery' ); ?></a>
                <a class="luma-account__link" href="<?php echo esc_url( home_url( '/cart/' ) ); ?>"><?php esc_html_e( 'Cart', 'luma-gallery' ); ?></a>
            </nav>
        </div>
    </section>

    <!-- ─── Body ─────────────────────────────────────────────────────────── -->
    <section class="luma-account__body">
        <div class="luma-container">

            <!-- Stats (filled by account.js) -->
            <div class="luma-account__stats js-account-stats" aria-label="<?php esc_attr_e( 'Account statistics', 'luma-gallery' ); ?>"></div>

            <!-- Orders -->
            <section class="luma-account__section">
                <div class="luma-account__section-head">
                    <p class="luma-account__section-eyebrow"><?php esc_html_e( 'Purchases', 'luma-gallery' ); ?></p>
                    <h2 class="luma-account__section-title"><?php esc_html_e( 'Demo Order History', 'luma-gallery' ); ?></h2>
                </div>
                <div class="luma-account__orders js-account-orders"></div>
            </section>

            <!-- Saved / Following / Recently viewed -->
            <?php foreach ( $sections as $s ) : ?>
                <section class="luma-account__section">
                    <div class="luma-account__section-head">
                        <div>
                            <p class="luma-account__section-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
                            <h2 class="luma-account__section-title"><?php echo esc_html( $s['title'] ); ?></h2>
                        </div>
                        <a class="luma-account__section-link" href="<?php echo esc_url( home_url( $s['link'] ) ); ?>"><?php echo esc_html( $s['link_label'] ); ?> &rarr;</a>
                    </div>
                    <div class="<?php echo esc_attr( $s['grid'] . ' ' . $s['sel'] ); ?>"></div>
                </section>
            <?php endforeach; ?>

        </div><!-- .luma-container -->
    </section>

</main>

<?php get_footer(); ?>

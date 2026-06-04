<?php
/**
 * 404 — Phase 7A.
 *
 * A "missing artwork" composition in the gallery's visual language. Pure CSS
 * visual (tilted empty frames); no heavy JS. Gives the visitor a search box
 * and useful navigation back into the site.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="luma-error" id="main-content">

    <span class="luma-error__word" aria-hidden="true">LOST</span>

    <div class="luma-container luma-error__inner">

        <!-- ─── Visual: empty museum wall ─────────────────────────────────── -->
        <div class="luma-error__visual" aria-hidden="true">
            <div class="luma-error__frame luma-error__frame--a"></div>
            <div class="luma-error__frame luma-error__frame--b">
                <span class="luma-error__frame-tag">No. 404</span>
            </div>
            <div class="luma-error__frame luma-error__frame--c"></div>
            <span class="luma-error__hook"></span>
        </div>

        <!-- ─── Copy ──────────────────────────────────────────────────────── -->
        <div class="luma-error__content">
            <p class="luma-error__number" aria-hidden="true">404</p>
            <p class="luma-error__eyebrow"><?php esc_html_e( 'Error 404', 'luma-gallery' ); ?></p>
            <h1 class="luma-error__title"><?php esc_html_e( 'This artwork is missing', 'luma-gallery' ); ?></h1>
            <p class="luma-error__subtitle">
                <?php esc_html_e( 'The page you’re looking for may have been moved, archived, or never existed.', 'luma-gallery' ); ?>
            </p>

            <form class="luma-error__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <label class="luma-visually-hidden" for="luma-404-search"><?php esc_html_e( 'Search the gallery', 'luma-gallery' ); ?></label>
                <input
                    class="luma-input luma-error__search-field"
                    type="search"
                    id="luma-404-search"
                    name="s"
                    placeholder="<?php esc_attr_e( 'Search the gallery…', 'luma-gallery' ); ?>"
                    autocomplete="off"
                >
                <button type="submit" class="luma-button luma-button--accent"><?php esc_html_e( 'Search', 'luma-gallery' ); ?></button>
            </form>

            <div class="luma-error__actions">
                <a class="luma-button luma-button--ghost-light" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return Home', 'luma-gallery' ); ?></a>
                <a class="luma-button luma-button--ghost-light" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Explore Gallery', 'luma-gallery' ); ?></a>
                <a class="luma-button luma-button--ghost-light" href="<?php echo esc_url( home_url( '/ai-curator/' ) ); ?>"><?php esc_html_e( 'Ask AI Curator', 'luma-gallery' ); ?></a>
            </div>

            <nav class="luma-error__links" aria-label="<?php esc_attr_e( 'Suggested pages', 'luma-gallery' ); ?>">
                <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Gallery', 'luma-gallery' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/artists/' ) ); ?>"><?php esc_html_e( 'Artists', 'luma-gallery' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/exhibitions/' ) ); ?>"><?php esc_html_e( 'Exhibitions', 'luma-gallery' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'Journal', 'luma-gallery' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/favorites/' ) ); ?>"><?php esc_html_e( 'Favorites', 'luma-gallery' ); ?></a>
            </nav>
        </div>

    </div><!-- .luma-error__inner -->

</main>

<?php get_footer(); ?>

<?php
/**
 * Template Name: About
 *
 * Phase 7A — editorial About page. Static, content-only (no business logic):
 * explains the Luma Gallery experience for collectors and artists, and is
 * transparent that this is a portfolio demo (no real payment / no external AI).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$gallery_url  = esc_url( home_url( '/gallery/' ) );
$studio_url   = esc_url( home_url( '/studio/' ) );
$curator_url  = esc_url( home_url( '/ai-curator/' ) );
$journal_url  = esc_url( home_url( '/journal/' ) );
?>

<main class="luma-about" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-about__hero">
        <span class="luma-about__word" aria-hidden="true">ABOUT</span>
        <div class="luma-container">
            <p class="luma-about__eyebrow"><?php esc_html_e( 'Digital Art Experience', 'luma-gallery' ); ?></p>
            <h1 class="luma-about__title"><?php esc_html_e( 'About Luma Gallery', 'luma-gallery' ); ?></h1>
            <p class="luma-about__subtitle">
                <?php esc_html_e( 'A digital gallery built around artists, stories, and immersive discovery.', 'luma-gallery' ); ?>
            </p>
        </div>
    </section>

    <div class="luma-about__body">
        <div class="luma-container">

            <!-- ─── 01 · Mission ─────────────────────────────────────────── -->
            <section class="luma-about__section luma-about__mission">
                <div class="luma-about__section-head">
                    <span class="luma-about__section-num" aria-hidden="true">01</span>
                    <p class="luma-about__section-eyebrow"><?php esc_html_e( 'Mission', 'luma-gallery' ); ?></p>
                </div>
                <h2 class="luma-about__section-title"><?php esc_html_e( 'A gallery beyond the white wall', 'luma-gallery' ); ?></h2>
                <p class="luma-about__section-text">
                    <?php esc_html_e( 'Luma Gallery combines curated artworks, artist stories, immersive viewing modes, and demo collecting tools into one digital gallery experience — discovery that feels less like a shop and more like walking a thoughtfully hung exhibition.', 'luma-gallery' ); ?>
                </p>
            </section>

            <!-- ─── 02 · Curation ────────────────────────────────────────── -->
            <section class="luma-about__section">
                <div class="luma-about__section-head">
                    <span class="luma-about__section-num" aria-hidden="true">02</span>
                    <p class="luma-about__section-eyebrow"><?php esc_html_e( 'Curation', 'luma-gallery' ); ?></p>
                </div>
                <h2 class="luma-about__section-title"><?php esc_html_e( 'Curated by mood, space, and story', 'luma-gallery' ); ?></h2>

                <div class="luma-about__grid">
                    <article class="luma-about__card">
                        <span class="luma-about__card-num" aria-hidden="true">A</span>
                        <h3 class="luma-about__card-title"><?php esc_html_e( 'Mood-based discovery', 'luma-gallery' ); ?></h3>
                        <p class="luma-about__card-text"><?php esc_html_e( 'Browse by feeling — calm, dark, romantic — and let the AI Curator translate a sentence about your space into a focused selection.', 'luma-gallery' ); ?></p>
                    </article>
                    <article class="luma-about__card">
                        <span class="luma-about__card-num" aria-hidden="true">B</span>
                        <h3 class="luma-about__card-title"><?php esc_html_e( 'Artist-first storytelling', 'luma-gallery' ); ?></h3>
                        <p class="luma-about__card-text"><?php esc_html_e( 'Every work sits next to the artist behind it — profiles, journal entries, and the context that makes a piece worth living with.', 'luma-gallery' ); ?></p>
                    </article>
                    <article class="luma-about__card">
                        <span class="luma-about__card-num" aria-hidden="true">C</span>
                        <h3 class="luma-about__card-title"><?php esc_html_e( 'Immersive viewing', 'luma-gallery' ); ?></h3>
                        <p class="luma-about__card-text"><?php esc_html_e( 'Gallery walk, view-in-room, and quick views let you experience scale and presence before you ever reach for the cart.', 'luma-gallery' ); ?></p>
                    </article>
                </div>
            </section>

            <!-- ─── 03 · For collectors ──────────────────────────────────── -->
            <section class="luma-about__section luma-about__split">
                <div class="luma-about__split-aside">
                    <span class="luma-about__section-num" aria-hidden="true">03</span>
                    <p class="luma-about__section-eyebrow"><?php esc_html_e( 'For collectors', 'luma-gallery' ); ?></p>
                    <h2 class="luma-about__section-title"><?php esc_html_e( 'For collectors', 'luma-gallery' ); ?></h2>
                    <a class="luma-button luma-button--accent" href="<?php echo $gallery_url; ?>"><?php esc_html_e( 'Explore Gallery', 'luma-gallery' ); ?></a>
                </div>
                <ul class="luma-about__list" role="list">
                    <li><?php esc_html_e( 'Browse a curated catalogue of original artworks', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Save favorites and revisit them any time', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Ask the AI Curator for a tailored selection', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Preview works in a room before deciding', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Run a full demo checkout, start to finish', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Track everything from an account dashboard', 'luma-gallery' ); ?></li>
                </ul>
            </section>

            <!-- ─── 04 · For artists ─────────────────────────────────────── -->
            <section class="luma-about__section luma-about__split luma-about__split--reverse">
                <div class="luma-about__split-aside">
                    <span class="luma-about__section-num" aria-hidden="true">04</span>
                    <p class="luma-about__section-eyebrow"><?php esc_html_e( 'For artists', 'luma-gallery' ); ?></p>
                    <h2 class="luma-about__section-title"><?php esc_html_e( 'For artists', 'luma-gallery' ); ?></h2>
                    <a class="luma-button luma-button--accent" href="<?php echo $studio_url; ?>"><?php esc_html_e( 'Open Artist Studio', 'luma-gallery' ); ?></a>
                </div>
                <ul class="luma-about__list" role="list">
                    <li><?php esc_html_e( 'Present a dedicated artist profile', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Publish artist posts and studio notes', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Manage demo artworks in the Artist Studio', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Draft copy with the mock AI Assistant', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Preview how a work appears in the gallery', 'luma-gallery' ); ?></li>
                </ul>
            </section>

            <!-- ─── 05 · Demo transparency ───────────────────────────────── -->
            <section class="luma-about__section luma-about__demo">
                <div class="luma-about__section-head">
                    <span class="luma-about__section-num" aria-hidden="true">05</span>
                    <p class="luma-about__section-eyebrow"><?php esc_html_e( 'Transparency', 'luma-gallery' ); ?></p>
                </div>
                <h2 class="luma-about__section-title"><?php esc_html_e( 'Built as a portfolio demo', 'luma-gallery' ); ?></h2>
                <p class="luma-about__section-text">
                    <?php esc_html_e( 'Luma Gallery is a portfolio project. It is designed to feel like a finished product while staying completely safe to explore:', 'luma-gallery' ); ?>
                </p>
                <ul class="luma-about__demo-list" role="list">
                    <li><?php esc_html_e( 'No real payment is ever processed', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'No external or paid AI API is used — assistants are rule-based', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'The demo cart and checkout run entirely in your browser via localStorage', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Artist Studio content is stored locally and is not published to WordPress', 'luma-gallery' ); ?></li>
                    <li><?php esc_html_e( 'It is built on a real WordPress theme and companion plugin architecture', 'luma-gallery' ); ?></li>
                </ul>
            </section>

            <!-- ─── 06 · Architecture ────────────────────────────────────── -->
            <section class="luma-about__section luma-about__architecture">
                <div class="luma-about__section-head">
                    <span class="luma-about__section-num" aria-hidden="true">06</span>
                    <p class="luma-about__section-eyebrow"><?php esc_html_e( 'Under the hood', 'luma-gallery' ); ?></p>
                </div>
                <h2 class="luma-about__section-title"><?php esc_html_e( 'WordPress architecture', 'luma-gallery' ); ?></h2>

                <div class="luma-about__arch-grid">
                    <div class="luma-about__arch-col">
                        <h3 class="luma-about__arch-col-title"><?php esc_html_e( 'Custom Theme', 'luma-gallery' ); ?></h3>
                        <p class="luma-about__arch-col-text"><?php esc_html_e( 'Templates, UI, SCSS, Vanilla JS', 'luma-gallery' ); ?></p>
                    </div>
                    <div class="luma-about__arch-col">
                        <h3 class="luma-about__arch-col-title"><?php esc_html_e( 'Luma Core Plugin', 'luma-gallery' ); ?></h3>
                        <p class="luma-about__arch-col-text"><?php esc_html_e( 'CPTs, taxonomies, demo helpers, Journal', 'luma-gallery' ); ?></p>
                    </div>
                    <div class="luma-about__arch-col">
                        <h3 class="luma-about__arch-col-title"><?php esc_html_e( 'Local Demo Layer', 'luma-gallery' ); ?></h3>
                        <p class="luma-about__arch-col-text"><?php esc_html_e( 'Favorites, cart, checkout, account, studio', 'luma-gallery' ); ?></p>
                    </div>
                </div>
            </section>

        </div><!-- .luma-container -->
    </div><!-- .luma-about__body -->

    <!-- ─── Final CTA ────────────────────────────────────────────────────── -->
    <section class="luma-about__cta">
        <span class="luma-about__word luma-about__word--cta" aria-hidden="true">LUMA</span>
        <div class="luma-container">
            <h2 class="luma-about__cta-title"><?php esc_html_e( 'Start exploring Luma', 'luma-gallery' ); ?></h2>
            <div class="luma-about__cta-actions">
                <a class="luma-button luma-button--accent luma-button--lg" href="<?php echo $gallery_url; ?>"><?php esc_html_e( 'Enter Gallery', 'luma-gallery' ); ?></a>
                <a class="luma-button luma-button--ghost-light luma-button--lg" href="<?php echo $curator_url; ?>"><?php esc_html_e( 'Ask AI Curator', 'luma-gallery' ); ?></a>
                <a class="luma-button luma-button--ghost-light luma-button--lg" href="<?php echo $journal_url; ?>"><?php esc_html_e( 'View Journal', 'luma-gallery' ); ?></a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>

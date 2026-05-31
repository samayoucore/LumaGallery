<?php
/**
 * Front page — Homepage of Luma Gallery.
 */

get_header();
?>

<main class="luma-main luma-main--home" id="main-content">

    <!-- Hero -->
    <?php get_template_part( 'template-parts/hero' ); ?>

    <!-- Featured Gallery Walk -->
    <?php get_template_part( 'template-parts/section-gallery-walk' ); ?>

    <!-- Exhibition of the Week -->
    <?php get_template_part( 'template-parts/section-featured-exhibition' ); ?>

    <!-- Popular Artworks -->
    <?php get_template_part( 'template-parts/section-popular-artworks' ); ?>

    <!-- Find Art by Mood -->
    <?php get_template_part( 'template-parts/section-mood-picker' ); ?>

    <!-- New Artists -->
    <?php get_template_part( 'template-parts/section-new-artists' ); ?>

    <!-- How It Works -->
    <section class="luma-section luma-how-it-works" id="how-it-works">
        <div class="luma-container">
            <div class="luma-section__header">
                <span class="luma-section__label"><?php esc_html_e( 'The Process', 'luma-gallery' ); ?></span>
                <h2 class="luma-heading luma-heading--section"><?php esc_html_e( 'How Luma Works', 'luma-gallery' ); ?></h2>
            </div>
            <div class="luma-how-it-works__steps">
                <?php
                $steps = [
                    [ 'num' => '01', 'title' => 'Discover', 'text' => 'Browse curated exhibitions, explore by mood, or let the AI Curator suggest artworks tailored to your space and taste.' ],
                    [ 'num' => '02', 'title' => 'Connect', 'text' => 'Follow artists, read their stories, explore their studio posts, and understand the inspiration behind each work.' ],
                    [ 'num' => '03', 'title' => 'Collect', 'text' => 'Save to your favourites, view art in a virtual room, and make a demo purchase to explore the full collector experience.' ],
                ];
                foreach ( $steps as $step ) :
                ?>
                <div class="luma-how-it-works__step">
                    <span class="luma-how-it-works__num"><?php echo esc_html( $step['num'] ); ?></span>
                    <h3 class="luma-how-it-works__title"><?php echo esc_html( $step['title'] ); ?></h3>
                    <p class="luma-how-it-works__text"><?php echo esc_html( $step['text'] ); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- AI Curator Teaser -->
    <?php get_template_part( 'template-parts/section-ai-curator' ); ?>

    <!-- Final CTA -->
    <section class="luma-section luma-cta luma-cta--dark" id="cta">
        <div class="luma-container">
            <div class="luma-cta__inner">
                <h2 class="luma-heading luma-heading--display luma-cta__heading">
                    <?php esc_html_e( 'Ready to discover your next favourite artwork?', 'luma-gallery' ); ?>
                </h2>
                <p class="luma-cta__subtext">
                    <?php esc_html_e( 'Over 120 original works by independent artists. Curated exhibitions. Immersive viewing. No pressure — just art.', 'luma-gallery' ); ?>
                </p>
                <div class="luma-cta__actions">
                    <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="luma-button luma-button--primary luma-button--lg">
                        <?php esc_html_e( 'Enter the Gallery', 'luma-gallery' ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/artists/' ) ); ?>" class="luma-button luma-button--ghost luma-button--lg">
                        <?php esc_html_e( 'Meet the Artists', 'luma-gallery' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_template_part( 'template-parts/modal-view-in-room' );
get_template_part( 'template-parts/modal-ai-assistant' );
get_footer();
?>

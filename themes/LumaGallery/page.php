<?php
/**
 * Generic page template — used for pages without a specific template assigned.
 */

get_header();
?>

<main class="luma-main luma-main--page" id="main-content">

    <?php while ( have_posts() ) : the_post(); ?>

        <article <?php post_class( 'luma-page-article' ); ?>>

            <header class="luma-page-hero luma-page-hero--simple">
                <div class="luma-container">
                    <h1 class="luma-heading luma-heading--display"><?php the_title(); ?></h1>
                </div>
            </header>

            <div class="luma-section luma-section--spacious">
                <div class="luma-container luma-container--narrow">
                    <div class="luma-page-content wp-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

        </article>

    <?php endwhile; ?>

</main>

<?php get_footer(); ?>

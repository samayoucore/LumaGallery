<?php
/**
 * Single post template — used for blog posts and artist posts.
 */

get_header();
?>

<main class="luma-main luma-main--single" id="main-content">

    <?php while ( have_posts() ) : the_post(); ?>

        <article <?php post_class( 'luma-single-post' ); ?>>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="luma-single-post__hero">
                    <?php the_post_thumbnail( 'luma-hero', [ 'class' => 'luma-single-post__hero-image' ] ); ?>
                    <div class="luma-single-post__hero-overlay"></div>
                    <div class="luma-single-post__hero-content">
                        <div class="luma-container">
                            <?php luma_post_categories(); ?>
                            <h1 class="luma-heading luma-heading--display luma-single-post__title"><?php the_title(); ?></h1>
                            <div class="luma-single-post__meta">
                                <span class="luma-single-post__author"><?php the_author(); ?></span>
                                <span class="luma-single-post__date"><?php echo esc_html( get_the_date() ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="luma-page-hero luma-page-hero--simple">
                    <div class="luma-container">
                        <?php luma_post_categories(); ?>
                        <h1 class="luma-heading luma-heading--display"><?php the_title(); ?></h1>
                        <div class="luma-single-post__meta">
                            <span><?php the_author(); ?></span>
                            <span><?php echo esc_html( get_the_date() ); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="luma-section luma-section--spacious">
                <div class="luma-container luma-container--narrow">
                    <div class="luma-post-content wp-content">
                        <?php the_content(); ?>
                    </div>
                    <?php
                    wp_link_pages( [
                        'before' => '<div class="luma-post-pages">',
                        'after'  => '</div>',
                    ] );
                    ?>
                </div>
            </div>

        </article>

        <!-- Post navigation -->
        <div class="luma-section luma-section--tight">
            <div class="luma-container">
                <?php
                the_post_navigation( [
                    'prev_text' => '<span class="luma-post-nav__label">' . esc_html__( 'Previous', 'luma-gallery' ) . '</span><span class="luma-post-nav__title">%title</span>',
                    'next_text' => '<span class="luma-post-nav__label">' . esc_html__( 'Next', 'luma-gallery' ) . '</span><span class="luma-post-nav__title">%title</span>',
                    'class'     => 'luma-post-nav',
                ] );
                ?>
            </div>
        </div>

    <?php endwhile; ?>

</main>

<?php get_footer(); ?>

<?php
function luma_post_categories(): void {
    $cats = get_the_category();
    if ( ! empty( $cats ) ) {
        echo '<div class="luma-post-cats">';
        foreach ( $cats as $cat ) {
            printf(
                '<a href="%s" class="luma-badge">%s</a> ',
                esc_url( get_category_link( $cat->term_id ) ),
                esc_html( $cat->name )
            );
        }
        echo '</div>';
    }
}

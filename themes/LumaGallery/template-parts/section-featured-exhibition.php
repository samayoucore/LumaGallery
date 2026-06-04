<?php
/**
 * Featured exhibition section — homepage.
 */

$featured_exhibition = function_exists( 'luma_get_featured_exhibition' ) ? luma_get_featured_exhibition() : null;

// Demo fallback
$demo = [
    'title'       => 'Night Silence',
    'subtitle'    => 'Exhibition of the Week',
    'curator'     => 'A meditative journey through after-hours landscapes — where darkness becomes texture and silence finds a visual voice.',
    'works_count' => 12,
    'mood_tags'   => [ 'dark', 'atmospheric', 'calm' ],
    'gradient'    => 'linear-gradient(160deg, #0A0A0E 0%, #1A1A28 50%, #0E0E18 100%)',
    'permalink'   => home_url( '/exhibitions/' ),
];
?>

<section class="luma-section luma-featured-exhibition" id="featured-exhibition">
    <div class="luma-container">

        <div class="luma-section__header">
            <span class="luma-section__label"><?php esc_html_e( 'Exhibition of the Week', 'luma-gallery' ); ?></span>
        </div>

        <?php
        $ex = $featured_exhibition ?? (object) $demo;
        $has_real = $featured_exhibition !== null;
        $gradient = $has_real ? luma_get_placeholder_gradient( 0 ) : $demo['gradient'];
        $permalink = $has_real ? get_permalink( $ex->ID ) : $demo['permalink'];
        $title = $has_real ? get_the_title( $ex->ID ) : $demo['title'];
        $subtitle = $has_real ? ( get_post_meta( $ex->ID, 'exhibition_subtitle', true ) ?: '' ) : $demo['subtitle'];
        $curator = $has_real ? ( get_post_meta( $ex->ID, 'exhibition_curator_note', true ) ?: '' ) : $demo['curator'];
        $works_count = $has_real ? (int) get_post_meta( $ex->ID, 'exhibition_works_count', true ) : $demo['works_count'];
        $mood_tags = $has_real ? (array) wp_get_post_terms( $ex->ID, 'luma_mood', [ 'fields' => 'names' ] ) : $demo['mood_tags'];
        ?>

        <div class="luma-featured-exhibition__inner">

            <!-- Cover -->
            <a href="<?php echo esc_url( $permalink ); ?>" class="luma-featured-exhibition__cover-link">
                <div class="luma-featured-exhibition__cover">
                    <?php if ( $has_real && has_post_thumbnail( $ex->ID ) ) : ?>
                        <?php echo get_the_post_thumbnail( $ex->ID, 'luma-exhibition-cover', [ 'class' => 'luma-featured-exhibition__cover-img', 'alt' => esc_attr( $title ) ] ); ?>
                    <?php else : ?>
                        <div class="luma-featured-exhibition__cover-placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;"></div>
                    <?php endif; ?>
                    <div class="luma-featured-exhibition__cover-overlay"></div>

                    <?php if ( ! empty( $mood_tags ) ) : ?>
                        <div class="luma-featured-exhibition__tags">
                            <?php foreach ( array_slice( $mood_tags, 0, 3 ) as $tag ) : ?>
                                <span class="luma-badge luma-badge--mood"><?php echo esc_html( $tag ); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </a>

            <!-- Info -->
            <div class="luma-featured-exhibition__info">
                <?php if ( $subtitle ) : ?>
                    <span class="luma-featured-exhibition__subtitle"><?php echo esc_html( $subtitle ); ?></span>
                <?php endif; ?>
                <h2 class="luma-heading luma-heading--section luma-featured-exhibition__title">
                    <?php echo esc_html( $title ); ?>
                </h2>
                <?php if ( $curator ) : ?>
                    <p class="luma-featured-exhibition__curator"><?php echo esc_html( $curator ); ?></p>
                <?php endif; ?>
                <div class="luma-featured-exhibition__meta">
                    <?php if ( $works_count ) : ?>
                        <span><?php printf( esc_html( _n( '%d artwork', '%d artworks', $works_count, 'luma-gallery' ) ), esc_html( $works_count ) ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="luma-featured-exhibition__actions">
                    <a href="<?php echo esc_url( $permalink ); ?>" class="luma-button luma-button--primary">
                        <?php esc_html_e( 'Enter Exhibition', 'luma-gallery' ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/exhibitions/' ) ); ?>" class="luma-button luma-button--ghost">
                        <?php esc_html_e( 'All Exhibitions', 'luma-gallery' ); ?>
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

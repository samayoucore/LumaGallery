<?php
/**
 * Journal post card partial. Renders both real and demo posts.
 *
 * $args (normalized view — see luma_core_normalize_post / luma_get_demo_artist_posts):
 *   - title, excerpt, date, topic, topic_slug, artist, url, gradient, image, index
 */

defined( 'ABSPATH' ) || exit;

$args       = $args ?? [];
$title      = $args['title']      ?? '';
$excerpt    = $args['excerpt']    ?? '';
$date       = $args['date']       ?? '';
$topic      = $args['topic']      ?? '';
$topic_slug = $args['topic_slug'] ?? sanitize_title( $topic );
$artist     = $args['artist']     ?? '';
$url        = $args['url']        ?? '#';
$gradient   = $args['gradient']   ?? luma_get_placeholder_gradient( (int) ( $args['index'] ?? 0 ) );
$image      = $args['image']      ?? '';
?>

<article class="luma-post-card luma-journal-card js-journal-card" data-topic="<?php echo esc_attr( $topic_slug ); ?>">

    <a href="<?php echo esc_url( $url ); ?>" class="luma-post-card__image-link luma-journal-card__media">
        <?php if ( $image ) : ?>
            <img class="luma-post-card__image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
        <?php else : ?>
            <span class="luma-journal-card__placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;" aria-hidden="true"></span>
        <?php endif; ?>
        <?php if ( $topic ) : ?>
            <span class="luma-journal-card__topic"><?php echo esc_html( $topic ); ?></span>
        <?php endif; ?>
    </a>

    <div class="luma-post-card__body">
        <div class="luma-journal-card__meta">
            <?php if ( $date ) : ?>
                <span class="luma-journal-card__date"><?php echo esc_html( $date ); ?></span>
            <?php endif; ?>
            <?php if ( $artist ) : ?>
                <span class="luma-journal-card__artist"><?php echo esc_html( $artist ); ?></span>
            <?php endif; ?>
        </div>

        <h3 class="luma-post-card__title">
            <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
        </h3>

        <?php if ( $excerpt ) : ?>
            <p class="luma-post-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
        <?php endif; ?>

        <a href="<?php echo esc_url( $url ); ?>" class="luma-journal-card__cta">
            <?php esc_html_e( 'Read story', 'luma-gallery' ); ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </a>
    </div>

</article>

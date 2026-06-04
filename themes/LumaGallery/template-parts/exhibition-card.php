<?php
/**
 * Exhibition card partial — cinematic poster (content overlaid on the cover).
 *
 * $args: post_id, title, subtitle, curator_note, works_count, mood_tags,
 *        gradient, index, featured
 *
 * Keeps the .js-toggle-favorite hook + __title/__subtitle/__cover* selectors
 * that favorites-page.js reads when snapshotting a saved exhibition.
 */

$args        = $args ?? [];
$post_id     = (int) ( $args['post_id']      ?? get_the_ID() );
$title       = $args['title']       ?? get_the_title( $post_id );
$subtitle    = $args['subtitle']    ?? '';
$curator     = $args['curator_note'] ?? '';
$works_cnt   = (int) ( $args['works_count'] ?? 0 );
$mood_tags   = (array) ( $args['mood_tags'] ?? [] );
$gradient    = $args['gradient']    ?? luma_get_placeholder_gradient( $args['index'] ?? 0 );
$index       = (int) ( $args['index'] ?? 0 );
$featured    = (bool) ( $args['featured'] ?? false );
$permalink   = get_permalink( $post_id ) ?: '#';

$fav_id = $post_id > 0 ? (string) $post_id : 'demo-' . sanitize_title( $title );
?>

<article
    class="luma-exhibition-card<?php echo $featured ? ' luma-exhibition-card--featured' : ''; ?>"
    data-exhibition-id="<?php echo esc_attr( $fav_id ); ?>"
    data-index="<?php echo esc_attr( $index ); ?>"
>

    <a href="<?php echo esc_url( $permalink ); ?>" class="luma-exhibition-card__cover-link">
        <div class="luma-exhibition-card__cover">
            <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                <?php echo get_the_post_thumbnail( $post_id, 'luma-exhibition-cover', [ 'class' => 'luma-exhibition-card__cover-img', 'alt' => esc_attr( $title ) ] ); ?>
            <?php else : ?>
                <div class="luma-exhibition-card__cover-placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;"></div>
            <?php endif; ?>

            <span class="luma-exhibition-card__index" aria-hidden="true">EXH&middot;<?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
            <div class="luma-exhibition-card__cover-overlay" aria-hidden="true"></div>

            <div class="luma-exhibition-card__content">
                <?php if ( ! empty( $mood_tags ) ) : ?>
                    <div class="luma-exhibition-card__mood-tags">
                        <?php foreach ( array_slice( $mood_tags, 0, 3 ) as $tag ) : ?>
                            <span class="luma-exhibition-card__mood"><?php echo esc_html( $tag ); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $subtitle ) : ?>
                    <span class="luma-exhibition-card__subtitle"><?php echo esc_html( $subtitle ); ?></span>
                <?php endif; ?>

                <h3 class="luma-exhibition-card__title">
                    <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                </h3>

                <?php if ( $curator ) : ?>
                    <p class="luma-exhibition-card__curator"><?php echo esc_html( wp_trim_words( $curator, 16 ) ); ?></p>
                <?php endif; ?>

                <div class="luma-exhibition-card__bottom">
                    <?php if ( $works_cnt ) : ?>
                        <span class="luma-exhibition-card__count">
                            <?php printf( esc_html( _n( '%d work', '%d works', $works_cnt, 'luma-gallery' ) ), esc_html( $works_cnt ) ); ?>
                        </span>
                    <?php endif; ?>
                    <span class="luma-exhibition-card__enter">
                        <?php esc_html_e( 'Enter exhibition', 'luma-gallery' ); ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </span>
                </div>
            </div>
        </div>
    </a>

    <!-- Save / favorite -->
    <button
        class="luma-exhibition-card__favorite js-toggle-favorite"
        data-exhibition-id="<?php echo esc_attr( $fav_id ); ?>"
        data-type="exhibition"
        aria-label="<?php esc_attr_e( 'Save exhibition', 'luma-gallery' ); ?>"
        aria-pressed="false"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
    </button>

</article>

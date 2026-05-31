<?php
/**
 * Exhibition card partial.
 *
 * $args:
 *   - post_id       (int)
 *   - title         (string)
 *   - subtitle      (string)
 *   - curator_note  (string)
 *   - works_count   (int)
 *   - mood_tags     (array)
 *   - gradient      (string)
 *   - index         (int)
 *   - featured      (bool)
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
?>

<article
    class="luma-exhibition-card<?php echo $featured ? ' luma-exhibition-card--featured' : ''; ?>"
    data-index="<?php echo esc_attr( $index ); ?>"
>

    <a href="<?php echo esc_url( $permalink ); ?>" class="luma-exhibition-card__cover-link">
        <div class="luma-exhibition-card__cover">
            <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                <?php echo get_the_post_thumbnail( $post_id, 'luma-exhibition-cover', [ 'class' => 'luma-exhibition-card__cover-img', 'alt' => esc_attr( $title ) ] ); ?>
            <?php else : ?>
                <div class="luma-exhibition-card__cover-placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;"></div>
            <?php endif; ?>
            <div class="luma-exhibition-card__cover-overlay" aria-hidden="true"></div>

            <?php if ( ! empty( $mood_tags ) ) : ?>
                <div class="luma-exhibition-card__mood-tags">
                    <?php foreach ( array_slice( $mood_tags, 0, 3 ) as $tag ) : ?>
                        <span class="luma-badge luma-badge--mood"><?php echo esc_html( $tag ); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </a>

    <div class="luma-exhibition-card__body">
        <?php if ( $subtitle ) : ?>
            <span class="luma-exhibition-card__subtitle"><?php echo esc_html( $subtitle ); ?></span>
        <?php endif; ?>
        <h3 class="luma-exhibition-card__title">
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
        </h3>
        <?php if ( $curator ) : ?>
            <p class="luma-exhibition-card__curator"><?php echo esc_html( wp_trim_words( $curator, 18 ) ); ?></p>
        <?php endif; ?>
        <div class="luma-exhibition-card__footer">
            <?php if ( $works_cnt ) : ?>
                <span class="luma-exhibition-card__count">
                    <?php printf(
                        esc_html( _n( '%d work', '%d works', $works_cnt, 'luma-gallery' ) ),
                        esc_html( $works_cnt )
                    ); ?>
                </span>
            <?php endif; ?>
            <a href="<?php echo esc_url( $permalink ); ?>" class="luma-button luma-button--ghost luma-button--sm">
                <?php esc_html_e( 'View Exhibition', 'luma-gallery' ); ?>
            </a>
        </div>
    </div>

</article>

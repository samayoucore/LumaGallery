<?php
/**
 * Artist card partial.
 *
 * $args:
 *   - post_id      (int)
 *   - name         (string)
 *   - location     (string)
 *   - short_bio    (string)
 *   - artworks_count (int)
 *   - gradient     (string) fallback gradient for avatar
 *   - index        (int)
 */

$args          = $args ?? [];
$post_id       = (int) ( $args['post_id'] ?? get_the_ID() );
$name          = $args['name']           ?? get_the_title( $post_id );
$location      = $args['location']       ?? '';
$short_bio     = $args['short_bio']      ?? '';
$artworks_cnt  = (int) ( $args['artworks_count'] ?? 0 );
$gradient      = $args['gradient']       ?? luma_get_placeholder_gradient( $args['index'] ?? 0 );
$index         = (int) ( $args['index'] ?? 0 );
$permalink     = get_permalink( $post_id ) ?: '#';
?>

<article
    class="luma-artist-card"
    data-artist-id="<?php echo esc_attr( $post_id ); ?>"
    data-index="<?php echo esc_attr( $index ); ?>"
>

    <a href="<?php echo esc_url( $permalink ); ?>" class="luma-artist-card__image-link">
        <div class="luma-artist-card__image-wrap">
            <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                <?php echo get_the_post_thumbnail( $post_id, 'luma-artist-avatar', [ 'class' => 'luma-artist-card__avatar', 'alt' => esc_attr( $name ) ] ); ?>
            <?php else : ?>
                <div class="luma-artist-card__avatar-placeholder" style="background:<?php echo esc_attr( $gradient ); ?>;">
                    <span class="luma-artist-card__initial"><?php echo esc_html( mb_substr( $name, 0, 1 ) ); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </a>

    <div class="luma-artist-card__body">
        <h3 class="luma-artist-card__name">
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
        </h3>
        <?php if ( $location ) : ?>
            <span class="luma-artist-card__location">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo esc_html( $location ); ?>
            </span>
        <?php endif; ?>
        <?php if ( $short_bio ) : ?>
            <p class="luma-artist-card__bio"><?php echo esc_html( wp_trim_words( $short_bio, 16 ) ); ?></p>
        <?php endif; ?>
        <div class="luma-artist-card__footer">
            <?php if ( $artworks_cnt ) : ?>
                <span class="luma-artist-card__count">
                    <?php printf(
                        /* translators: %d: number of works */
                        esc_html( _n( '%d work', '%d works', $artworks_cnt, 'luma-gallery' ) ),
                        esc_html( $artworks_cnt )
                    ); ?>
                </span>
            <?php endif; ?>
            <button
                class="luma-button luma-button--ghost luma-button--xs js-toggle-favorite"
                data-artist-id="<?php echo esc_attr( $post_id ); ?>"
                data-type="artist"
                aria-label="<?php esc_attr_e( 'Follow artist', 'luma-gallery' ); ?>"
            >
                <?php esc_html_e( 'Follow', 'luma-gallery' ); ?>
            </button>
        </div>
    </div>

</article>

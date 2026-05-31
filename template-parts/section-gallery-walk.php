<?php
/**
 * Gallery Walk section — homepage teaser.
 * Renders a preview strip of artworks and a "Start Gallery Walk" CTA.
 */

$artworks = function_exists( 'luma_get_demo_artworks' ) ? luma_get_demo_artworks( 8 ) : [];
?>

<section class="luma-section luma-gallery-walk-section" id="gallery-walk">

    <div class="luma-container">
        <div class="luma-section__header luma-section__header--split">
            <div>
                <span class="luma-section__label"><?php esc_html_e( 'Immersive Experience', 'luma-gallery' ); ?></span>
                <h2 class="luma-heading luma-heading--section">
                    <?php esc_html_e( 'Walk Through the Gallery', 'luma-gallery' ); ?>
                </h2>
            </div>
            <button class="luma-button luma-button--primary js-start-gallery-walk" data-collection="featured">
                <?php esc_html_e( 'Start Gallery Walk', 'luma-gallery' ); ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>

    <!-- Scrollable artwork strip -->
    <div class="luma-gallery-walk-section__strip js-gallery-strip">
        <div class="luma-gallery-walk-section__track">
            <?php if ( ! empty( $artworks ) ) : ?>
                <?php foreach ( $artworks as $i => $artwork ) :
                    $meta = luma_get_artwork_meta( $artwork['id'] ?? 0 );
                ?>
                <div
                    class="luma-gallery-walk-section__frame"
                    data-artwork-id="<?php echo esc_attr( $artwork['id'] ?? $i ); ?>"
                    data-index="<?php echo esc_attr( $i ); ?>"
                >
                    <div class="luma-gallery-walk-section__frame-inner" style="background:<?php echo esc_attr( $artwork['gradient'] ?? luma_get_placeholder_gradient( $i ) ); ?>;">
                        <?php if ( ! empty( $artwork['thumbnail'] ) ) : ?>
                            <img src="<?php echo esc_url( $artwork['thumbnail'] ); ?>" alt="<?php echo esc_attr( $artwork['title'] ?? '' ); ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="luma-gallery-walk-section__frame-label">
                        <span><?php echo esc_html( $artwork['title'] ?? '' ); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else : ?>
                <?php
                $demo = [
                    ['title' => 'Silent Morning',   'g' => 'linear-gradient(135deg,#2C2C2A,#4A4845)'],
                    ['title' => 'Blue Interior',    'g' => 'linear-gradient(160deg,#1E2A3A,#2E4A5A)'],
                    ['title' => 'Nocturne Field',   'g' => 'linear-gradient(120deg,#0A0A0E,#1A1A28)'],
                    ['title' => 'Soft Gravity',     'g' => 'linear-gradient(145deg,#2A1E1A,#4A3530)'],
                    ['title' => 'The Last Window',  'g' => 'linear-gradient(135deg,#1A2A1A,#3A4A3A)'],
                    ['title' => 'Warm Distance',    'g' => 'linear-gradient(150deg,#2A2A1A,#4A4A2A)'],
                    ['title' => 'Pale Garden',      'g' => 'linear-gradient(125deg,#1E1A2A,#3A304A)'],
                    ['title' => 'After the Rain',   'g' => 'linear-gradient(140deg,#2A1A2A,#4A3A4A)'],
                ];
                foreach ( $demo as $i => $d ) : ?>
                <div class="luma-gallery-walk-section__frame" data-index="<?php echo esc_attr( $i ); ?>">
                    <div class="luma-gallery-walk-section__frame-inner" style="background:<?php echo esc_attr( $d['g'] ); ?>;"></div>
                    <div class="luma-gallery-walk-section__frame-label">
                        <span><?php echo esc_html( $d['title'] ); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="luma-container">
        <p class="luma-gallery-walk-section__hint">
            <?php esc_html_e( 'Scroll horizontally or press Start to explore in fullscreen.', 'luma-gallery' ); ?>
        </p>
    </div>

</section>

<!-- Gallery Walk fullscreen overlay (rendered here, activated by JS) -->
<div class="luma-gallery-walk js-gallery-walk" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Gallery Walk', 'luma-gallery' ); ?>">

    <div class="luma-gallery-walk__wall js-gw-wall"></div>

    <!-- Spotlight -->
    <div class="luma-gallery-walk__spotlight js-gw-spotlight" aria-hidden="true"></div>

    <!-- Controls -->
    <div class="luma-gallery-walk__controls">
        <button class="luma-gallery-walk__btn luma-gallery-walk__btn--prev js-gw-prev" aria-label="<?php esc_attr_e( 'Previous', 'luma-gallery' ); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <div class="luma-gallery-walk__progress">
            <span class="js-gw-current">1</span>
            <span class="luma-gallery-walk__sep">/</span>
            <span class="js-gw-total">8</span>
        </div>
        <button class="luma-gallery-walk__btn luma-gallery-walk__btn--next js-gw-next" aria-label="<?php esc_attr_e( 'Next', 'luma-gallery' ); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </div>

    <!-- Artwork info panel -->
    <div class="luma-gallery-walk__info js-gw-info">
        <h3 class="luma-gallery-walk__info-title js-gw-title"></h3>
        <p class="luma-gallery-walk__info-artist js-gw-artist"></p>
        <p class="luma-gallery-walk__info-price js-gw-price"></p>
        <div class="luma-gallery-walk__info-actions">
            <a href="#" class="luma-button luma-button--primary luma-button--sm js-gw-view-link">
                <?php esc_html_e( 'View Details', 'luma-gallery' ); ?>
            </a>
            <button class="luma-button luma-button--ghost luma-button--sm js-gw-favorite">
                <?php esc_html_e( 'Add to Favorites', 'luma-gallery' ); ?>
            </button>
        </div>
    </div>

    <!-- Thumbnail strip -->
    <div class="luma-gallery-walk__thumbs js-gw-thumbs"></div>

    <!-- Close -->
    <button class="luma-gallery-walk__close js-gw-close" aria-label="<?php esc_attr_e( 'Close Gallery Walk', 'luma-gallery' ); ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>

</div><!-- .luma-gallery-walk -->

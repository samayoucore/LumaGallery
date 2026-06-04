<?php
/**
 * View in Room modal.
 * CSS-only mock interior — no real 3D, no external assets required.
 */
?>

<div
    class="luma-modal luma-modal--room js-modal"
    id="modal-view-in-room"
    role="dialog"
    aria-modal="true"
    aria-label="<?php esc_attr_e( 'View artwork in a room', 'luma-gallery' ); ?>"
    aria-hidden="true"
>
    <div class="luma-modal__backdrop js-modal-close" tabindex="-1"></div>

    <div class="luma-modal__panel luma-modal__panel--room">

        <!-- Header -->
        <div class="luma-modal__header">
            <h2 class="luma-modal__title"><?php esc_html_e( 'View in Room', 'luma-gallery' ); ?></h2>
            <div class="luma-room-switcher">
                <?php
                $rooms = [
                    'living' => __( 'Living Room', 'luma-gallery' ),
                    'bedroom' => __( 'Bedroom', 'luma-gallery' ),
                    'office'  => __( 'Office', 'luma-gallery' ),
                ];
                foreach ( $rooms as $slug => $label ) : ?>
                <button
                    class="luma-room-switcher__btn js-room-switch<?php echo $slug === 'living' ? ' is-active' : ''; ?>"
                    data-room="<?php echo esc_attr( $slug ); ?>"
                >
                    <?php echo esc_html( $label ); ?>
                </button>
                <?php endforeach; ?>
            </div>
            <button class="luma-modal__close js-modal-close" aria-label="<?php esc_attr_e( 'Close', 'luma-gallery' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Room scene -->
        <div class="luma-room js-room" data-active-room="living">

            <!-- Wall -->
            <div class="luma-room__wall">
                <!-- Ceiling light -->
                <div class="luma-room__light" aria-hidden="true"></div>
                <!-- Light cone -->
                <div class="luma-room__light-cone" aria-hidden="true"></div>

                <!-- Artwork frame on wall -->
                <div class="luma-room__frame js-room-frame">
                    <div class="luma-room__frame-border">
                        <div class="luma-room__frame-mat">
                            <div class="luma-room__frame-image js-room-artwork-image">
                                <!-- Artwork image injected by JS -->
                            </div>
                        </div>
                    </div>
                    <!-- Artwork shadow -->
                    <div class="luma-room__frame-shadow" aria-hidden="true"></div>
                </div>

                <!-- Wainscot -->
                <div class="luma-room__wainscot" aria-hidden="true"></div>
            </div>

            <!-- Floor -->
            <div class="luma-room__floor" aria-hidden="true">
                <div class="luma-room__floor-planks"></div>
            </div>

            <!-- Room furniture — living -->
            <div class="luma-room__furniture luma-room__furniture--living js-room-furniture" data-room="living">
                <div class="luma-room__sofa">
                    <div class="luma-room__sofa-back"></div>
                    <div class="luma-room__sofa-seat"></div>
                    <div class="luma-room__sofa-cushion luma-room__sofa-cushion--1"></div>
                    <div class="luma-room__sofa-cushion luma-room__sofa-cushion--2"></div>
                </div>
                <div class="luma-room__side-table">
                    <div class="luma-room__side-table-top"></div>
                    <div class="luma-room__side-table-leg"></div>
                </div>
                <div class="luma-room__plant">
                    <div class="luma-room__plant-pot"></div>
                    <div class="luma-room__plant-leaves"></div>
                </div>
            </div>

            <!-- Room furniture — bedroom -->
            <div class="luma-room__furniture luma-room__furniture--bedroom js-room-furniture" data-room="bedroom" style="display:none;">
                <div class="luma-room__bed">
                    <div class="luma-room__bed-frame"></div>
                    <div class="luma-room__bed-mattress"></div>
                    <div class="luma-room__bed-pillow luma-room__bed-pillow--1"></div>
                    <div class="luma-room__bed-pillow luma-room__bed-pillow--2"></div>
                    <div class="luma-room__bed-blanket"></div>
                </div>
                <div class="luma-room__nightstand">
                    <div class="luma-room__nightstand-top"></div>
                    <div class="luma-room__nightstand-lamp">
                        <div class="luma-room__lamp-shade"></div>
                        <div class="luma-room__lamp-base"></div>
                    </div>
                </div>
            </div>

            <!-- Room furniture — office -->
            <div class="luma-room__furniture luma-room__furniture--office js-room-furniture" data-room="office" style="display:none;">
                <div class="luma-room__desk">
                    <div class="luma-room__desk-top"></div>
                    <div class="luma-room__desk-monitor">
                        <div class="luma-room__monitor-screen"></div>
                        <div class="luma-room__monitor-stand"></div>
                    </div>
                    <div class="luma-room__desk-leg luma-room__desk-leg--1"></div>
                    <div class="luma-room__desk-leg luma-room__desk-leg--2"></div>
                </div>
                <div class="luma-room__office-chair">
                    <div class="luma-room__chair-back"></div>
                    <div class="luma-room__chair-seat"></div>
                </div>
            </div>

        </div><!-- .luma-room -->

        <!-- Artwork info below scene -->
        <div class="luma-modal__room-info">
            <div class="luma-modal__room-info-text">
                <span class="luma-modal__room-info-title js-room-info-title"></span>
                <span class="luma-modal__room-info-size js-room-info-size"></span>
            </div>
            <a href="#" class="luma-button luma-button--primary luma-button--sm js-room-view-link">
                <?php esc_html_e( 'View Artwork', 'luma-gallery' ); ?>
            </a>
        </div>

    </div><!-- .luma-modal__panel -->

</div><!-- .luma-modal -->

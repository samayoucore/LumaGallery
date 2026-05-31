<?php
/**
 * AI Artist Assistant modal.
 * Renders three states: empty, loading, generated.
 */
?>

<div
    class="luma-modal luma-modal--ai js-modal"
    id="modal-ai-assistant"
    role="dialog"
    aria-modal="true"
    aria-label="<?php esc_attr_e( 'AI Artist Assistant', 'luma-gallery' ); ?>"
    aria-hidden="true"
>
    <div class="luma-modal__backdrop js-modal-close" tabindex="-1"></div>

    <div class="luma-modal__panel luma-modal__panel--ai">

        <div class="luma-modal__header">
            <div class="luma-modal__header-brand">
                <div class="luma-ai-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h2 class="luma-modal__title"><?php esc_html_e( 'AI Artist Assistant', 'luma-gallery' ); ?></h2>
            </div>
            <button class="luma-modal__close js-modal-close" aria-label="<?php esc_attr_e( 'Close', 'luma-gallery' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Tone selector -->
        <div class="luma-ai-panel__tones">
            <span class="luma-ai-panel__tones-label"><?php esc_html_e( 'Tone:', 'luma-gallery' ); ?></span>
            <?php
            $tones = [
                'poetic'       => __( 'Poetic', 'luma-gallery' ),
                'gallery'      => __( 'Gallery', 'luma-gallery' ),
                'minimal'      => __( 'Minimal', 'luma-gallery' ),
                'commercial'   => __( 'Commercial', 'luma-gallery' ),
                'social-media' => __( 'Social Media', 'luma-gallery' ),
            ];
            foreach ( $tones as $slug => $label ) : ?>
            <button
                class="luma-ai-panel__tone-btn js-ai-tone<?php echo $slug === 'gallery' ? ' is-active' : ''; ?>"
                data-tone="<?php echo esc_attr( $slug ); ?>"
            ><?php echo esc_html( $label ); ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Action buttons -->
        <div class="luma-ai-panel__actions">
            <?php
            $actions = [
                'description' => __( 'Generate Description', 'luma-gallery' ),
                'tags'        => __( 'Suggest Tags', 'luma-gallery' ),
                'story'       => __( 'Write Story', 'luma-gallery' ),
                'social'      => __( 'Social Post', 'luma-gallery' ),
                'seo'         => __( 'SEO Description', 'luma-gallery' ),
            ];
            foreach ( $actions as $type => $label ) : ?>
            <button
                class="luma-button luma-button--ghost luma-button--sm js-ai-generate"
                data-type="<?php echo esc_attr( $type ); ?>"
            ><?php echo esc_html( $label ); ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Output area -->
        <div class="luma-ai-panel__output">

            <!-- Empty state -->
            <div class="luma-ai-panel__state luma-ai-panel__state--empty js-ai-state-empty">
                <div class="luma-ai-panel__empty-icon" aria-hidden="true">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p><?php esc_html_e( 'Choose a generation type above and the assistant will write for you.', 'luma-gallery' ); ?></p>
            </div>

            <!-- Loading state -->
            <div class="luma-ai-panel__state luma-ai-panel__state--loading js-ai-state-loading" style="display:none;" aria-live="polite">
                <div class="luma-ai-panel__loader">
                    <div class="luma-ai-panel__loader-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <p class="luma-ai-panel__loader-text js-ai-loader-text">
                        <?php esc_html_e( 'Generating…', 'luma-gallery' ); ?>
                    </p>
                </div>
            </div>

            <!-- Generated state -->
            <div class="luma-ai-panel__state luma-ai-panel__state--generated js-ai-state-generated" style="display:none;" aria-live="polite">
                <div class="luma-ai-panel__generated-type js-ai-generated-type"></div>
                <div class="luma-ai-panel__generated-text js-ai-generated-text" contenteditable="false"></div>
                <div class="luma-ai-panel__generated-actions">
                    <button class="luma-button luma-button--primary luma-button--sm js-ai-apply">
                        <?php esc_html_e( 'Apply', 'luma-gallery' ); ?>
                    </button>
                    <button class="luma-button luma-button--ghost luma-button--sm js-ai-copy">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        <?php esc_html_e( 'Copy', 'luma-gallery' ); ?>
                    </button>
                    <button class="luma-button luma-button--ghost luma-button--sm js-ai-regenerate">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        <?php esc_html_e( 'Regenerate', 'luma-gallery' ); ?>
                    </button>
                    <button class="luma-button luma-button--ghost luma-button--sm js-ai-edit">
                        <?php esc_html_e( 'Edit Manually', 'luma-gallery' ); ?>
                    </button>
                </div>
            </div>

        </div><!-- .luma-ai-panel__output -->

        <!-- Disclaimer -->
        <p class="luma-ai-panel__disclaimer">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?php esc_html_e( 'Demo AI Assistant. No external AI API is used. All results are rule-based simulations.', 'luma-gallery' ); ?>
        </p>

    </div><!-- .luma-modal__panel -->

</div><!-- .luma-modal -->

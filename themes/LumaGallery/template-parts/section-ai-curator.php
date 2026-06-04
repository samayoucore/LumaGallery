<?php
/**
 * AI Curator teaser section — homepage.
 */
?>

<section class="luma-section luma-ai-curator-teaser luma-section--dark" id="ai-curator-teaser">
    <div class="luma-container">

        <div class="luma-ai-curator-teaser__inner">

            <!-- Left: copy -->
            <div class="luma-ai-curator-teaser__copy">
                <span class="luma-section__label luma-section__label--light">
                    <?php esc_html_e( 'Intelligent Curation', 'luma-gallery' ); ?>
                </span>
                <h2 class="luma-heading luma-heading--section">
                    <?php esc_html_e( 'Let the AI Curator find art for you.', 'luma-gallery' ); ?>
                </h2>
                <p class="luma-ai-curator-teaser__desc">
                    <?php esc_html_e( 'Describe your mood, budget, or room — the Luma Curator will suggest artworks tailored to your space and sensibility.', 'luma-gallery' ); ?>
                </p>
                <div class="luma-ai-curator-teaser__chips" aria-hidden="true">
                    <span class="luma-chip">calm atmosphere</span>
                    <span class="luma-chip">under €500</span>
                    <span class="luma-chip">living room</span>
                    <span class="luma-chip">soft palette</span>
                    <span class="luma-chip">medium format</span>
                </div>
                <div class="luma-ai-curator-teaser__disclaimer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <?php esc_html_e( 'Demo AI — no external AI API. Rule-based suggestions only.', 'luma-gallery' ); ?>
                </div>
                <a href="<?php echo esc_url( home_url( '/ai-curator/' ) ); ?>" class="luma-button luma-button--primary luma-button--lg">
                    <?php esc_html_e( 'Try the AI Curator', 'luma-gallery' ); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>

            <!-- Right: quick form preview -->
            <div class="luma-ai-curator-teaser__form-preview" aria-hidden="true">
                <div class="luma-ai-curator-teaser__panel">
                    <div class="luma-ai-curator-teaser__panel-header">
                        <span class="luma-ai-curator-teaser__panel-dot"></span>
                        <span class="luma-ai-curator-teaser__panel-dot"></span>
                        <span class="luma-ai-curator-teaser__panel-dot"></span>
                        <span class="luma-ai-curator-teaser__panel-title">AI Curator</span>
                    </div>
                    <div class="luma-ai-curator-teaser__panel-body">
                        <div class="luma-ai-curator-teaser__field">
                            <label><?php esc_html_e( 'Mood', 'luma-gallery' ); ?></label>
                            <div class="luma-ai-curator-teaser__select-mock">calm &nbsp;▾</div>
                        </div>
                        <div class="luma-ai-curator-teaser__field">
                            <label><?php esc_html_e( 'Budget', 'luma-gallery' ); ?></label>
                            <div class="luma-ai-curator-teaser__select-mock">€ 200 – € 800 &nbsp;▾</div>
                        </div>
                        <div class="luma-ai-curator-teaser__field">
                            <label><?php esc_html_e( 'Room', 'luma-gallery' ); ?></label>
                            <div class="luma-ai-curator-teaser__select-mock">Living Room &nbsp;▾</div>
                        </div>
                        <div class="luma-ai-curator-teaser__loader">
                            <div class="luma-ai-curator-teaser__loader-bar"></div>
                            <span><?php esc_html_e( 'Finding artworks…', 'luma-gallery' ); ?></span>
                        </div>
                        <div class="luma-ai-curator-teaser__result-row">
                            <div class="luma-ai-curator-teaser__result-frame" style="background:<?php echo esc_attr( luma_get_placeholder_gradient( 0 ) ); ?>;"></div>
                            <div class="luma-ai-curator-teaser__result-frame" style="background:<?php echo esc_attr( luma_get_placeholder_gradient( 1 ) ); ?>;"></div>
                            <div class="luma-ai-curator-teaser__result-frame" style="background:<?php echo esc_attr( luma_get_placeholder_gradient( 4 ) ); ?>;"></div>
                        </div>
                        <p class="luma-ai-curator-teaser__result-summary">
                            <?php esc_html_e( 'Found 6 calm artworks under €800.', 'luma-gallery' ); ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

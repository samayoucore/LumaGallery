<?php
/**
 * Template Name: Artist Studio
 *
 * Phase 6C — Artist Studio MVP. A front-end demo of an artist workspace.
 * PHP renders the shell only; all data (demo artworks, demo posts, stats,
 * live preview and the mock AI assistant) is handled client-side by
 * src/js/modules/studio.js from localStorage. No real auth, no CPT writes,
 * no external APIs, no WooCommerce.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ─── Option lists (kept here so PHP renders accessible, no-JS-ready selects) ──
$studio_moods      = [ 'calm', 'dark', 'romantic', 'minimal', 'bright', 'melancholic', 'atmospheric' ];
$studio_styles     = [ 'abstract', 'modern', 'minimal', 'figurative', 'landscape', 'surreal' ];
$studio_techniques = [ 'oil', 'acrylic', 'watercolor', 'mixed media', 'digital', 'ink' ];
$studio_colors     = [ 'light', 'dark', 'warm', 'cold', 'neutral', 'accent' ];
$studio_rooms      = [ 'living room', 'bedroom', 'office', 'studio' ];
$studio_statuses   = [ 'draft', 'published', 'archived' ];
$studio_currencies = [ '€', '$', '£' ];
$studio_topics     = [
    'artist-stories' => __( 'Artist Stories', 'luma-gallery' ),
    'exhibitions'    => __( 'Exhibitions', 'luma-gallery' ),
    'collecting-art' => __( 'Collecting Art', 'luma-gallery' ),
    'studio-notes'   => __( 'Studio Notes', 'luma-gallery' ),
];
$studio_post_statuses = [ 'draft', 'published' ];
$studio_ai_types = [
    'description' => __( 'Artwork Description', 'luma-gallery' ),
    'story'       => __( 'Story Behind the Artwork', 'luma-gallery' ),
    'tags'        => __( 'Tags', 'luma-gallery' ),
    'social'      => __( 'Social Post', 'luma-gallery' ),
    'seo'         => __( 'SEO Description', 'luma-gallery' ),
];
$studio_tones = [
    'poetic'     => __( 'Poetic', 'luma-gallery' ),
    'gallery'    => __( 'Gallery', 'luma-gallery' ),
    'minimal'    => __( 'Minimal', 'luma-gallery' ),
    'commercial' => __( 'Commercial', 'luma-gallery' ),
    'social'     => __( 'Social Media', 'luma-gallery' ),
];

// Small helper: print one <option>, with a returned (not echoed) selected attr.
$studio_opt = static function ( string $value, string $label, bool $is_selected = false ): void {
    printf(
        '<option value="%s"%s>%s</option>',
        esc_attr( $value ),
        selected( $is_selected, true, false ),
        esc_html( $label )
    );
};

// Tabs: key => label. The first tab is selected on load.
$studio_tabs = [
    'overview'    => __( 'Overview', 'luma-gallery' ),
    'artworks'    => __( 'Artworks', 'luma-gallery' ),
    'add-artwork' => __( 'Add Artwork', 'luma-gallery' ),
    'posts'       => __( 'Posts', 'luma-gallery' ),
    'ai'          => __( 'AI Assistant', 'luma-gallery' ),
];

// Dashboard stat cells: key (data-studio-stat) => label.
$studio_stats = [
    'artworks'  => __( 'Artworks', 'luma-gallery' ),
    'drafts'    => __( 'Drafts', 'luma-gallery' ),
    'published' => __( 'Published Demo Works', 'luma-gallery' ),
    'posts'     => __( 'Artist Posts', 'luma-gallery' ),
    'ai'        => __( 'AI Generations', 'luma-gallery' ),
];
?>

<main class="luma-studio js-studio-page" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-studio__hero">
        <span class="luma-studio__word" aria-hidden="true">STUDIO</span>
        <div class="luma-container">
            <p class="luma-studio__eyebrow"><?php esc_html_e( 'Demo Studio', 'luma-gallery' ); ?></p>
            <h1 class="luma-studio__title"><?php esc_html_e( 'Artist Studio', 'luma-gallery' ); ?></h1>
            <p class="luma-studio__subtitle">
                <?php esc_html_e( 'Create demo artworks, generate gallery-ready descriptions, and manage your artist presence.', 'luma-gallery' ); ?>
            </p>

            <p class="luma-studio__disclaimer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <?php esc_html_e( 'Demo studio. Content is stored locally in your browser and is not published to WordPress.', 'luma-gallery' ); ?>
            </p>

            <nav class="luma-studio__links" aria-label="<?php esc_attr_e( 'Studio shortcuts', 'luma-gallery' ); ?>">
                <a class="luma-studio__link" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Gallery', 'luma-gallery' ); ?></a>
                <a class="luma-studio__link" href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'Journal', 'luma-gallery' ); ?></a>
                <a class="luma-studio__link" href="<?php echo esc_url( home_url( '/account/' ) ); ?>"><?php esc_html_e( 'Account', 'luma-gallery' ); ?></a>
                <a class="luma-studio__link" href="<?php echo esc_url( home_url( '/favorites/' ) ); ?>"><?php esc_html_e( 'Favorites', 'luma-gallery' ); ?></a>
            </nav>
        </div>
    </section>

    <!-- ─── Body ─────────────────────────────────────────────────────────── -->
    <section class="luma-studio__body">
        <div class="luma-container">

            <!-- Demo artist profile -->
            <div class="luma-studio__profile">
                <span class="luma-studio__profile-avatar" aria-hidden="true">DA</span>
                <div class="luma-studio__profile-info">
                    <p class="luma-studio__profile-label"><?php esc_html_e( 'Local demo profile', 'luma-gallery' ); ?></p>
                    <h2 class="luma-studio__profile-name"><?php esc_html_e( 'Demo Artist', 'luma-gallery' ); ?></h2>
                    <ul class="luma-studio__profile-meta">
                        <li><span><?php esc_html_e( 'Role', 'luma-gallery' ); ?></span><?php esc_html_e( 'Independent artist', 'luma-gallery' ); ?></li>
                        <li><span><?php esc_html_e( 'Location', 'luma-gallery' ); ?></span><?php esc_html_e( 'Digital Studio', 'luma-gallery' ); ?></li>
                    </ul>
                    <p class="luma-studio__profile-note">
                        <?php esc_html_e( 'This studio simulates the artist workflow without registration or external APIs.', 'luma-gallery' ); ?>
                    </p>
                </div>
            </div>

            <!-- Stats -->
            <div class="luma-studio__stats" aria-label="<?php esc_attr_e( 'Studio statistics', 'luma-gallery' ); ?>">
                <?php foreach ( $studio_stats as $key => $label ) : ?>
                    <div class="luma-studio__stat">
                        <span class="luma-studio__stat-value" data-studio-stat="<?php echo esc_attr( $key ); ?>">0</span>
                        <span class="luma-studio__stat-label"><?php echo esc_html( $label ); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tabs -->
            <div class="luma-studio__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Studio sections', 'luma-gallery' ); ?>">
                <?php $first = true; foreach ( $studio_tabs as $key => $label ) : ?>
                    <button
                        type="button"
                        class="luma-studio__tab js-studio-tab"
                        id="studio-tab-<?php echo esc_attr( $key ); ?>"
                        role="tab"
                        data-studio-tab="<?php echo esc_attr( $key ); ?>"
                        aria-controls="studio-panel-<?php echo esc_attr( $key ); ?>"
                        aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
                        tabindex="<?php echo $first ? '0' : '-1'; ?>"
                    ><?php echo esc_html( $label ); ?></button>
                <?php $first = false; endforeach; ?>
            </div>

            <!-- ─── Panel: Overview ──────────────────────────────────────── -->
            <section class="luma-studio__panel js-studio-panel" id="studio-panel-overview" role="tabpanel" aria-labelledby="studio-tab-overview" tabindex="0">

                <div class="luma-studio__overview-head">
                    <p class="luma-studio__panel-eyebrow"><?php esc_html_e( 'Dashboard', 'luma-gallery' ); ?></p>
                    <h3 class="luma-studio__panel-title"><?php esc_html_e( 'Studio Overview', 'luma-gallery' ); ?></h3>
                </div>

                <div class="luma-studio__quick-actions" aria-label="<?php esc_attr_e( 'Quick actions', 'luma-gallery' ); ?>">
                    <button type="button" class="luma-button luma-button--accent js-studio-go" data-studio-go="add-artwork"><?php esc_html_e( 'Add Artwork', 'luma-gallery' ); ?></button>
                    <button type="button" class="luma-button luma-button--ghost js-studio-go" data-studio-go="posts"><?php esc_html_e( 'Write Studio Post', 'luma-gallery' ); ?></button>
                    <button type="button" class="luma-button luma-button--ghost js-studio-go" data-studio-go="ai"><?php esc_html_e( 'Generate Description', 'luma-gallery' ); ?></button>
                </div>

                <div class="luma-studio__overview-grid">
                    <div class="luma-studio__overview-col">
                        <div class="luma-studio__subhead">
                            <h4 class="luma-studio__subhead-title"><?php esc_html_e( 'Recent Artworks', 'luma-gallery' ); ?></h4>
                            <button type="button" class="luma-studio__subhead-link js-studio-go" data-studio-go="artworks"><?php esc_html_e( 'All artworks', 'luma-gallery' ); ?> &rarr;</button>
                        </div>
                        <div class="luma-studio__grid luma-studio__grid--3 js-studio-overview-artworks"></div>
                    </div>

                    <div class="luma-studio__overview-col">
                        <div class="luma-studio__subhead">
                            <h4 class="luma-studio__subhead-title"><?php esc_html_e( 'Recent Posts', 'luma-gallery' ); ?></h4>
                            <button type="button" class="luma-studio__subhead-link js-studio-go" data-studio-go="posts"><?php esc_html_e( 'All posts', 'luma-gallery' ); ?> &rarr;</button>
                        </div>
                        <div class="luma-studio__post-list js-studio-overview-posts"></div>
                    </div>
                </div>

                <p class="luma-studio__note">
                    <?php esc_html_e( 'In a production version, these actions would save data to WordPress CPTs with user roles and moderation.', 'luma-gallery' ); ?>
                </p>
            </section>

            <!-- ─── Panel: Artworks ──────────────────────────────────────── -->
            <section class="luma-studio__panel js-studio-panel" id="studio-panel-artworks" role="tabpanel" aria-labelledby="studio-tab-artworks" tabindex="0" hidden>
                <div class="luma-studio__panel-head">
                    <div>
                        <p class="luma-studio__panel-eyebrow"><?php esc_html_e( 'Catalogue', 'luma-gallery' ); ?></p>
                        <h3 class="luma-studio__panel-title"><?php esc_html_e( 'Your Demo Artworks', 'luma-gallery' ); ?></h3>
                    </div>
                    <button type="button" class="luma-button luma-button--accent js-studio-go" data-studio-go="add-artwork"><?php esc_html_e( 'Add Artwork', 'luma-gallery' ); ?></button>
                </div>
                <p class="luma-studio__feedback js-studio-artworks-feedback" role="status" aria-live="polite"></p>
                <div class="luma-studio__grid luma-studio__grid--4 js-studio-artworks"></div>
            </section>

            <!-- ─── Panel: Add Artwork ───────────────────────────────────── -->
            <section class="luma-studio__panel js-studio-panel" id="studio-panel-add-artwork" role="tabpanel" aria-labelledby="studio-tab-add-artwork" tabindex="0" hidden>
                <div class="luma-studio__panel-head">
                    <div>
                        <p class="luma-studio__panel-eyebrow"><?php esc_html_e( 'Compose', 'luma-gallery' ); ?></p>
                        <h3 class="luma-studio__panel-title js-studio-form-title"><?php esc_html_e( 'Add Artwork', 'luma-gallery' ); ?></h3>
                    </div>
                </div>

                <div class="luma-studio__split">

                    <!-- Form -->
                    <form class="luma-studio__form js-studio-artwork-form" novalidate>
                        <input type="hidden" name="editingId" value="">

                        <div class="luma-studio__form-grid">
                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-art-title"><?php esc_html_e( 'Title', 'luma-gallery' ); ?> <span class="luma-studio__req">*</span></label>
                                <input class="luma-input" type="text" id="studio-art-title" name="title" autocomplete="off">
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-price"><?php esc_html_e( 'Price', 'luma-gallery' ); ?> <span class="luma-studio__req">*</span></label>
                                <input class="luma-input" type="number" id="studio-art-price" name="price" min="0" step="1" inputmode="numeric">
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-currency"><?php esc_html_e( 'Currency', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-art-currency" name="currency">
                                    <?php foreach ( $studio_currencies as $cur ) { $studio_opt( $cur, $cur, '€' === $cur ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-status"><?php esc_html_e( 'Status', 'luma-gallery' ); ?> <span class="luma-studio__req">*</span></label>
                                <select class="luma-select" id="studio-art-status" name="status">
                                    <option value=""><?php esc_html_e( 'Select status', 'luma-gallery' ); ?></option>
                                    <?php foreach ( $studio_statuses as $st ) { $studio_opt( $st, ucfirst( $st ), 'draft' === $st ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-mood"><?php esc_html_e( 'Mood', 'luma-gallery' ); ?> <span class="luma-studio__req">*</span></label>
                                <select class="luma-select" id="studio-art-mood" name="mood">
                                    <option value=""><?php esc_html_e( 'Select mood', 'luma-gallery' ); ?></option>
                                    <?php foreach ( $studio_moods as $m ) { $studio_opt( $m, ucfirst( $m ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-style"><?php esc_html_e( 'Style', 'luma-gallery' ); ?> <span class="luma-studio__req">*</span></label>
                                <select class="luma-select" id="studio-art-style" name="style">
                                    <option value=""><?php esc_html_e( 'Select style', 'luma-gallery' ); ?></option>
                                    <?php foreach ( $studio_styles as $sty ) { $studio_opt( $sty, ucfirst( $sty ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-technique"><?php esc_html_e( 'Technique', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-art-technique" name="technique">
                                    <option value="">&mdash;</option>
                                    <?php foreach ( $studio_techniques as $tq ) { $studio_opt( $tq, ucwords( $tq ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-material"><?php esc_html_e( 'Material', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="studio-art-material" name="material" autocomplete="off" placeholder="<?php esc_attr_e( 'canvas', 'luma-gallery' ); ?>">
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-size"><?php esc_html_e( 'Size', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="studio-art-size" name="size" autocomplete="off" placeholder="80 &times; 120 cm">
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-year"><?php esc_html_e( 'Year', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="number" id="studio-art-year" name="year" min="1900" max="2099" step="1" inputmode="numeric" placeholder="<?php echo esc_attr( gmdate( 'Y' ) ); ?>">
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-color"><?php esc_html_e( 'Color palette', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-art-color" name="color">
                                    <option value="">&mdash;</option>
                                    <?php foreach ( $studio_colors as $cl ) { $studio_opt( $cl, ucfirst( $cl ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-art-room"><?php esc_html_e( 'Room type', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-art-room" name="room">
                                    <option value="">&mdash;</option>
                                    <?php foreach ( $studio_rooms as $rm ) { $studio_opt( $rm, ucwords( $rm ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-art-description"><?php esc_html_e( 'Description', 'luma-gallery' ); ?></label>
                                <textarea class="luma-textarea" id="studio-art-description" name="description" rows="4"></textarea>
                            </div>

                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-art-story"><?php esc_html_e( 'Story behind the artwork', 'luma-gallery' ); ?></label>
                                <textarea class="luma-textarea" id="studio-art-story" name="story" rows="4"></textarea>
                            </div>

                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-art-tags"><?php esc_html_e( 'Tags', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="studio-art-tags" name="tags" autocomplete="off" placeholder="<?php esc_attr_e( 'calm, minimal, soft light', 'luma-gallery' ); ?>">
                            </div>
                        </div>

                        <p class="luma-studio__feedback js-studio-artwork-feedback" role="status" aria-live="polite"></p>

                        <div class="luma-studio__form-actions">
                            <button type="submit" class="luma-button luma-button--accent js-studio-artwork-submit"><?php esc_html_e( 'Save Artwork', 'luma-gallery' ); ?></button>
                            <button type="reset" class="luma-button luma-button--ghost js-studio-artwork-reset"><?php esc_html_e( 'Reset Form', 'luma-gallery' ); ?></button>
                            <button type="button" class="luma-button luma-button--ghost js-studio-artwork-ai"><?php esc_html_e( 'Generate with AI', 'luma-gallery' ); ?></button>
                        </div>
                    </form>

                    <!-- Live preview -->
                    <aside class="luma-studio__preview-wrap" aria-label="<?php esc_attr_e( 'Live preview', 'luma-gallery' ); ?>">
                        <p class="luma-studio__preview-eyebrow"><?php esc_html_e( 'Live Preview', 'luma-gallery' ); ?></p>
                        <div class="luma-studio__preview js-studio-preview"></div>
                        <p class="luma-studio__preview-note"><?php esc_html_e( 'Preview updates as you type. Visuals use a generated gradient — no image upload in this demo.', 'luma-gallery' ); ?></p>
                    </aside>

                </div>
            </section>

            <!-- ─── Panel: Posts ─────────────────────────────────────────── -->
            <section class="luma-studio__panel js-studio-panel" id="studio-panel-posts" role="tabpanel" aria-labelledby="studio-tab-posts" tabindex="0" hidden>
                <div class="luma-studio__panel-head">
                    <div>
                        <p class="luma-studio__panel-eyebrow"><?php esc_html_e( 'Journal', 'luma-gallery' ); ?></p>
                        <h3 class="luma-studio__panel-title"><?php esc_html_e( 'Studio Posts', 'luma-gallery' ); ?></h3>
                    </div>
                </div>

                <div class="luma-studio__split luma-studio__split--posts">

                    <form class="luma-studio__form js-studio-post-form" novalidate>
                        <input type="hidden" name="editingId" value="">

                        <div class="luma-studio__form-grid">
                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-post-title"><?php esc_html_e( 'Title', 'luma-gallery' ); ?> <span class="luma-studio__req">*</span></label>
                                <input class="luma-input" type="text" id="studio-post-title" name="title" autocomplete="off">
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-post-topic"><?php esc_html_e( 'Topic', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-post-topic" name="topic">
                                    <?php foreach ( $studio_topics as $slug => $label ) { $studio_opt( $slug, $label, 'studio-notes' === $slug ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-post-artwork"><?php esc_html_e( 'Linked artwork', 'luma-gallery' ); ?></label>
                                <select class="luma-select js-studio-post-artwork" id="studio-post-artwork" name="linkedArtworkId">
                                    <option value=""><?php esc_html_e( 'None', 'luma-gallery' ); ?></option>
                                </select>
                            </div>

                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-post-excerpt"><?php esc_html_e( 'Excerpt', 'luma-gallery' ); ?></label>
                                <textarea class="luma-textarea luma-textarea--sm" id="studio-post-excerpt" name="excerpt" rows="2"></textarea>
                            </div>

                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-post-content"><?php esc_html_e( 'Content', 'luma-gallery' ); ?> <span class="luma-studio__req">*</span></label>
                                <textarea class="luma-textarea" id="studio-post-content" name="content" rows="5"></textarea>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-post-status"><?php esc_html_e( 'Status', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-post-status" name="status">
                                    <?php foreach ( $studio_post_statuses as $st ) { $studio_opt( $st, ucfirst( $st ), 'draft' === $st ); } ?>
                                </select>
                            </div>
                        </div>

                        <p class="luma-studio__feedback js-studio-post-feedback" role="status" aria-live="polite"></p>

                        <div class="luma-studio__form-actions">
                            <button type="submit" class="luma-button luma-button--accent js-studio-post-submit"><?php esc_html_e( 'Save Post', 'luma-gallery' ); ?></button>
                            <button type="reset" class="luma-button luma-button--ghost js-studio-post-reset"><?php esc_html_e( 'Reset', 'luma-gallery' ); ?></button>
                            <button type="button" class="luma-button luma-button--ghost js-studio-post-ai"><?php esc_html_e( 'Generate Post with AI', 'luma-gallery' ); ?></button>
                        </div>

                        <p class="luma-studio__note">
                            <?php esc_html_e( 'Studio posts are stored locally. Public Journal integration can be added later through WordPress CPTs.', 'luma-gallery' ); ?>
                        </p>
                    </form>

                    <div class="luma-studio__post-list-wrap">
                        <div class="luma-studio__subhead">
                            <h4 class="luma-studio__subhead-title"><?php esc_html_e( 'Your Posts', 'luma-gallery' ); ?></h4>
                        </div>
                        <p class="luma-studio__feedback js-studio-posts-feedback" role="status" aria-live="polite"></p>
                        <div class="luma-studio__post-list js-studio-posts"></div>
                    </div>

                </div>
            </section>

            <!-- ─── Panel: AI Assistant ──────────────────────────────────── -->
            <section class="luma-studio__panel js-studio-panel" id="studio-panel-ai" role="tabpanel" aria-labelledby="studio-tab-ai" tabindex="0" hidden>
                <div class="luma-studio__panel-head">
                    <div>
                        <p class="luma-studio__panel-eyebrow"><?php esc_html_e( 'Assistant', 'luma-gallery' ); ?></p>
                        <h3 class="luma-studio__panel-title"><?php esc_html_e( 'Mock AI Assistant', 'luma-gallery' ); ?></h3>
                    </div>
                </div>

                <div class="luma-studio__split luma-studio__split--ai">

                    <form class="luma-studio__form luma-studio__ai js-studio-ai-form" novalidate>
                        <div class="luma-studio__form-grid">
                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-ai-type"><?php esc_html_e( 'Generate', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-ai-type" name="type">
                                    <?php foreach ( $studio_ai_types as $slug => $label ) { $studio_opt( $slug, $label, 'description' === $slug ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field luma-studio__field--wide">
                                <label for="studio-ai-title"><?php esc_html_e( 'Artwork title', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="studio-ai-title" name="title" autocomplete="off" placeholder="<?php esc_attr_e( 'Silent Study', 'luma-gallery' ); ?>">
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-ai-mood"><?php esc_html_e( 'Mood', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-ai-mood" name="mood">
                                    <option value="">&mdash;</option>
                                    <?php foreach ( $studio_moods as $m ) { $studio_opt( $m, ucfirst( $m ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-ai-style"><?php esc_html_e( 'Style', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-ai-style" name="style">
                                    <option value="">&mdash;</option>
                                    <?php foreach ( $studio_styles as $sty ) { $studio_opt( $sty, ucfirst( $sty ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-ai-technique"><?php esc_html_e( 'Technique', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-ai-technique" name="technique">
                                    <option value="">&mdash;</option>
                                    <?php foreach ( $studio_techniques as $tq ) { $studio_opt( $tq, ucwords( $tq ) ); } ?>
                                </select>
                            </div>

                            <div class="luma-studio__field">
                                <label for="studio-ai-tone"><?php esc_html_e( 'Tone', 'luma-gallery' ); ?></label>
                                <select class="luma-select" id="studio-ai-tone" name="tone">
                                    <?php foreach ( $studio_tones as $slug => $label ) { $studio_opt( $slug, $label, 'gallery' === $slug ); } ?>
                                </select>
                            </div>
                        </div>

                        <div class="luma-studio__form-actions">
                            <button type="submit" class="luma-button luma-button--accent js-studio-ai-generate"><?php esc_html_e( 'Generate', 'luma-gallery' ); ?></button>
                        </div>

                        <p class="luma-studio__disclaimer luma-studio__disclaimer--ink">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            <?php esc_html_e( 'Demo AI Assistant. No external AI API is used.', 'luma-gallery' ); ?>
                        </p>
                    </form>

                    <div class="luma-studio__ai-output js-studio-ai-output" role="status" aria-live="polite"></div>

                </div>
            </section>

        </div><!-- .luma-container -->
    </section>

</main>

<?php get_footer(); ?>

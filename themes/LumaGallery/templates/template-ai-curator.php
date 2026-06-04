<?php
/**
 * Template Name: AI Curator
 *
 * Rule-based artwork recommender (demo — no external AI API). The form and a
 * curated demo set are rendered server-side; src/js/modules/ai-curator.js parses
 * the prompt, applies the rules and curates the grid client-side.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Curated demo set — rich attributes drive the rule-based filtering ─────────
$curator_artworks = [
    [ 'title' => 'Silent Morning',  'artist' => 'Elena Morozova', 'price_raw' => 280, 'mood' => 'Calm',        'color' => 'Light',   'size' => 'Medium', 'room' => 'Bedroom', 'gradient' => 'linear-gradient(135deg,#2C2C2A,#4A4845)' ],
    [ 'title' => 'Soft Gravity',    'artist' => 'Anna Weiss',     'price_raw' => 240, 'mood' => 'Calm',        'color' => 'Neutral', 'size' => 'Small',  'room' => 'Bedroom', 'gradient' => 'linear-gradient(145deg,#2A1E1A,#4A3530)' ],
    [ 'title' => 'Pale Garden',     'artist' => 'Elena Morozova', 'price_raw' => 190, 'mood' => 'Bright',      'color' => 'Light',   'size' => 'Medium', 'room' => 'Living',  'gradient' => 'linear-gradient(125deg,#1E1A2A,#3A304A)' ],
    [ 'title' => 'Blue Interior',   'artist' => 'Victor Hale',    'price_raw' => 320, 'mood' => 'Calm',        'color' => 'Cold',    'size' => 'Medium', 'room' => 'Living',  'gradient' => 'linear-gradient(160deg,#1E2A3A,#2E4A5A)' ],
    [ 'title' => 'Inner Geometry',  'artist' => 'Anna Weiss',     'price_raw' => 150, 'mood' => 'Minimal',     'color' => 'Light',   'size' => 'Small',  'room' => 'Office',  'gradient' => 'linear-gradient(145deg,#1A1E2A,#2A2E3A)' ],
    [ 'title' => 'Nocturne Field',  'artist' => 'Mira Solen',     'price_raw' => 480, 'mood' => 'Dark',        'color' => 'Dark',    'size' => 'Large',  'room' => 'Office',  'gradient' => 'linear-gradient(120deg,#3A3530,#5C5550)' ],
    [ 'title' => 'Night Study',     'artist' => 'Daniel Arno',    'price_raw' => 560, 'mood' => 'Dark',        'color' => 'Dark',    'size' => 'Large',  'room' => 'Studio',  'gradient' => 'linear-gradient(160deg,#1A1A2A,#2A2A3A)' ],
    [ 'title' => 'Warm Distance',   'artist' => 'Sofia Lumen',    'price_raw' => 260, 'mood' => 'Romantic',    'color' => 'Warm',    'size' => 'Medium', 'room' => 'Living',  'gradient' => 'linear-gradient(150deg,#2A2A1A,#4A4A2A)' ],
    [ 'title' => 'Summer Haze',     'artist' => 'Sofia Lumen',    'price_raw' => 140, 'mood' => 'Bright',      'color' => 'Warm',    'size' => 'Small',  'room' => 'Bedroom', 'gradient' => 'linear-gradient(120deg,#2A2510,#4A4520)' ],
    [ 'title' => 'The Last Window', 'artist' => 'Daniel Arno',    'price_raw' => 300, 'mood' => 'Melancholic', 'color' => 'Neutral', 'size' => 'Large',  'room' => 'Studio',  'gradient' => 'linear-gradient(135deg,#1A2A1A,#3A4A3A)' ],
    [ 'title' => 'Inner Calm',      'artist' => 'Mira Solen',     'price_raw' => 220, 'mood' => 'Atmospheric', 'color' => 'Cold',    'size' => 'Medium', 'room' => 'Bedroom', 'gradient' => 'linear-gradient(140deg,#2A1A2A,#4A3A4A)' ],
    [ 'title' => 'Quiet Field',     'artist' => 'Victor Hale',    'price_raw' => 410, 'mood' => 'Calm',        'color' => 'Neutral', 'size' => 'Large',  'room' => 'Living',  'gradient' => 'linear-gradient(135deg,#222,#3a3a3a)' ],
];

// Select option sets (value => label).
$opt_mood   = [ 'any' => 'Any mood', 'calm' => 'Calm', 'dark' => 'Dark', 'romantic' => 'Romantic', 'minimal' => 'Minimal', 'bright' => 'Bright', 'melancholic' => 'Melancholic', 'atmospheric' => 'Atmospheric' ];
$opt_budget = [ 'any' => 'Any budget', '150' => 'Under €150', '300' => 'Under €300', '500' => 'Under €500', 'premium' => 'Premium' ];
$opt_room   = [ 'any' => 'Any room', 'living' => 'Living room', 'bedroom' => 'Bedroom', 'office' => 'Office', 'studio' => 'Studio' ];
$opt_color  = [ 'any' => 'Any color', 'light' => 'Light', 'dark' => 'Dark', 'warm' => 'Warm', 'cold' => 'Cold', 'neutral' => 'Neutral', 'accent' => 'Accent' ];
$opt_size   = [ 'any' => 'Any size', 'small' => 'Small', 'medium' => 'Medium', 'large' => 'Large' ];

/** Print a labelled select. */
$render_select = function ( string $name, string $id, string $label, array $options ): void {
    ?>
    <div class="luma-curator-field">
        <label class="luma-curator-field__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
        <select class="luma-select luma-select--full" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
            <?php foreach ( $options as $value => $text ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $text ); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
};
?>

<main class="luma-main luma-page luma-page--curator js-curator-page" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-curator-hero">
        <div class="luma-container">
            <p class="luma-curator-hero__eyebrow">
                <span class="luma-ai-icon" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                </span>
                <?php esc_html_e( 'Intelligent Curation', 'luma-gallery' ); ?>
            </p>
            <h1 class="luma-curator-hero__title"><?php esc_html_e( 'AI Curator', 'luma-gallery' ); ?></h1>
            <p class="luma-curator-hero__subtitle">
                <?php esc_html_e( 'Describe your mood, room, or budget — Luma will suggest artworks.', 'luma-gallery' ); ?>
            </p>
            <p class="luma-curator-hero__disclaimer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php esc_html_e( 'Demo AI Curator. No external AI API is used.', 'luma-gallery' ); ?>
            </p>
        </div>
    </section>

    <!-- ─── Curator body ─────────────────────────────────────────────────── -->
    <section class="luma-curator-body">
        <div class="luma-container">
            <div class="luma-curator-layout">

                <!-- Form -->
                <aside class="luma-curator-panel">
                    <form class="luma-curator-form js-curator-form" novalidate>

                        <div class="luma-curator-field">
                            <label class="luma-curator-field__label" for="curator-prompt"><?php esc_html_e( 'What are you looking for?', 'luma-gallery' ); ?></label>
                            <textarea
                                class="luma-textarea"
                                id="curator-prompt"
                                name="curator_prompt"
                                rows="3"
                                placeholder="<?php esc_attr_e( 'I want a calm abstract artwork for a bright bedroom under €300', 'luma-gallery' ); ?>"
                            ></textarea>
                        </div>

                        <div class="luma-curator-fields">
                            <?php
                            $render_select( 'curator_mood',   'curator-mood',   __( 'Mood', 'luma-gallery' ),          $opt_mood );
                            $render_select( 'curator_budget', 'curator-budget', __( 'Budget', 'luma-gallery' ),        $opt_budget );
                            $render_select( 'curator_room',   'curator-room',   __( 'Room', 'luma-gallery' ),          $opt_room );
                            $render_select( 'curator_color',  'curator-color',  __( 'Color palette', 'luma-gallery' ), $opt_color );
                            $render_select( 'curator_size',   'curator-size',   __( 'Size', 'luma-gallery' ),          $opt_size );
                            ?>
                        </div>

                        <button type="submit" class="luma-button luma-button--accent luma-button--lg luma-curator-submit">
                            <?php esc_html_e( 'Find Artworks', 'luma-gallery' ); ?>
                        </button>

                        <p class="luma-curator-form__hint">
                            <?php esc_html_e( 'Tip: try “calm minimal piece for an office under €300”.', 'luma-gallery' ); ?>
                        </p>
                    </form>
                </aside>

                <!-- Results -->
                <div class="luma-curator-results-wrap">

                    <!-- Loading state -->
                    <div class="luma-curator-loader js-curator-loader" role="status" aria-live="polite" hidden>
                        <span class="luma-curator-loader__dots"><span></span><span></span><span></span></span>
                        <span class="luma-curator-loader__text"><?php esc_html_e( 'Curating your selection…', 'luma-gallery' ); ?></span>
                    </div>

                    <!-- Recommendation summary -->
                    <p class="luma-curator-summary js-curator-summary" aria-live="polite" hidden></p>

                    <!-- Empty state -->
                    <div class="luma-curator-empty js-curator-empty" hidden>
                        <h2 class="luma-curator-empty__title"><?php esc_html_e( 'No perfect matches yet', 'luma-gallery' ); ?></h2>
                        <p class="luma-curator-empty__text"><?php esc_html_e( 'Try changing your mood, budget, or color palette.', 'luma-gallery' ); ?></p>
                        <button type="button" class="luma-button luma-button--ghost js-curator-reset"><?php esc_html_e( 'Reset Curator', 'luma-gallery' ); ?></button>
                    </div>

                    <!-- Results grid -->
                    <div class="luma-artwork-grid luma-artwork-grid--3col js-curator-results">
                        <?php foreach ( $curator_artworks as $i => $aw ) : ?>
                            <?php get_template_part( 'template-parts/artwork-card', null, [
                                'post_id'     => 0,
                                'title'       => $aw['title'],
                                'artist_name' => $aw['artist'],
                                'price'       => '€ ' . number_format( $aw['price_raw'], 0, '.', ' ' ),
                                'price_raw'   => $aw['price_raw'],
                                'status'      => 'available',
                                'mood'        => $aw['mood'],
                                'color'       => $aw['color'],
                                'room'        => $aw['room'],
                                'size'        => $aw['size'],
                                'gradient'    => $aw['gradient'],
                                'index'       => $i,
                            ] ); ?>
                        <?php endforeach; ?>
                    </div>

                </div><!-- .luma-curator-results-wrap -->

            </div><!-- .luma-curator-layout -->
        </div><!-- .luma-container -->
    </section>

    <!-- ─── How it works ─────────────────────────────────────────────────── -->
    <section class="luma-curator-how">
        <div class="luma-container">
            <div class="luma-curator-how__header">
                <p class="luma-curator-how__eyebrow"><?php esc_html_e( 'Behind the curation', 'luma-gallery' ); ?></p>
                <h2 class="luma-curator-how__title"><?php esc_html_e( 'How Luma Curator works', 'luma-gallery' ); ?></h2>
            </div>
            <div class="luma-curator-how__grid">
                <div class="luma-curator-how__col">
                    <span class="luma-curator-how__num">01</span>
                    <h3 class="luma-curator-how__col-title"><?php esc_html_e( 'Mood', 'luma-gallery' ); ?></h3>
                    <p class="luma-curator-how__col-text"><?php esc_html_e( 'We read the feeling you want — calm, dark, bright — and match the emotional tone of each work.', 'luma-gallery' ); ?></p>
                </div>
                <div class="luma-curator-how__col">
                    <span class="luma-curator-how__num">02</span>
                    <h3 class="luma-curator-how__col-title"><?php esc_html_e( 'Space', 'luma-gallery' ); ?></h3>
                    <p class="luma-curator-how__col-text"><?php esc_html_e( 'Room and scale shape the suggestion, so the piece sits naturally in your bedroom, office or studio.', 'luma-gallery' ); ?></p>
                </div>
                <div class="luma-curator-how__col">
                    <span class="luma-curator-how__num">03</span>
                    <h3 class="luma-curator-how__col-title"><?php esc_html_e( 'Budget', 'luma-gallery' ); ?></h3>
                    <p class="luma-curator-how__col-text"><?php esc_html_e( 'Your price range filters the selection, from accessible originals to premium statement pieces.', 'luma-gallery' ); ?></p>
                </div>
            </div>
            <p class="luma-curator-how__note">
                <?php esc_html_e( 'This demo uses rule-based recommendations without external AI APIs.', 'luma-gallery' ); ?>
            </p>
        </div>
    </section>

</main>

<?php get_footer(); ?>

<?php
/**
 * Template Name: Gallery
 *
 * Gallery catalog page — filterable, sortable artwork grid.
 * Fallback: rich demo data when no real CPT posts exist.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Query real artworks ───────────────────────────────────────────────────────
$cpt = class_exists( 'WooCommerce' ) ? 'product' : 'luma_artwork';
$artworks_query = new WP_Query( [
    'post_type'      => $cpt,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
$use_demo = ! $artworks_query->have_posts();

// ── Demo artworks (rich filter data) ─────────────────────────────────────────
$demo_artworks = [
    [ 'id' => 0, 'index' => 0,  'title' => 'Silent Morning',     'artist' => 'Elena Morozova', 'price' => '€ 1 200', 'price_raw' => 1200, 'status' => 'available', 'style' => 'Minimalist',    'mood' => 'Calm',        'technique' => 'Oil',         'color' => 'Earth Tones', 'date' => 1700000000, 'gradient' => 'linear-gradient(135deg,#2C2C2A,#4A4845)' ],
    [ 'id' => 0, 'index' => 1,  'title' => 'Blue Interior',      'artist' => 'Victor Hale',    'price' => '€ 980',   'price_raw' => 980,  'status' => 'available', 'style' => 'Figurative',    'mood' => 'Calm',        'technique' => 'Acrylic',     'color' => 'Blue',        'date' => 1699000000, 'gradient' => 'linear-gradient(160deg,#1E2A3A,#2E4A5A)' ],
    [ 'id' => 0, 'index' => 2,  'title' => 'Nocturne Field',     'artist' => 'Mira Solen',     'price' => '€ 2 400', 'price_raw' => 2400, 'status' => 'reserved',  'style' => 'Abstract',      'mood' => 'Dark',        'technique' => 'Oil',         'color' => 'Black',       'date' => 1698000000, 'gradient' => 'linear-gradient(120deg,#3A3530,#5C5550)' ],
    [ 'id' => 0, 'index' => 3,  'title' => 'Soft Gravity',       'artist' => 'Anna Weiss',     'price' => '€ 750',   'price_raw' => 750,  'status' => 'available', 'style' => 'Minimalist',    'mood' => 'Calm',        'technique' => 'Ink',         'color' => 'White',       'date' => 1697000000, 'gradient' => 'linear-gradient(145deg,#2A1E1A,#4A3530)' ],
    [ 'id' => 0, 'index' => 4,  'title' => 'The Last Window',    'artist' => 'Daniel Arno',    'price' => '€ 3 200', 'price_raw' => 3200, 'status' => 'available', 'style' => 'Figurative',    'mood' => 'Melancholic', 'technique' => 'Oil',         'color' => 'Earth Tones', 'date' => 1696000000, 'gradient' => 'linear-gradient(135deg,#1A2A1A,#3A4A3A)' ],
    [ 'id' => 0, 'index' => 5,  'title' => 'Warm Distance',      'artist' => 'Sofia Lumen',    'price' => '€ 1 100', 'price_raw' => 1100, 'status' => 'sold',      'style' => 'Landscape',     'mood' => 'Romantic',    'technique' => 'Watercolor',  'color' => 'Yellow',      'date' => 1695000000, 'gradient' => 'linear-gradient(150deg,#2A2A1A,#4A4A2A)' ],
    [ 'id' => 0, 'index' => 6,  'title' => 'Pale Garden',        'artist' => 'Elena Morozova', 'price' => '€ 890',   'price_raw' => 890,  'status' => 'available', 'style' => 'Landscape',     'mood' => 'Bright',      'technique' => 'Oil',         'color' => 'Green',       'date' => 1694000000, 'gradient' => 'linear-gradient(125deg,#1E1A2A,#3A304A)' ],
    [ 'id' => 0, 'index' => 7,  'title' => 'After the Rain',     'artist' => 'Victor Hale',    'price' => '€ 1 560', 'price_raw' => 1560, 'status' => 'available', 'style' => 'Landscape',     'mood' => 'Melancholic', 'technique' => 'Acrylic',     'color' => 'Blue',        'date' => 1693000000, 'gradient' => 'linear-gradient(140deg,#2A1A2A,#4A3A4A)' ],
    [ 'id' => 0, 'index' => 8,  'title' => 'Red Architecture',   'artist' => 'Mira Solen',     'price' => '€ 4 100', 'price_raw' => 4100, 'status' => 'available', 'style' => 'Abstract',      'mood' => 'Energetic',   'technique' => 'Acrylic',     'color' => 'Red',         'date' => 1692000000, 'gradient' => 'linear-gradient(135deg,#3A1A1A,#5A2A2A)' ],
    [ 'id' => 0, 'index' => 9,  'title' => 'Night Study',        'artist' => 'Daniel Arno',    'price' => '€ 2 800', 'price_raw' => 2800, 'status' => 'reserved',  'style' => 'Expressionist', 'mood' => 'Dark',        'technique' => 'Oil',         'color' => 'Black',       'date' => 1691000000, 'gradient' => 'linear-gradient(160deg,#1A1A2A,#2A2A3A)' ],
    [ 'id' => 0, 'index' => 10, 'title' => 'Summer Haze',        'artist' => 'Sofia Lumen',    'price' => '€ 650',   'price_raw' => 650,  'status' => 'available', 'style' => 'Landscape',     'mood' => 'Bright',      'technique' => 'Watercolor',  'color' => 'Yellow',      'date' => 1690000000, 'gradient' => 'linear-gradient(120deg,#2A2510,#4A4520)' ],
    [ 'id' => 0, 'index' => 11, 'title' => 'Inner Geometry',     'artist' => 'Anna Weiss',     'price' => '€ 1 350', 'price_raw' => 1350, 'status' => 'available', 'style' => 'Abstract',      'mood' => 'Calm',        'technique' => 'Mixed Media', 'color' => 'White',       'date' => 1689000000, 'gradient' => 'linear-gradient(145deg,#1A1E2A,#2A2E3A)' ],
];

$total = $use_demo ? count( $demo_artworks ) : $artworks_query->found_posts;

// Filter options
$filter_styles     = [ 'Abstract', 'Figurative', 'Landscape', 'Minimalist', 'Expressionist' ];
$filter_moods      = [ 'Calm', 'Melancholic', 'Energetic', 'Dark', 'Romantic', 'Bright' ];
$filter_techniques = [ 'Oil', 'Watercolor', 'Acrylic', 'Ink', 'Mixed Media', 'Pastel' ];
$filter_artists    = [ 'Elena Morozova', 'Victor Hale', 'Mira Solen', 'Anna Weiss', 'Daniel Arno', 'Sofia Lumen' ];
$color_swatches    = [
    'Black'       => '#1d1d1f',
    'White'       => '#f5f5f5',
    'Red'         => '#c0392b',
    'Blue'        => '#2980b9',
    'Green'       => '#27ae60',
    'Yellow'      => '#f1c40f',
    'Earth Tones' => '#8B6914',
];
?>

<main class="luma-main luma-page luma-page--gallery" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-page-hero luma-page-hero--gallery" aria-label="<?php esc_attr_e( 'Gallery', 'luma-gallery' ); ?>">
        <div class="luma-container">
            <div class="luma-page-hero__inner">
                <p class="luma-page-hero__eyebrow"><?php esc_html_e( 'Luma Gallery', 'luma-gallery' ); ?></p>
                <h1 class="luma-page-hero__title"><?php esc_html_e( 'Gallery', 'luma-gallery' ); ?></h1>
                <p class="luma-page-hero__subtitle"><?php esc_html_e( 'Browse curated artworks by mood, style, technique, and artist', 'luma-gallery' ); ?></p>
            </div>
        </div>
        <div class="luma-page-hero__word" aria-hidden="true">GALLERY</div>
    </section>

    <!-- ─── Gallery body ─────────────────────────────────────────────────── -->
    <section class="luma-gallery-body">
        <div class="luma-container">

            <!-- Topbar -->
            <div class="luma-gallery-topbar">
                <div class="luma-gallery-topbar__left">
                    <button
                        class="luma-button luma-button--ghost luma-button--sm js-filter-toggle"
                        aria-expanded="false"
                        aria-controls="gallery-filters"
                    >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
                        <?php esc_html_e( 'Filters', 'luma-gallery' ); ?>
                    </button>
                    <span class="luma-gallery-topbar__count js-gallery-count">
                        <?php printf( esc_html( _n( '%d artwork', '%d artworks', $total, 'luma-gallery' ) ), esc_html( $total ) ); ?>
                    </span>
                </div>
                <div class="luma-gallery-topbar__right">
                    <select class="luma-select js-sort-select" aria-label="<?php esc_attr_e( 'Sort artworks', 'luma-gallery' ); ?>">
                        <option value=""><?php esc_html_e( 'Newest first', 'luma-gallery' ); ?></option>
                        <option value="price-asc"><?php esc_html_e( 'Price: Low to High', 'luma-gallery' ); ?></option>
                        <option value="price-desc"><?php esc_html_e( 'Price: High to Low', 'luma-gallery' ); ?></option>
                    </select>
                    <div class="luma-view-toggle" role="group" aria-label="<?php esc_attr_e( 'View mode', 'luma-gallery' ); ?>">
                        <button class="luma-view-toggle__btn is-active js-view-toggle" data-view="grid" aria-label="<?php esc_attr_e( 'Grid view', 'luma-gallery' ); ?>" title="<?php esc_attr_e( 'Grid view', 'luma-gallery' ); ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </button>
                        <button class="luma-view-toggle__btn js-view-toggle" data-view="wall" aria-label="<?php esc_attr_e( 'Wall view', 'luma-gallery' ); ?>" title="<?php esc_attr_e( 'Gallery wall view', 'luma-gallery' ); ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="3" y="3" width="4" height="11" rx="1"/><rect x="10" y="3" width="11" height="5" rx="1"/><rect x="10" y="11" width="11" height="10" rx="1"/><rect x="3" y="17" width="4" height="4" rx="1"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="luma-gallery-layout">

                <!-- ─── Filters sidebar ───────────────────────────────────── -->
                <aside class="luma-gallery-sidebar js-filter-panel" id="gallery-filters" aria-label="<?php esc_attr_e( 'Filter artworks', 'luma-gallery' ); ?>">
                    <form class="luma-filter-form js-filter-form" novalidate>

                        <!-- Search -->
                        <div class="luma-filter-group">
                            <label class="luma-filter-group__label" for="filter-search"><?php esc_html_e( 'Search', 'luma-gallery' ); ?></label>
                            <input type="search" id="filter-search" name="title" class="luma-input"
                                placeholder="<?php esc_attr_e( 'Title or artist…', 'luma-gallery' ); ?>" autocomplete="off">
                        </div>

                        <!-- Availability -->
                        <div class="luma-filter-group">
                            <label class="luma-filter-group__label" for="filter-status"><?php esc_html_e( 'Availability', 'luma-gallery' ); ?></label>
                            <select id="filter-status" name="status" class="luma-select luma-select--full">
                                <option value=""><?php esc_html_e( 'All', 'luma-gallery' ); ?></option>
                                <option value="available"><?php esc_html_e( 'Available', 'luma-gallery' ); ?></option>
                                <option value="reserved"><?php esc_html_e( 'Reserved', 'luma-gallery' ); ?></option>
                                <option value="sold"><?php esc_html_e( 'Sold', 'luma-gallery' ); ?></option>
                            </select>
                        </div>

                        <!-- Price range -->
                        <div class="luma-filter-group">
                            <label class="luma-filter-group__label" for="filter-price"><?php esc_html_e( 'Max Price', 'luma-gallery' ); ?></label>
                            <input type="range" id="filter-price" name="priceMax" class="luma-range js-price-range" min="0" max="10000" step="100" value="10000">
                            <div class="luma-filter-range__labels">
                                <span>€0</span>
                                <span class="js-price-display">€10 000+</span>
                            </div>
                        </div>

                        <!-- Style -->
                        <div class="luma-filter-group">
                            <label class="luma-filter-group__label" for="filter-style"><?php esc_html_e( 'Style', 'luma-gallery' ); ?></label>
                            <select id="filter-style" name="style" class="luma-select luma-select--full">
                                <option value=""><?php esc_html_e( 'All styles', 'luma-gallery' ); ?></option>
                                <?php foreach ( $filter_styles as $s ) : ?>
                                    <option value="<?php echo esc_attr( $s ); ?>"><?php echo esc_html( $s ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Mood -->
                        <div class="luma-filter-group">
                            <label class="luma-filter-group__label" for="filter-mood"><?php esc_html_e( 'Mood', 'luma-gallery' ); ?></label>
                            <select id="filter-mood" name="mood" class="luma-select luma-select--full">
                                <option value=""><?php esc_html_e( 'Any mood', 'luma-gallery' ); ?></option>
                                <?php foreach ( $filter_moods as $m ) : ?>
                                    <option value="<?php echo esc_attr( $m ); ?>"><?php echo esc_html( $m ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Technique -->
                        <div class="luma-filter-group">
                            <label class="luma-filter-group__label" for="filter-technique"><?php esc_html_e( 'Technique', 'luma-gallery' ); ?></label>
                            <select id="filter-technique" name="technique" class="luma-select luma-select--full">
                                <option value=""><?php esc_html_e( 'Any technique', 'luma-gallery' ); ?></option>
                                <?php foreach ( $filter_techniques as $t ) : ?>
                                    <option value="<?php echo esc_attr( $t ); ?>"><?php echo esc_html( $t ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dominant Color -->
                        <div class="luma-filter-group">
                            <span class="luma-filter-group__label"><?php esc_html_e( 'Color', 'luma-gallery' ); ?></span>
                            <div class="luma-color-filters" role="radiogroup" aria-label="<?php esc_attr_e( 'Color filter', 'luma-gallery' ); ?>">
                                <?php foreach ( $color_swatches as $label => $hex ) : ?>
                                    <label class="luma-color-filter" title="<?php echo esc_attr( $label ); ?>">
                                        <input type="radio" name="color" value="<?php echo esc_attr( $label ); ?>" class="luma-color-filter__input" aria-label="<?php echo esc_attr( $label ); ?>">
                                        <span class="luma-color-filter__swatch" style="background:<?php echo esc_attr( $hex ); ?>;"></span>
                                    </label>
                                <?php endforeach; ?>
                                <label class="luma-color-filter" title="<?php esc_attr_e( 'Any color', 'luma-gallery' ); ?>">
                                    <input type="radio" name="color" value="" checked class="luma-color-filter__input" aria-label="<?php esc_attr_e( 'Any color', 'luma-gallery' ); ?>">
                                    <span class="luma-color-filter__swatch luma-color-filter__swatch--any">×</span>
                                </label>
                            </div>
                        </div>

                        <!-- Artist -->
                        <div class="luma-filter-group">
                            <label class="luma-filter-group__label" for="filter-artist"><?php esc_html_e( 'Artist', 'luma-gallery' ); ?></label>
                            <select id="filter-artist" name="artist" class="luma-select luma-select--full">
                                <option value=""><?php esc_html_e( 'All artists', 'luma-gallery' ); ?></option>
                                <?php foreach ( $filter_artists as $a ) : ?>
                                    <option value="<?php echo esc_attr( $a ); ?>"><?php echo esc_html( $a ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="reset" class="luma-button luma-button--ghost luma-button--sm luma-filter-reset">
                            <?php esc_html_e( 'Reset all filters', 'luma-gallery' ); ?>
                        </button>

                    </form>
                </aside>

                <!-- ─── Grid area ─────────────────────────────────────────── -->
                <div class="luma-gallery-main">

                    <!-- Empty state -->
                    <div class="luma-gallery-empty js-gallery-empty" style="display:none;" aria-live="polite" role="status">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        <h3><?php esc_html_e( 'No artworks found', 'luma-gallery' ); ?></h3>
                        <p><?php esc_html_e( 'Try adjusting your filters to discover more works.', 'luma-gallery' ); ?></p>
                    </div>

                    <!-- Artwork grid -->
                    <div class="luma-artwork-grid luma-artwork-grid--4col js-artwork-grid" data-view="grid">

                        <?php if ( $use_demo ) : ?>
                            <?php foreach ( $demo_artworks as $aw ) : ?>
                                <?php get_template_part( 'template-parts/artwork-card', null, [
                                    'post_id'     => 0,
                                    'title'       => $aw['title'],
                                    'artist_name' => $aw['artist'],
                                    'price'       => $aw['price'],
                                    'price_raw'   => $aw['price_raw'],
                                    'status'      => $aw['status'],
                                    'style'       => $aw['style'],
                                    'mood'        => $aw['mood'],
                                    'technique'   => $aw['technique'],
                                    'color'       => $aw['color'],
                                    'date'        => $aw['date'],
                                    'gradient'    => $aw['gradient'],
                                    'index'       => $aw['index'],
                                ] ); ?>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <?php while ( $artworks_query->have_posts() ) : $artworks_query->the_post();
                                $pid      = get_the_ID();
                                $meta     = function_exists( 'luma_get_artwork_meta' ) ? luma_get_artwork_meta( $pid ) : [];
                                $style    = function_exists( 'luma_get_artwork_terms' ) ? implode( ', ', luma_get_artwork_terms( $pid, 'luma_style' ) ) : '';
                                $mood     = function_exists( 'luma_get_artwork_terms' ) ? implode( ', ', luma_get_artwork_terms( $pid, 'luma_mood' ) ) : '';
                                $raw_price = (float) get_post_meta( $pid, class_exists( 'WooCommerce' ) ? '_price' : 'artwork_price', true );
                                get_template_part( 'template-parts/artwork-card', null, [
                                    'post_id'     => $pid,
                                    'artist_name' => $meta['artist']    ?? '',
                                    'price'       => $meta['price']     ?? '',
                                    'price_raw'   => $raw_price,
                                    'status'      => $meta['status']    ?? 'available',
                                    'style'       => $style,
                                    'mood'        => $mood,
                                    'technique'   => $meta['technique'] ?? '',
                                    'index'       => $artworks_query->current_post,
                                ] );
                            endwhile;
                            wp_reset_postdata(); ?>
                        <?php endif; ?>

                    </div><!-- .js-artwork-grid -->

                </div><!-- .luma-gallery-main -->

            </div><!-- .luma-gallery-layout -->

        </div><!-- .luma-container -->
    </section><!-- .luma-gallery-body -->

</main>

<?php get_footer(); ?>

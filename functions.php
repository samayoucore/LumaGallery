<?php
/**
 * Luma Gallery — functions.php
 * Theme setup, asset enqueue, image sizes, menus, widget areas.
 * Business logic lives in the luma-core plugin.
 */

defined( 'ABSPATH' ) || exit;

define( 'LUMA_THEME_VERSION', '1.0.0' );
define( 'LUMA_THEME_DIR', get_template_directory() );
define( 'LUMA_THEME_URI', get_template_directory_uri() );

// ─── Theme setup ──────────────────────────────────────────────────────────────

add_action( 'after_setup_theme', function () {

    load_theme_textdomain( 'luma-gallery', LUMA_THEME_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 160,
        'flex-width'  => true,
        'flex-height' => true,
    ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );

    // WooCommerce support — only if the plugin is active
    if ( luma_is_woocommerce_active() ) {
        add_theme_support( 'woocommerce', [
            'thumbnail_image_width' => 600,
            'single_image_width'    => 900,
        ] );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
    }

    // Menus
    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'luma-gallery' ),
        'footer'  => __( 'Footer Navigation', 'luma-gallery' ),
    ] );

    // Image sizes
    add_image_size( 'luma-artwork-card',   600, 700, true );
    add_image_size( 'luma-artwork-single', 900, 900, false );
    add_image_size( 'luma-artist-avatar',  300, 300, true );
    add_image_size( 'luma-artist-cover',   1200, 500, true );
    add_image_size( 'luma-exhibition-cover', 1400, 700, true );
    add_image_size( 'luma-hero',           1920, 1080, true );
} );

// ─── Asset enqueue ────────────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', function () {

    // Google Fonts — Cormorant Garamond + Inter + Playfair Display (hero display)
    wp_enqueue_style(
        'luma-google-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap',
        [],
        null
    );

    // Main compiled CSS
    $css_path = LUMA_THEME_DIR . '/assets/css/main.min.css';
    if ( file_exists( $css_path ) ) {
        wp_enqueue_style(
            'luma-main',
            LUMA_THEME_URI . '/assets/css/main.min.css',
            [ 'luma-google-fonts' ],
            filemtime( $css_path )
        );
    }

    // Main compiled JS
    $js_path = LUMA_THEME_DIR . '/assets/js/main.min.js';
    if ( file_exists( $js_path ) ) {
        wp_enqueue_script(
            'luma-main',
            LUMA_THEME_URI . '/assets/js/main.min.js',
            [],
            filemtime( $js_path ),
            true
        );
    }

    // Localize script — pass WP data to JS
    wp_localize_script( 'luma-main', 'lumaData', [
        'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
        'nonce'          => wp_create_nonce( 'luma_nonce' ),
        'siteUrl'        => get_site_url(),
        'themeUrl'       => LUMA_THEME_URI,
        'isWooActive'    => luma_is_woocommerce_active() ? 'yes' : 'no',
        'cartUrl'        => luma_is_woocommerce_active() ? wc_get_cart_url() : home_url( '/cart/' ),
        'favoritesLabel' => __( 'Favorites', 'luma-gallery' ),
        'currency'       => luma_is_woocommerce_active() ? get_woocommerce_currency_symbol() : '€',
    ] );

} );

// ─── WooCommerce check helper ─────────────────────────────────────────────────

function luma_is_woocommerce_active(): bool {
    return class_exists( 'WooCommerce' );
}

// ─── Excerpt length ───────────────────────────────────────────────────────────

add_filter( 'excerpt_length', fn() => 20 );
add_filter( 'excerpt_more', fn() => '…' );

// ─── Body classes ─────────────────────────────────────────────────────────────

add_filter( 'body_class', function ( array $classes ): array {
    if ( is_front_page() ) {
        $classes[] = 'luma-home';
    }
    if ( luma_is_woocommerce_active() ) {
        $classes[] = 'luma-woo-active';
    }
    return $classes;
} );

// ─── Remove WooCommerce default styles (we use our own) ───────────────────────

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// ─── Widget areas ─────────────────────────────────────────────────────────────

add_action( 'widgets_init', function () {
    register_sidebar( [
        'name'          => __( 'Gallery Sidebar', 'luma-gallery' ),
        'id'            => 'gallery-sidebar',
        'description'   => __( 'Appears on gallery and artwork pages.', 'luma-gallery' ),
        'before_widget' => '<div id="%1$s" class="luma-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="luma-widget__title">',
        'after_title'   => '</h3>',
    ] );
} );

// ─── Template helpers ─────────────────────────────────────────────────────────

/**
 * Get the placeholder gradient for a demo artwork card.
 * Returns an inline style string with a CSS gradient based on index.
 */
function luma_get_placeholder_gradient( int $index = 0 ): string {
    $gradients = [
        'linear-gradient(135deg, #2C2C2A 0%, #4A4845 50%, #1C1C1E 100%)',
        'linear-gradient(160deg, #3A3530 0%, #5C5550 40%, #2A2520 100%)',
        'linear-gradient(120deg, #1E2A3A 0%, #2E4A5A 50%, #1A2030 100%)',
        'linear-gradient(145deg, #2A1E1A 0%, #4A3530 50%, #1E1510 100%)',
        'linear-gradient(135deg, #1A2A1A 0%, #3A4A3A 50%, #141E14 100%)',
        'linear-gradient(150deg, #2A2A1A 0%, #4A4A2A 50%, #1E1E10 100%)',
        'linear-gradient(125deg, #1E1A2A 0%, #3A304A 50%, #14101E 100%)',
        'linear-gradient(140deg, #2A1A2A 0%, #4A3A4A 50%, #1A1020 100%)',
    ];
    return $gradients[ $index % count( $gradients ) ];
}

/**
 * Format a price with currency symbol.
 */
function luma_format_price( float $price, string $currency = '€' ): string {
    return esc_html( $currency . number_format( $price, 0, '.', ' ' ) );
}

/**
 * Get CPT permalink — works with or without WooCommerce.
 */
function luma_get_artwork_url( int $post_id ): string {
    if ( luma_is_woocommerce_active() ) {
        return get_permalink( $post_id );
    }
    return get_permalink( $post_id );
}

// ─── Customizer ─────────────────────────────────────────────────────────────

/**
 * Boolean sanitizer for checkbox controls.
 */
function luma_sanitize_checkbox( $checked ): bool {
    return ( isset( $checked ) && true === (bool) $checked );
}

/**
 * Whether small demo / portfolio disclaimers should be shown.
 * Controlled via Customizer → Luma Gallery Settings (default: on).
 */
function luma_show_demo_notice(): bool {
    return (bool) get_theme_mod( 'luma_show_demo_notice', true );
}

/**
 * Footer tagline — Customizer value with a sensible default.
 */
function luma_get_footer_tagline(): string {
    return (string) get_theme_mod(
        'luma_footer_tagline',
        __( 'A digital gallery for artworks, artists, and immersive discovery.', 'luma-gallery' )
    );
}

add_action( 'customize_register', function ( $wp_customize ): void {

    $wp_customize->add_section( 'luma_gallery_settings', [
        'title'       => __( 'Luma Gallery Settings', 'luma-gallery' ),
        'description' => __( 'Hero image and brand settings for the Luma Gallery theme.', 'luma-gallery' ),
        'priority'    => 30,
    ] );

    // 1. Hero image — read by template-parts/hero.php
    $wp_customize->add_setting( 'luma_hero_image_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'luma_hero_image_url', [
        'label'       => __( 'Hero Image', 'luma-gallery' ),
        'description' => __( 'Optional hero image for the homepage. Falls back to bundled artwork if empty.', 'luma-gallery' ),
        'section'     => 'luma_gallery_settings',
    ] ) );

    // 2. Hero image alt text
    $wp_customize->add_setting( 'luma_hero_image_alt', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'luma_hero_image_alt', [
        'label'       => __( 'Hero Image Alt Text', 'luma-gallery' ),
        'description' => __( 'Describes the hero image for screen readers.', 'luma-gallery' ),
        'section'     => 'luma_gallery_settings',
        'type'        => 'text',
    ] );

    // 3. Demo notice toggle
    $wp_customize->add_setting( 'luma_show_demo_notice', [
        'default'           => true,
        'sanitize_callback' => 'luma_sanitize_checkbox',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'luma_show_demo_notice', [
        'label'       => __( 'Show demo notice', 'luma-gallery' ),
        'description' => __( 'Show small demo/portfolio disclaimers where supported.', 'luma-gallery' ),
        'section'     => 'luma_gallery_settings',
        'type'        => 'checkbox',
    ] );

    // 4. Footer tagline
    $wp_customize->add_setting( 'luma_footer_tagline', [
        'default'           => __( 'A digital gallery for artworks, artists, and immersive discovery.', 'luma-gallery' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'luma_footer_tagline', [
        'label'       => __( 'Footer Tagline', 'luma-gallery' ),
        'description' => __( 'Short brand line shown in the footer.', 'luma-gallery' ),
        'section'     => 'luma_gallery_settings',
        'type'        => 'text',
    ] );
} );

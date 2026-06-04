<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="luma-header js-header" role="banner">
    <div class="luma-header__inner">

        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="luma-header__logo" aria-label="<?php bloginfo( 'name' ); ?>">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="luma-header__logo-text">
                    <span class="luma-header__logo-luma">Luma</span><span class="luma-header__logo-gallery">Gallery</span>
                </span>
            <?php endif; ?>
        </a>

        <!-- Primary navigation -->
        <nav class="luma-header__nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'luma-gallery' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_class'     => 'luma-header__nav-list',
                'container'      => false,
                'fallback_cb'    => 'luma_header_fallback_nav',
            ] );
            ?>
        </nav>

        <!-- Header actions -->
        <div class="luma-header__actions">

            <!-- Search -->
            <button class="luma-header__action-btn js-search-toggle" aria-label="<?php esc_attr_e( 'Open search', 'luma-gallery' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </button>

            <!-- Favorites -->
            <a href="<?php echo esc_url( home_url( '/favorites/' ) ); ?>" class="luma-header__action-btn luma-header__action-btn--favorites" aria-label="<?php esc_attr_e( 'Favorites', 'luma-gallery' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <span class="luma-header__action-count js-favorites-count" style="display:none;">0</span>
            </a>

            <!-- Cart -->
            <a href="<?php echo esc_url( luma_is_woocommerce_active() ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>" class="luma-header__action-btn luma-header__action-btn--cart" aria-label="<?php esc_attr_e( 'Cart', 'luma-gallery' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <?php if ( luma_is_woocommerce_active() ) : ?>
                    <span class="luma-header__action-count js-cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
                <?php else : ?>
                    <span class="luma-header__action-count js-cart-count" style="display:none;">0</span>
                <?php endif; ?>
            </a>

            <!-- Account -->
            <a href="<?php echo esc_url( luma_is_woocommerce_active() ? wc_get_account_endpoint_url( 'dashboard' ) : home_url( '/account/' ) ); ?>" class="luma-header__action-btn" aria-label="<?php esc_attr_e( 'Account', 'luma-gallery' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </a>

            <!-- Become an Artist CTA -->
            <a href="<?php echo esc_url( home_url( '/studio/' ) ); ?>" class="luma-button luma-button--ghost luma-header__cta">
                <?php esc_html_e( 'Become an Artist', 'luma-gallery' ); ?>
            </a>

            <!-- Mobile burger -->
            <button class="luma-header__burger js-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'luma-gallery' ); ?>" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

        </div><!-- .luma-header__actions -->

    </div><!-- .luma-header__inner -->

    <!-- Mobile menu overlay -->
    <div class="luma-header__mobile-menu js-mobile-menu" aria-hidden="true">
        <nav class="luma-header__mobile-nav">
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_class'     => 'luma-header__mobile-nav-list',
                'container'      => false,
                'fallback_cb'    => 'luma_header_fallback_nav',
            ] );
            ?>
            <a href="<?php echo esc_url( home_url( '/studio/' ) ); ?>" class="luma-button luma-button--primary luma-header__mobile-cta">
                <?php esc_html_e( 'Become an Artist', 'luma-gallery' ); ?>
            </a>
        </nav>
    </div>

    <!-- Search overlay -->
    <div class="luma-header__search js-search-overlay" aria-hidden="true">
        <div class="luma-header__search-inner">
            <form class="luma-header__search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input class="luma-header__search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Search artworks, artists…', 'luma-gallery' ); ?>" autocomplete="off">
                <button type="submit" class="luma-header__search-submit" aria-label="<?php esc_attr_e( 'Search', 'luma-gallery' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </button>
            </form>
            <button class="luma-header__search-close js-search-close" aria-label="<?php esc_attr_e( 'Close search', 'luma-gallery' ); ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

</header><!-- .luma-header -->

<?php
function luma_header_fallback_nav(): void {
    echo '<ul class="luma-header__nav-list">';
    $pages = [
        '/gallery/'     => 'Gallery',
        '/artists/'     => 'Artists',
        '/exhibitions/' => 'Exhibitions',
        '/journal/'     => 'Journal',
        '/ai-curator/'  => 'AI Curator',
        '/studio/'      => 'Studio',
        '/about/'       => 'About',
    ];
    foreach ( $pages as $url => $label ) {
        printf(
            '<li class="menu-item"><a href="%s">%s</a></li>',
            esc_url( home_url( $url ) ),
            esc_html( $label )
        );
    }
    echo '</ul>';
}

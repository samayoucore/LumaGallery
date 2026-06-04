
<footer class="luma-footer" role="contentinfo">

    <div class="luma-footer__inner">

        <!-- Brand column -->
        <div class="luma-footer__brand">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="luma-footer__logo">
                <span class="luma-footer__logo-luma">Luma</span><span class="luma-footer__logo-gallery">Gallery</span>
            </a>
            <p class="luma-footer__tagline">
                <?php esc_html_e( 'A premium digital gallery for discovering, curating, and collecting original artworks.', 'luma-gallery' ); ?>
            </p>
            <div class="luma-footer__social">
                <a href="#" class="luma-footer__social-link" aria-label="Instagram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
                <a href="#" class="luma-footer__social-link" aria-label="Pinterest">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 12a4 4 0 1 0 8 0 4 4 0 0 0-8 0z"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M9 15.5c-.5 2-1 3.5-1 3.5s2.5-1 4-4c0 0-1 0-3-2s-2-4-2-4"/></svg>
                </a>
                <a href="#" class="luma-footer__social-link" aria-label="Twitter / X">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4l16 16M4 20L20 4"/></svg>
                </a>
            </div>
        </div>

        <!-- Navigation columns -->
        <div class="luma-footer__nav-col">
            <h4 class="luma-footer__nav-title"><?php esc_html_e( 'Explore', 'luma-gallery' ); ?></h4>
            <ul class="luma-footer__nav-list">
                <li><a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Gallery', 'luma-gallery' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/artists/' ) ); ?>"><?php esc_html_e( 'Artists', 'luma-gallery' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/exhibitions/' ) ); ?>"><?php esc_html_e( 'Exhibitions', 'luma-gallery' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'Journal', 'luma-gallery' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/ai-curator/' ) ); ?>"><?php esc_html_e( 'AI Curator', 'luma-gallery' ); ?></a></li>
            </ul>
        </div>

        <div class="luma-footer__nav-col">
            <h4 class="luma-footer__nav-title"><?php esc_html_e( 'Artists', 'luma-gallery' ); ?></h4>
            <ul class="luma-footer__nav-list">
                <li><a href="<?php echo esc_url( home_url( '/studio/' ) ); ?>"><?php esc_html_e( 'Artist Studio', 'luma-gallery' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/artists/' ) ); ?>"><?php esc_html_e( 'Meet the Artists', 'luma-gallery' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Luma', 'luma-gallery' ); ?></a></li>
            </ul>
        </div>

        <div class="luma-footer__nav-col">
            <h4 class="luma-footer__nav-title"><?php esc_html_e( 'Newsletter', 'luma-gallery' ); ?></h4>
            <p class="luma-footer__newsletter-text">
                <?php esc_html_e( 'Get curated picks and new exhibitions delivered to your inbox.', 'luma-gallery' ); ?>
            </p>
            <form class="luma-footer__newsletter-form js-newsletter-form">
                <input
                    type="email"
                    class="luma-footer__newsletter-input"
                    placeholder="<?php esc_attr_e( 'your@email.com', 'luma-gallery' ); ?>"
                    required
                >
                <button type="submit" class="luma-button luma-button--primary luma-footer__newsletter-btn">
                    <?php esc_html_e( 'Subscribe', 'luma-gallery' ); ?>
                </button>
            </form>
        </div>

    </div><!-- .luma-footer__inner -->

    <!-- Oversized editorial wordmark -->
    <div class="luma-footer__wordmark-row">
        <span class="luma-footer__wordmark js-distort-text" aria-hidden="true">LUMA GALLERY</span>
    </div>

    <!-- Bottom bar -->
    <div class="luma-footer__bottom">
        <div class="luma-footer__bottom-inner">
            <p class="luma-footer__copyright">
                &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Luma Gallery.
                <?php esc_html_e( 'All rights reserved.', 'luma-gallery' ); ?>
            </p>
            <p class="luma-footer__disclaimer">
                <?php esc_html_e( 'Demo project — no real payments are processed. AI features are rule-based simulations, not real AI APIs.', 'luma-gallery' ); ?>
            </p>
            <nav class="luma-footer__legal" aria-label="<?php esc_attr_e( 'Legal navigation', 'luma-gallery' ); ?>">
                <?php
                wp_nav_menu( [
                    'theme_location' => 'footer',
                    'menu_class'     => 'luma-footer__legal-list',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => function () {
                        echo '<ul class="luma-footer__legal-list">';
                        echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">' . esc_html__( 'About', 'luma-gallery' ) . '</a></li>';
                        echo '</ul>';
                    },
                ] );
                ?>
            </nav>
        </div>
    </div><!-- .luma-footer__bottom -->

</footer><!-- .luma-footer -->

<?php wp_footer(); ?>

</body>
</html>

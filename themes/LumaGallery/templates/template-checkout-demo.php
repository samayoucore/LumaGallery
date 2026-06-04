<?php
/**
 * Template Name: Demo Checkout
 *
 * Simulated checkout (no WooCommerce, no real payment). PHP renders the form
 * shell; src/js/modules/checkout.js fills the summary, validates, persists the
 * order to `luma_demo_orders`, clears the cart and shows the success state.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="luma-checkout js-checkout-page" id="main-content">

    <!-- ─── Hero ─────────────────────────────────────────────────────────── -->
    <section class="luma-checkout__hero">
        <span class="luma-checkout__word" aria-hidden="true">ORDER</span>
        <div class="luma-container">
            <p class="luma-checkout__eyebrow"><?php esc_html_e( 'Demo Checkout', 'luma-gallery' ); ?></p>
            <h1 class="luma-checkout__title"><?php esc_html_e( 'Demo Checkout', 'luma-gallery' ); ?></h1>
            <p class="luma-checkout__subtitle"><?php esc_html_e( 'Complete a simulated order for your selected artworks.', 'luma-gallery' ); ?></p>
            <p class="luma-checkout__disclaimer">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php esc_html_e( 'No real payment will be processed.', 'luma-gallery' ); ?>
            </p>
        </div>
    </section>

    <!-- ─── Body ─────────────────────────────────────────────────────────── -->
    <section class="luma-checkout__body">
        <div class="luma-container">

            <!-- Empty cart state (shown by JS when cart is empty) -->
            <div class="luma-checkout__empty js-checkout-empty" hidden>
                <h2 class="luma-checkout__empty-title"><?php esc_html_e( 'Your cart is empty', 'luma-gallery' ); ?></h2>
                <p class="luma-checkout__empty-text"><?php esc_html_e( 'Add an artwork before starting the demo checkout.', 'luma-gallery' ); ?></p>
                <a class="luma-button luma-button--accent" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Return to Gallery', 'luma-gallery' ); ?></a>
            </div>

            <!-- Main: form + summary -->
            <div class="luma-checkout__layout js-checkout-main">

                <form class="luma-checkout__form js-checkout-form" novalidate>

                    <!-- Contact -->
                    <fieldset class="luma-checkout__section">
                        <legend class="luma-checkout__section-title"><span>01</span><?php esc_html_e( 'Contact Information', 'luma-gallery' ); ?></legend>
                        <div class="luma-checkout__grid">
                            <div class="luma-checkout__field luma-checkout__field--wide">
                                <label for="co-name"><?php esc_html_e( 'Full name', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="co-name" name="name" autocomplete="name" required>
                            </div>
                            <div class="luma-checkout__field">
                                <label for="co-email"><?php esc_html_e( 'Email', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="email" id="co-email" name="email" autocomplete="email" required>
                            </div>
                            <div class="luma-checkout__field">
                                <label for="co-phone"><?php esc_html_e( 'Phone', 'luma-gallery' ); ?> <span class="luma-checkout__optional"><?php esc_html_e( '(optional)', 'luma-gallery' ); ?></span></label>
                                <input class="luma-input" type="tel" id="co-phone" name="phone" autocomplete="tel">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Shipping -->
                    <fieldset class="luma-checkout__section">
                        <legend class="luma-checkout__section-title"><span>02</span><?php esc_html_e( 'Shipping Address', 'luma-gallery' ); ?></legend>
                        <div class="luma-checkout__grid">
                            <div class="luma-checkout__field">
                                <label for="co-country"><?php esc_html_e( 'Country', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="co-country" name="country" autocomplete="country-name" required>
                            </div>
                            <div class="luma-checkout__field">
                                <label for="co-city"><?php esc_html_e( 'City', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="co-city" name="city" autocomplete="address-level2" required>
                            </div>
                            <div class="luma-checkout__field luma-checkout__field--wide">
                                <label for="co-street"><?php esc_html_e( 'Street address', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="co-street" name="street" autocomplete="address-line1" required>
                            </div>
                            <div class="luma-checkout__field">
                                <label for="co-postal"><?php esc_html_e( 'Postal code', 'luma-gallery' ); ?></label>
                                <input class="luma-input" type="text" id="co-postal" name="postal" autocomplete="postal-code" required>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Delivery -->
                    <fieldset class="luma-checkout__section">
                        <legend class="luma-checkout__section-title"><span>03</span><?php esc_html_e( 'Delivery Method', 'luma-gallery' ); ?></legend>
                        <div class="luma-checkout__delivery-group js-delivery-group" role="radiogroup" aria-label="<?php esc_attr_e( 'Delivery method', 'luma-gallery' ); ?>">
                            <?php
                            $delivery = [
                                [ 'value' => 'standard', 'label' => __( 'Standard Art Shipping', 'luma-gallery' ), 'desc' => __( 'Tracked, 5–9 business days', 'luma-gallery' ), 'price' => 25, 'checked' => true ],
                                [ 'value' => 'premium',  'label' => __( 'Premium Insured Shipping', 'luma-gallery' ), 'desc' => __( 'Climate-controlled, fully insured', 'luma-gallery' ), 'price' => 45, 'checked' => false ],
                                [ 'value' => 'pickup',   'label' => __( 'Local Gallery Pickup', 'luma-gallery' ), 'desc' => __( 'Collect in person at the gallery', 'luma-gallery' ), 'price' => 0,  'checked' => false ],
                            ];
                            foreach ( $delivery as $d ) : ?>
                                <label class="luma-checkout__delivery">
                                    <input type="radio" name="delivery" value="<?php echo esc_attr( $d['value'] ); ?>" data-price="<?php echo esc_attr( $d['price'] ); ?>" <?php checked( $d['checked'] ); ?>>
                                    <span class="luma-checkout__delivery-body">
                                        <span class="luma-checkout__delivery-label"><?php echo esc_html( $d['label'] ); ?></span>
                                        <span class="luma-checkout__delivery-desc"><?php echo esc_html( $d['desc'] ); ?></span>
                                    </span>
                                    <span class="luma-checkout__delivery-price"><?php echo $d['price'] ? esc_html( '€ ' . $d['price'] ) : esc_html__( 'Free', 'luma-gallery' ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <!-- Demo payment -->
                    <fieldset class="luma-checkout__section">
                        <legend class="luma-checkout__section-title"><span>04</span><?php esc_html_e( 'Demo Payment', 'luma-gallery' ); ?></legend>
                        <div class="luma-checkout__payment-demo">
                            <div class="luma-checkout__card" aria-hidden="true">
                                <div class="luma-checkout__card-top">
                                    <span class="luma-checkout__card-brand"><?php esc_html_e( 'Luma Demo Payment', 'luma-gallery' ); ?></span>
                                    <span class="luma-checkout__card-chip"></span>
                                </div>
                                <div class="luma-checkout__card-number">4242 4242 4242 4242</div>
                                <div class="luma-checkout__card-foot">
                                    <span>12 / 30</span>
                                    <span>CVC 123</span>
                                </div>
                            </div>
                            <div class="luma-checkout__payment-fields">
                                <div class="luma-checkout__field">
                                    <label for="co-card-name"><?php esc_html_e( 'Cardholder name', 'luma-gallery' ); ?></label>
                                    <input class="luma-input" type="text" id="co-card-name" name="cardName" placeholder="<?php esc_attr_e( 'Optional — demo only', 'luma-gallery' ); ?>">
                                </div>
                                <div class="luma-checkout__field">
                                    <label for="co-card-number"><?php esc_html_e( 'Card number', 'luma-gallery' ); ?></label>
                                    <input class="luma-input" type="text" id="co-card-number" value="4242 4242 4242 4242" disabled aria-disabled="true">
                                </div>
                                <div class="luma-checkout__field luma-checkout__field--half">
                                    <label for="co-card-exp"><?php esc_html_e( 'Expiry', 'luma-gallery' ); ?></label>
                                    <input class="luma-input" type="text" id="co-card-exp" value="12 / 30" disabled aria-disabled="true">
                                </div>
                                <div class="luma-checkout__field luma-checkout__field--half">
                                    <label for="co-card-cvc"><?php esc_html_e( 'CVC', 'luma-gallery' ); ?></label>
                                    <input class="luma-input" type="text" id="co-card-cvc" value="123" disabled aria-disabled="true">
                                </div>
                            </div>
                            <p class="luma-checkout__payment-note"><?php esc_html_e( 'This payment method is simulated for portfolio demonstration. No card is charged.', 'luma-gallery' ); ?></p>
                        </div>
                    </fieldset>

                    <button type="submit" class="luma-button luma-button--accent luma-button--lg luma-checkout__submit">
                        <?php esc_html_e( 'Place Demo Order', 'luma-gallery' ); ?>
                    </button>

                </form>

                <!-- Summary (filled by checkout.js) -->
                <aside class="luma-checkout__summary js-checkout-summary" aria-label="<?php esc_attr_e( 'Order summary', 'luma-gallery' ); ?>"></aside>

            </div><!-- .js-checkout-main -->

            <!-- Success (shown by JS after a successful order) -->
            <div class="luma-checkout__success js-checkout-success" hidden>
                <div class="luma-checkout__success-inner">
                    <span class="luma-checkout__success-mark" aria-hidden="true">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <p class="luma-checkout__success-eyebrow"><?php esc_html_e( 'Confirmation', 'luma-gallery' ); ?></p>
                    <h2 class="luma-checkout__success-title"><?php esc_html_e( 'Demo order confirmed', 'luma-gallery' ); ?></h2>
                    <p class="luma-checkout__success-text"><?php esc_html_e( 'Your simulated order has been created successfully. No payment was processed.', 'luma-gallery' ); ?></p>

                    <dl class="luma-checkout__success-details">
                        <div><dt><?php esc_html_e( 'Order number', 'luma-gallery' ); ?></dt><dd class="js-success-number">—</dd></div>
                        <div><dt><?php esc_html_e( 'Date', 'luma-gallery' ); ?></dt><dd class="js-success-date">—</dd></div>
                        <div><dt><?php esc_html_e( 'Name', 'luma-gallery' ); ?></dt><dd class="js-success-name">—</dd></div>
                        <div><dt><?php esc_html_e( 'Email', 'luma-gallery' ); ?></dt><dd class="js-success-email">—</dd></div>
                        <div><dt><?php esc_html_e( 'Delivery', 'luma-gallery' ); ?></dt><dd class="js-success-delivery">—</dd></div>
                        <div><dt><?php esc_html_e( 'Total', 'luma-gallery' ); ?></dt><dd class="js-success-total">—</dd></div>
                    </dl>

                    <ol class="luma-order-timeline">
                        <li class="luma-order-timeline__step is-done"><?php esc_html_e( 'Order created', 'luma-gallery' ); ?></li>
                        <li class="luma-order-timeline__step is-done"><?php esc_html_e( 'Demo payment confirmed', 'luma-gallery' ); ?></li>
                        <li class="luma-order-timeline__step"><?php esc_html_e( 'Preparing artwork', 'luma-gallery' ); ?></li>
                        <li class="luma-order-timeline__step"><?php esc_html_e( 'Ready for shipping', 'luma-gallery' ); ?></li>
                    </ol>

                    <div class="luma-checkout__success-actions">
                        <a class="luma-button luma-button--accent" href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'Explore Gallery', 'luma-gallery' ); ?></a>
                        <a class="luma-button luma-button--ghost" href="<?php echo esc_url( home_url( '/favorites/' ) ); ?>"><?php esc_html_e( 'View Saved Collection', 'luma-gallery' ); ?></a>
                    </div>
                </div>
            </div>

        </div><!-- .luma-container -->
    </section>

</main>

<?php get_footer(); ?>

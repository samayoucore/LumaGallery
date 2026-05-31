<?php
/**
 * Popular Artworks section — homepage.
 */

$artworks = function_exists( 'luma_get_demo_artworks' ) ? luma_get_demo_artworks( 8 ) : [];
?>

<section class="luma-section luma-popular-artworks" id="popular-artworks">
    <div class="luma-container">

        <div class="luma-section__header luma-section__header--split">
            <div>
                <span class="luma-section__label"><?php esc_html_e( 'Most Collected', 'luma-gallery' ); ?></span>
                <h2 class="luma-heading luma-heading--section"><?php esc_html_e( 'Popular Artworks', 'luma-gallery' ); ?></h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="luma-button luma-button--ghost">
                <?php esc_html_e( 'View All', 'luma-gallery' ); ?>
            </a>
        </div>

        <div class="luma-artwork-grid luma-artwork-grid--4col">
            <?php if ( ! empty( $artworks ) ) : ?>
                <?php foreach ( $artworks as $i => $artwork ) :
                    get_template_part( 'template-parts/artwork-card', null, [
                        'post_id'     => $artwork['id']     ?? 0,
                        'title'       => $artwork['title']  ?? '',
                        'artist_name' => $artwork['artist'] ?? '',
                        'price'       => $artwork['price']  ?? '',
                        'status'      => $artwork['status'] ?? 'available',
                        'gradient'    => $artwork['gradient'] ?? luma_get_placeholder_gradient( $i ),
                        'index'       => $i,
                    ] );
                endforeach; ?>
            <?php else : ?>
                <?php
                $demo_artworks = [
                    [ 'title' => 'Silent Morning',  'artist' => 'Elena Morozova', 'price' => '€ 1 200', 'status' => 'available', 'g' => luma_get_placeholder_gradient( 0 ) ],
                    [ 'title' => 'Blue Interior',   'artist' => 'Victor Hale',    'price' => '€ 980',   'status' => 'available', 'g' => luma_get_placeholder_gradient( 1 ) ],
                    [ 'title' => 'Nocturne Field',  'artist' => 'Mira Solen',     'price' => '€ 2 400', 'status' => 'reserved',  'g' => luma_get_placeholder_gradient( 2 ) ],
                    [ 'title' => 'Soft Gravity',    'artist' => 'Anna Weiss',     'price' => '€ 750',   'status' => 'available', 'g' => luma_get_placeholder_gradient( 3 ) ],
                    [ 'title' => 'The Last Window', 'artist' => 'Daniel Arno',    'price' => '€ 3 200', 'status' => 'available', 'g' => luma_get_placeholder_gradient( 4 ) ],
                    [ 'title' => 'Warm Distance',   'artist' => 'Sofia Lumen',    'price' => '€ 1 100', 'status' => 'sold',      'g' => luma_get_placeholder_gradient( 5 ) ],
                    [ 'title' => 'Pale Garden',     'artist' => 'Elena Morozova', 'price' => '€ 890',   'status' => 'available', 'g' => luma_get_placeholder_gradient( 6 ) ],
                    [ 'title' => 'After the Rain',  'artist' => 'Victor Hale',    'price' => '€ 1 560', 'status' => 'available', 'g' => luma_get_placeholder_gradient( 7 ) ],
                ];
                foreach ( $demo_artworks as $i => $a ) :
                    get_template_part( 'template-parts/artwork-card', null, [
                        'post_id'     => 0,
                        'title'       => $a['title'],
                        'artist_name' => $a['artist'],
                        'price'       => $a['price'],
                        'status'      => $a['status'],
                        'gradient'    => $a['g'],
                        'index'       => $i,
                    ] );
                endforeach;
                ?>
            <?php endif; ?>
        </div>

    </div>
</section>

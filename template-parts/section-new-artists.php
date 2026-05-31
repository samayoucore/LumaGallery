<?php
/**
 * New Artists section — homepage.
 */

$artists = function_exists( 'luma_get_demo_artists' ) ? luma_get_demo_artists( 6 ) : [];
?>

<section class="luma-section luma-new-artists" id="new-artists">
    <div class="luma-container">

        <div class="luma-section__header luma-section__header--split">
            <div>
                <span class="luma-section__label"><?php esc_html_e( 'Recently Joined', 'luma-gallery' ); ?></span>
                <h2 class="luma-heading luma-heading--section"><?php esc_html_e( 'New Artists', 'luma-gallery' ); ?></h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/artists/' ) ); ?>" class="luma-button luma-button--ghost">
                <?php esc_html_e( 'All Artists', 'luma-gallery' ); ?>
            </a>
        </div>

        <div class="luma-artist-grid luma-artist-grid--3col">
            <?php if ( ! empty( $artists ) ) : ?>
                <?php foreach ( $artists as $i => $artist ) :
                    get_template_part( 'template-parts/artist-card', null, [
                        'post_id'        => $artist['id']       ?? 0,
                        'name'           => $artist['name']     ?? '',
                        'location'       => $artist['location'] ?? '',
                        'short_bio'      => $artist['bio']      ?? '',
                        'artworks_count' => $artist['artworks'] ?? 0,
                        'gradient'       => $artist['gradient'] ?? luma_get_placeholder_gradient( $i ),
                        'index'          => $i,
                    ] );
                endforeach; ?>
            <?php else : ?>
                <?php
                $demo_artists = [
                    [ 'name' => 'Elena Morozova', 'location' => 'Moscow, Russia',  'bio' => 'Painter of light and quiet domestic interiors. Working in oil on linen.', 'artworks' => 14 ],
                    [ 'name' => 'Victor Hale',    'location' => 'London, UK',      'bio' => 'Urban landscapes and atmospheric blues. Works with acrylic and ink.', 'artworks' => 9 ],
                    [ 'name' => 'Mira Solen',     'location' => 'Paris, France',   'bio' => 'Abstract painter focused on emotional color fields and gestural marks.', 'artworks' => 11 ],
                    [ 'name' => 'Anna Weiss',     'location' => 'Berlin, Germany', 'bio' => 'Minimalist print-maker exploring form, pattern and negative space.', 'artworks' => 7 ],
                    [ 'name' => 'Daniel Arno',    'location' => 'NY, USA',         'bio' => 'Figurative work between surrealism and memory — oil and charcoal.', 'artworks' => 18 ],
                    [ 'name' => 'Sofia Lumen',    'location' => 'Milan, Italy',    'bio' => 'Light and glass studies in watercolor. Quiet, luminous, meditative.', 'artworks' => 6 ],
                ];
                foreach ( $demo_artists as $i => $a ) :
                    get_template_part( 'template-parts/artist-card', null, [
                        'post_id'        => 0,
                        'name'           => $a['name'],
                        'location'       => $a['location'],
                        'short_bio'      => $a['bio'],
                        'artworks_count' => $a['artworks'],
                        'gradient'       => luma_get_placeholder_gradient( $i ),
                        'index'          => $i,
                    ] );
                endforeach;
                ?>
            <?php endif; ?>
        </div>

        <!-- Become an Artist CTA -->
        <div class="luma-new-artists__cta">
            <p class="luma-new-artists__cta-text">
                <?php esc_html_e( 'Are you an artist? Join Luma Gallery and reach new collectors.', 'luma-gallery' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/artist-studio/' ) ); ?>" class="luma-button luma-button--primary">
                <?php esc_html_e( 'Become an Artist', 'luma-gallery' ); ?>
            </a>
        </div>

    </div>
</section>

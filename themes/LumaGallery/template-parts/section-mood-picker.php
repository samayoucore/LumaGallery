<?php
/**
 * Mood picker section — Find Art by Mood.
 */

$moods = [
    [ 'slug' => 'calm',        'label' => 'Calm',        'icon' => '☁', 'desc' => 'Soft tones, quiet compositions' ],
    [ 'slug' => 'dark',        'label' => 'Dark',        'icon' => '◾', 'desc' => 'Depth, shadow, introspection' ],
    [ 'slug' => 'romantic',    'label' => 'Romantic',    'icon' => '✿', 'desc' => 'Warm light, tender moments' ],
    [ 'slug' => 'minimal',     'label' => 'Minimal',     'icon' => '▫', 'desc' => 'Space, silence, reduction' ],
    [ 'slug' => 'bright',      'label' => 'Bright',      'icon' => '◎', 'desc' => 'Energetic color, vivid life' ],
    [ 'slug' => 'melancholic', 'label' => 'Melancholic', 'icon' => '◑', 'desc' => 'Nostalgia, fog, distance' ],
    [ 'slug' => 'abstract',    'label' => 'Abstract',    'icon' => '◈', 'desc' => 'Form, gesture, interpretation' ],
    [ 'slug' => 'atmospheric', 'label' => 'Atmospheric', 'icon' => '◎', 'desc' => 'Mist, layers, immersion' ],
];
?>

<section class="luma-section luma-mood-picker" id="mood-picker">
    <div class="luma-container">

        <div class="luma-section__header">
            <span class="luma-section__label"><?php esc_html_e( 'Find Your Art', 'luma-gallery' ); ?></span>
            <h2 class="luma-heading luma-heading--section"><?php esc_html_e( 'Browse by Mood', 'luma-gallery' ); ?></h2>
            <p class="luma-section__sub"><?php esc_html_e( 'How do you want to feel? Choose a mood and discover artworks that match your state of mind.', 'luma-gallery' ); ?></p>
        </div>

        <div class="luma-mood-picker__grid">
            <?php foreach ( $moods as $mood ) :
                $url = add_query_arg( 'mood', $mood['slug'], home_url( '/gallery/' ) );
            ?>
            <a
                href="<?php echo esc_url( $url ); ?>"
                class="luma-mood-picker__item js-mood-item"
                data-mood="<?php echo esc_attr( $mood['slug'] ); ?>"
            >
                <span class="luma-mood-picker__icon" aria-hidden="true"><?php echo esc_html( $mood['icon'] ); ?></span>
                <span class="luma-mood-picker__label"><?php echo esc_html( $mood['label'] ); ?></span>
                <span class="luma-mood-picker__desc"><?php echo esc_html( $mood['desc'] ); ?></span>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>

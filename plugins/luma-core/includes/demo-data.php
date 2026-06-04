<?php
/**
 * Luma Core — Demo Data helpers for the Journal.
 *
 * These provide a graceful fallback so the Journal archive and single
 * templates always render something premium, even before any real
 * `luma_artist_post` records exist. Real records always take precedence.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Demo Journal posts. Each entry mirrors the shape returned by
 * luma_core_normalize_post() so templates can treat both identically.
 *
 * @return array<int,array<string,mixed>>
 */
function luma_get_demo_artist_posts(): array {

    $base = function_exists( 'home_url' ) ? home_url( '/journal/' ) : '#';

    $posts = [
        [
            'title'   => 'Inside the Studio: Elena Morozova',
            'excerpt' => 'A morning among unfinished canvases, cold coffee and northern light — how Elena builds silence into paint.',
            'topic'   => 'Artist Stories',
            'artist'  => 'Elena Morozova',
            'date'    => 'May 28, 2025',
            'gradient'=> 'linear-gradient(135deg, #2C2C2A 0%, #4A4845 50%, #1C1C1E 100%)',
            'content' => "<p>The studio sits at the top of a converted textile factory, and at eight in the morning the light comes in flat and grey — the kind of light Elena Morozova has spent twenty years learning to trust.</p><p>“People think calm is empty,” she says, wiping a brush against her sleeve. “It isn't. Calm is everything held very still.” Her canvases are built in dozens of thin, translucent layers, each one barely altering the last.</p><p>We talked for two hours about restraint, about knowing when a painting is finished, and about the discipline of leaving things out.</p>",
        ],
        [
            'title'   => 'How to Choose Art for a Quiet Interior',
            'excerpt' => 'Scale, palette and negative space — a practical guide to selecting work that lets a calm room breathe.',
            'topic'   => 'Collecting Art',
            'artist'  => 'Luma Editorial',
            'date'    => 'May 19, 2025',
            'gradient'=> 'linear-gradient(160deg, #3A3530 0%, #5C5550 40%, #2A2520 100%)',
            'content' => "<p>A quiet interior is not a blank one. The goal is resonance, not silence — a single work that gives the eye somewhere to rest.</p><p>Start with scale. A piece that is slightly too large almost always reads better than one that is slightly too small. Then consider palette: pull one tone from the room and let the artwork echo it.</p><p>Finally, leave space around the work. Negative space is part of the composition.</p>",
        ],
        [
            'title'   => 'Night Silence: Curator Notes',
            'excerpt' => 'Behind the dark-toned exhibition that asked visitors to slow down and look at what the night conceals.',
            'topic'   => 'Exhibitions',
            'artist'  => 'Mira Solen',
            'date'    => 'May 11, 2025',
            'gradient'=> 'linear-gradient(120deg, #1E2A3A 0%, #2E4A5A 50%, #1A2030 100%)',
            'content' => "<p><em>Night Silence</em> began as a question: what do we stop seeing when the lights go down? The exhibition gathered eleven works in deep, low-key palettes and hung them in near-darkness.</p><p>Visitors were handed no wall text. Instead they were asked to spend a full minute with each piece before moving on — an instruction that, at first, made people deeply uncomfortable.</p><p>By the end of the run, the guestbook was full of single words: <em>quiet, heavy, held, awake.</em></p>",
        ],
        [
            'title'   => 'The Story Behind “Soft Gravity”',
            'excerpt' => 'Anna Weiss on the ink drawing that almost ended up in the bin — and the accident that made it.',
            'topic'   => 'Studio Notes',
            'artist'  => 'Anna Weiss',
            'date'    => 'April 30, 2025',
            'gradient'=> 'linear-gradient(145deg, #2A1E1A 0%, #4A3530 50%, #1E1510 100%)',
            'content' => "<p>“Soft Gravity” was a mistake. Anna Weiss had been working on something else entirely when a loaded brush slipped and laid a single grey arc across the page.</p><p>“I almost threw it away,” she admits. “Then I left it on the floor for a week and kept walking past it. By the seventh day I understood it was finished, and that I hadn't really made it — I'd just allowed it.”</p>",
        ],
        [
            'title'   => 'Collecting Original Art in a Digital Gallery',
            'excerpt' => 'Provenance, trust and the new etiquette of buying one-of-one works online without ever touching them.',
            'topic'   => 'Collecting Art',
            'artist'  => 'Luma Editorial',
            'date'    => 'April 22, 2025',
            'gradient'=> 'linear-gradient(135deg, #1A2A1A 0%, #3A4A3A 50%, #141E14 100%)',
            'content' => "<p>Buying original art online used to feel like a contradiction. How do you trust the surface of a thing you can only see as pixels?</p><p>The answer turns out to be the same as it always was: provenance, transparency and relationships. A good digital gallery shows you the back of the canvas, the certificate, the artist's own words.</p><p>The screen is just the introduction. The work still arrives in a crate.</p>",
        ],
        [
            'title'   => 'Between Two Windows: A Painter’s Light',
            'excerpt' => 'Daniel Arno on why he has painted the same corner of his apartment for nine years running.',
            'topic'   => 'Artist Stories',
            'artist'  => 'Daniel Arno',
            'date'    => 'April 9, 2025',
            'gradient'=> 'linear-gradient(125deg, #1E1A2A 0%, #3A304A 50%, #14101E 100%)',
            'content' => "<p>For nine years, Daniel Arno has painted the same corner — two windows, a chair, the wall between them. The light is never the same twice.</p><p>“It's not the corner I'm interested in,” he says. “It's the time. Each painting is really a portrait of an hour that will never come back.”</p>",
        ],
    ];

    // Attach derived fields so demo posts share the normalized shape.
    foreach ( $posts as $i => &$p ) {
        $p['id']         = 0;
        $p['is_demo']    = true;
        $p['image']      = '';
        $p['url']        = $base;
        $p['topic_slug'] = sanitize_title( $p['topic'] );
        $p['artist_url'] = function_exists( 'home_url' ) ? home_url( '/artists/' ) : '#';
        $p['index']      = $i;
    }
    unset( $p );

    return $posts;
}

/**
 * Normalize a real WP_Post (luma_artist_post) into the shared view shape.
 *
 * @param int|WP_Post $post
 * @return array<string,mixed>
 */
function luma_core_normalize_post( $post ): array {
    $post = get_post( $post );
    if ( ! $post ) {
        $demo = luma_get_demo_artist_posts();
        return $demo[0];
    }

    $topic_label = '';
    $topic_slug  = '';
    $terms = get_the_terms( $post->ID, 'luma_post_topic' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        $topic_label = $terms[0]->name;
        $topic_slug  = $terms[0]->slug;
    }

    $author_id = (int) $post->post_author;
    $artist    = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';
    // Optional meta override for the by-line.
    $artist_meta = get_post_meta( $post->ID, 'luma_post_artist', true );
    if ( $artist_meta ) {
        $artist = $artist_meta;
    }

    return [
        'id'         => $post->ID,
        'title'      => get_the_title( $post ),
        'excerpt'    => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' ),
        'content'    => apply_filters( 'the_content', $post->post_content ),
        'date'       => get_the_date( '', $post ),
        'topic'      => $topic_label,
        'topic_slug' => $topic_slug,
        'artist'     => $artist,
        'artist_url' => home_url( '/artists/' ),
        'url'        => get_permalink( $post ),
        'gradient'   => function_exists( 'luma_get_placeholder_gradient' ) ? luma_get_placeholder_gradient( $post->ID ) : 'linear-gradient(135deg,#2C2C2A,#4A4845)',
        'image'      => has_post_thumbnail( $post ) ? (string) get_the_post_thumbnail_url( $post, 'luma-exhibition-cover' ) : '',
        'is_demo'    => false,
        'index'      => 0,
    ];
}

/**
 * Get the featured Journal post: newest real post if any, else first demo post.
 *
 * @return array<string,mixed>
 */
function luma_get_featured_artist_post(): array {
    if ( post_type_exists( 'luma_artist_post' ) ) {
        $q = new WP_Query( [
            'post_type'      => 'luma_artist_post',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        if ( $q->have_posts() ) {
            $view = luma_core_normalize_post( $q->posts[0] );
            wp_reset_postdata();
            return $view;
        }
    }

    $demo = luma_get_demo_artist_posts();
    return $demo[0];
}

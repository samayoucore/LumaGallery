<?php
/**
 * Luma Core — Demo Content Seeder.
 *
 * Creates the demo pages (with the right Page Templates), default Journal
 * topics and a handful of demo Journal posts so the gallery can be brought to a
 * presentable state in one click. Everything is idempotent and reversible:
 * seeded posts/pages are tagged with the `_luma_demo_content` meta so the
 * Clear action only ever removes content this seeder created.
 *
 * No external requests, no image downloads, no WooCommerce dependency.
 */

defined( 'ABSPATH' ) || exit;

const LUMA_DEMO_FLAG = '_luma_demo_content';

/**
 * Canonical demo pages: slug => [ title, page-template (theme-relative) ].
 * Shared by the seeder and the admin status overview.
 *
 * @return array<string,array{title:string,template:string}>
 */
function luma_demo_get_pages(): array {
    return [
        'gallery'     => [ 'title' => 'Gallery',     'template' => 'templates/template-gallery.php' ],
        'artists'     => [ 'title' => 'Artists',     'template' => 'templates/template-artists.php' ],
        'exhibitions' => [ 'title' => 'Exhibitions', 'template' => 'templates/template-exhibitions.php' ],
        'favorites'   => [ 'title' => 'Favorites',   'template' => 'templates/template-favorites.php' ],
        'ai-curator'  => [ 'title' => 'AI Curator',  'template' => 'templates/template-ai-curator.php' ],
        'cart'        => [ 'title' => 'Cart',        'template' => 'templates/template-cart-demo.php' ],
        'checkout'    => [ 'title' => 'Checkout',    'template' => 'templates/template-checkout-demo.php' ],
        'account'     => [ 'title' => 'Account',     'template' => 'templates/template-account.php' ],
        'studio'      => [ 'title' => 'Studio',      'template' => 'templates/template-artist-studio.php' ],
        'about'       => [ 'title' => 'About',       'template' => 'templates/template-about.php' ],
    ];
}

/**
 * Demo Journal posts. Topic is a `luma_post_topic` slug; artist fills the
 * `luma_post_artist` meta that the theme's by-line reads.
 *
 * @return array<int,array{title:string,topic:string,artist:string,excerpt:string,content:string}>
 */
function luma_demo_get_journal_posts(): array {
    return [
        [
            'title'   => 'Inside the Studio: Elena Morozova',
            'topic'   => 'studio-notes',
            'artist'  => 'Elena Morozova',
            'excerpt' => 'A quiet look at process, light, and the small rituals behind a finished artwork.',
            'content' =>
                "<p>The studio sits under a north-facing skylight, and the first thing Elena Morozova does each morning is nothing at all. She makes coffee, opens the window, and waits for the light to settle before she lets herself look at the work in progress.</p>\n" .
                "<p>Her process is built on layers. A canvas might carry twenty thin veils of colour before it reads as a single quiet field — each pass barely shifting the last, none of them hurried.</p>\n" .
                "<p>Sketches cover the far wall, most of them abandoned on purpose. “A drawing tells me where not to go,” she says. “By the time I reach the canvas, half the decisions are already made.”</p>\n" .
                "<p>What stays with you, leaving her studio, is the silence — not empty, but full of attention. It is the same stillness that ends up, somehow, inside the paint.</p>",
        ],
        [
            'title'   => 'How to Choose Art for a Quiet Interior',
            'topic'   => 'collecting-art',
            'artist'  => 'Luma Editorial',
            'excerpt' => 'A short guide to choosing calm artworks for bedrooms, reading corners, and soft living spaces.',
            'content' =>
                "<p>A calm room does not need a blank wall. It needs one piece that gives the eye somewhere to rest — a focal point quiet enough to live with, present enough to matter.</p>\n" .
                "<p>Begin with scale. In a bedroom or a reading corner, a work that is slightly too large almost always feels more intentional than one that is slightly too small.</p>\n" .
                "<p>Then borrow a tone. Pull a single colour from the room — the grey of the linen, the warmth of the floor — and let the artwork echo it rather than compete with it.</p>\n" .
                "<p>Finally, protect the space around the piece. Negative space is not wasted wall; it is part of the composition, and it is what lets a quiet interior breathe.</p>",
        ],
        [
            'title'   => 'Night Silence: Curator Notes',
            'topic'   => 'exhibitions',
            'artist'  => 'Mira Solen',
            'excerpt' => 'A curator’s note on shadow, silence, and nocturnal visual rhythm.',
            'content' =>
                "<p><em>Night Silence</em> began with a question: what do we stop seeing once the lights go down? The exhibition gathered eleven works in deep, low-key palettes and hung them in near-darkness.</p>\n" .
                "<p>There was no wall text. Visitors were asked instead to spend a full minute with each piece before moving on — an instruction that, at first, made almost everyone uncomfortable.</p>\n" .
                "<p>Slowly the room did its work. Eyes adjusted; shapes that had seemed like shadow resolved into form. People began to whisper, then stopped speaking altogether.</p>\n" .
                "<p>By the end of the run the guestbook had filled with single words: quiet, heavy, held, awake. It was, in the end, an exhibition about paying attention.</p>",
        ],
        [
            'title'   => 'The Story Behind Soft Gravity',
            'topic'   => 'artist-stories',
            'artist'  => 'Anna Weiss',
            'excerpt' => 'A story about balance, negative space, and the feeling of suspended movement.',
            'content' =>
                "<p>“Soft Gravity” was an accident. Anna Weiss had been working on something else entirely when a loaded brush slipped and laid one long grey arc across the page.</p>\n" .
                "<p>“I almost threw it away,” she admits. “Then I left it on the floor for a week and kept walking past it.”</p>\n" .
                "<p>What she saw, on the seventh day, was balance — a single mark suspended in a field of white, neither falling nor rising. The negative space had become the subject.</p>\n" .
                "<p>“I didn’t really make it,” she says now. “I just stopped interfering long enough to let it happen.” The piece is about exactly that: the feeling of movement held perfectly still.</p>",
        ],
        [
            'title'   => 'Collecting Original Art in a Digital Gallery',
            'topic'   => 'collecting-art',
            'artist'  => 'Luma Editorial',
            'excerpt' => 'How digital tools can make collecting art more personal, accessible, and story-driven.',
            'content' =>
                "<p>Buying original art online once felt like a contradiction. How do you trust the surface of a thing you can only see as pixels on a screen?</p>\n" .
                "<p>The answer turns out to be the oldest one: provenance, transparency and a relationship with the work. A good digital gallery shows you the back of the canvas, the certificate, the artist’s own words about the piece.</p>\n" .
                "<p>Digital tools can make collecting more personal, not less. You can save the works that stop you, return to them, and watch how your eye changes over weeks rather than minutes.</p>\n" .
                "<p>The screen is only the introduction. The work itself still arrives in a crate, and still has to earn its place on your wall.</p>",
        ],
    ];
}

/**
 * Create the demo pages. Existing pages are never duplicated; if a page exists
 * but points at the wrong template the template meta is corrected. Only pages
 * this function creates are tagged with the demo marker.
 *
 * @return array{created:string[],updated:string[],skipped:string[],errors:string[]}
 */
function luma_seed_demo_pages(): array {
    $result = [ 'created' => [], 'updated' => [], 'skipped' => [], 'errors' => [] ];

    foreach ( luma_demo_get_pages() as $slug => $data ) {
        $existing = get_page_by_path( $slug, OBJECT, 'page' );

        if ( $existing instanceof WP_Post ) {
            $current = (string) get_post_meta( $existing->ID, '_wp_page_template', true );
            if ( $current !== $data['template'] ) {
                update_post_meta( $existing->ID, '_wp_page_template', $data['template'] );
                $result['updated'][] = $data['title'];
            } else {
                $result['skipped'][] = $data['title'];
            }
            continue;
        }

        $id = wp_insert_post( [
            'post_title'   => $data['title'],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ], true );

        if ( is_wp_error( $id ) || ! $id ) {
            $result['errors'][] = $data['title'] . ': ' . ( is_wp_error( $id ) ? $id->get_error_message() : 'insert failed' );
            continue;
        }

        update_post_meta( $id, '_wp_page_template', $data['template'] );
        update_post_meta( $id, LUMA_DEMO_FLAG, 1 );
        $result['created'][] = $data['title'];
    }

    return $result;
}

/**
 * Seed default Journal topic terms (reuses the core topic list). Idempotent.
 *
 * @return array{created:string[],skipped:string[],errors:string[]}
 */
function luma_seed_journal_topics(): array {
    $result = [ 'created' => [], 'skipped' => [], 'errors' => [] ];

    if ( ! taxonomy_exists( 'luma_post_topic' ) ) {
        $result['errors'][] = 'Taxonomy luma_post_topic is not registered.';
        return $result;
    }

    $topics = function_exists( 'luma_core_get_default_topics' )
        ? luma_core_get_default_topics()
        : [
            'artist-stories' => 'Artist Stories',
            'exhibitions'    => 'Exhibitions',
            'collecting-art' => 'Collecting Art',
            'studio-notes'   => 'Studio Notes',
        ];

    foreach ( $topics as $slug => $label ) {
        if ( term_exists( $slug, 'luma_post_topic' ) ) {
            $result['skipped'][] = $label;
            continue;
        }
        $term = wp_insert_term( $label, 'luma_post_topic', [ 'slug' => $slug ] );
        if ( is_wp_error( $term ) ) {
            $result['errors'][] = $label . ': ' . $term->get_error_message();
        } else {
            $result['created'][] = $label;
        }
    }

    return $result;
}

/**
 * Create demo Journal posts (luma_artist_post). Skips any post whose slug
 * already exists; tags created posts with the demo marker, assigns the topic
 * term and stores the artist by-line meta.
 *
 * @return array{created:string[],skipped:string[],errors:string[]}
 */
function luma_seed_demo_journal_posts(): array {
    $result = [ 'created' => [], 'skipped' => [], 'errors' => [] ];

    if ( ! post_type_exists( 'luma_artist_post' ) ) {
        $result['errors'][] = 'Post type luma_artist_post is not registered.';
        return $result;
    }

    foreach ( luma_demo_get_journal_posts() as $data ) {
        $slug = sanitize_title( $data['title'] );

        $existing = get_posts( [
            'name'             => $slug,
            'post_type'        => 'luma_artist_post',
            'post_status'      => 'any',
            'numberposts'      => 1,
            'fields'           => 'ids',
            'suppress_filters' => false,
        ] );
        if ( ! empty( $existing ) ) {
            $result['skipped'][] = $data['title'];
            continue;
        }

        $id = wp_insert_post( [
            'post_title'   => $data['title'],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'luma_artist_post',
            'post_excerpt' => $data['excerpt'],
            'post_content' => $data['content'],
        ], true );

        if ( is_wp_error( $id ) || ! $id ) {
            $result['errors'][] = $data['title'] . ': ' . ( is_wp_error( $id ) ? $id->get_error_message() : 'insert failed' );
            continue;
        }

        update_post_meta( $id, LUMA_DEMO_FLAG, 1 );
        if ( ! empty( $data['artist'] ) ) {
            update_post_meta( $id, 'luma_post_artist', sanitize_text_field( $data['artist'] ) );
        }

        if ( ! empty( $data['topic'] ) && taxonomy_exists( 'luma_post_topic' ) ) {
            $term = get_term_by( 'slug', $data['topic'], 'luma_post_topic' );
            if ( $term && ! is_wp_error( $term ) ) {
                wp_set_object_terms( $id, (int) $term->term_id, 'luma_post_topic', false );
            }
        }

        $result['created'][] = $data['title'];
    }

    return $result;
}

/**
 * Remove ONLY content created by the seeder (pages + Journal posts tagged with
 * `_luma_demo_content`). Topic terms are intentionally left in place. Never
 * deletes content by slug alone.
 *
 * @return array{pages_deleted:string[],posts_deleted:string[],errors:string[]}
 */
function luma_clear_demo_content(): array {
    $result = [ 'pages_deleted' => [], 'posts_deleted' => [], 'errors' => [] ];

    $ids = get_posts( [
        'post_type'        => [ 'page', 'luma_artist_post' ],
        'post_status'      => 'any',
        'numberposts'      => -1,
        'fields'           => 'ids',
        'meta_key'         => LUMA_DEMO_FLAG,
        'meta_value'       => '1',
        'suppress_filters' => false,
    ] );

    foreach ( $ids as $id ) {
        $type  = get_post_type( $id );
        $title = get_the_title( $id );
        $deleted = wp_delete_post( $id, true ); // force delete, skip trash
        if ( ! $deleted ) {
            $result['errors'][] = $title . ' (delete failed)';
            continue;
        }
        if ( 'page' === $type ) {
            $result['pages_deleted'][] = $title;
        } else {
            $result['posts_deleted'][] = $title;
        }
    }

    return $result;
}

/**
 * Snapshot of demo-content status for the admin overview.
 *
 * @return array<string,mixed>
 */
function luma_demo_content_status(): array {
    $pages = [];
    foreach ( luma_demo_get_pages() as $slug => $data ) {
        $page   = get_page_by_path( $slug, OBJECT, 'page' );
        $exists = $page instanceof WP_Post;
        $pages[] = [
            'slug'        => $slug,
            'title'       => $data['title'],
            'exists'      => $exists,
            'template_ok' => $exists && (string) get_post_meta( $page->ID, '_wp_page_template', true ) === $data['template'],
        ];
    }

    $journal_count = 0;
    if ( post_type_exists( 'luma_artist_post' ) ) {
        $counts = wp_count_posts( 'luma_artist_post' );
        $journal_count = $counts ? (int) $counts->publish : 0;
    }

    $demo_posts = get_posts( [
        'post_type'   => 'luma_artist_post',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_key'    => LUMA_DEMO_FLAG,
        'meta_value'  => '1',
    ] );

    $topics_count = 0;
    if ( taxonomy_exists( 'luma_post_topic' ) ) {
        $terms = get_terms( [ 'taxonomy' => 'luma_post_topic', 'hide_empty' => false, 'fields' => 'ids' ] );
        $topics_count = is_array( $terms ) ? count( $terms ) : 0;
    }

    return [
        'pages'             => $pages,
        'journal_count'     => $journal_count,
        'demo_posts_count'  => count( $demo_posts ),
        'topics_count'      => $topics_count,
        'core_active'       => true,
        'pretty_permalinks' => (bool) get_option( 'permalink_structure' ),
    ];
}

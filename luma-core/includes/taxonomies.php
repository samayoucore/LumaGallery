<?php
/**
 * Luma Core — Taxonomies.
 *
 * Registers `luma_post_topic` for Journal posts (Artist Stories, Exhibitions,
 * Collecting Art, Studio Notes …). Rewrite slug: journal-topic.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default Journal topics, seeded on activation. Kept in one place so the
 * archive filter bar and demo data can reference the same labels.
 *
 * @return array<string,string> slug => label
 */
function luma_core_get_default_topics(): array {
    return [
        'artist-stories' => __( 'Artist Stories', 'luma-core' ),
        'exhibitions'    => __( 'Exhibitions', 'luma-core' ),
        'collecting-art' => __( 'Collecting Art', 'luma-core' ),
        'studio-notes'   => __( 'Studio Notes', 'luma-core' ),
    ];
}

/**
 * Register the `luma_post_topic` taxonomy for the Journal post type.
 */
function luma_core_register_taxonomies(): void {

    if ( taxonomy_exists( 'luma_post_topic' ) ) {
        return;
    }

    $labels = [
        'name'              => __( 'Topics', 'luma-core' ),
        'singular_name'     => __( 'Topic', 'luma-core' ),
        'menu_name'         => __( 'Topics', 'luma-core' ),
        'all_items'         => __( 'All Topics', 'luma-core' ),
        'edit_item'         => __( 'Edit Topic', 'luma-core' ),
        'view_item'         => __( 'View Topic', 'luma-core' ),
        'update_item'       => __( 'Update Topic', 'luma-core' ),
        'add_new_item'      => __( 'Add New Topic', 'luma-core' ),
        'new_item_name'     => __( 'New Topic Name', 'luma-core' ),
        'search_items'      => __( 'Search Topics', 'luma-core' ),
        'not_found'         => __( 'No topics found.', 'luma-core' ),
    ];

    register_taxonomy( 'luma_post_topic', [ 'luma_artist_post' ], [
        'labels'            => $labels,
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'journal-topic', 'with_front' => false ],
    ] );
}

/**
 * Seed the default topic terms (idempotent — only inserts what's missing).
 * Runs on plugin activation.
 */
function luma_core_seed_post_topics(): void {
    foreach ( luma_core_get_default_topics() as $slug => $label ) {
        if ( ! term_exists( $slug, 'luma_post_topic' ) ) {
            wp_insert_term( $label, 'luma_post_topic', [ 'slug' => $slug ] );
        }
    }
}

<?php
/**
 * Luma Core — Custom Post Types.
 *
 * Registers the Journal / Artist Post type used by the theme's
 * archive-luma_artist_post.php and single-luma_artist_post.php templates.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the `luma_artist_post` (Journal) post type.
 *
 * Archive lives at /journal/ ; single posts at /journal/{slug}.
 * Guarded so it never double-registers.
 */
function luma_core_register_post_types(): void {

    if ( post_type_exists( 'luma_artist_post' ) ) {
        return;
    }

    $labels = [
        'name'                  => __( 'Journal', 'luma-core' ),
        'singular_name'         => __( 'Journal Post', 'luma-core' ),
        'menu_name'             => __( 'Journal', 'luma-core' ),
        'name_admin_bar'        => __( 'Journal Post', 'luma-core' ),
        'add_new'               => __( 'Add New', 'luma-core' ),
        'add_new_item'          => __( 'Add New Story', 'luma-core' ),
        'new_item'              => __( 'New Story', 'luma-core' ),
        'edit_item'             => __( 'Edit Story', 'luma-core' ),
        'view_item'             => __( 'View Story', 'luma-core' ),
        'all_items'             => __( 'All Stories', 'luma-core' ),
        'search_items'          => __( 'Search Stories', 'luma-core' ),
        'not_found'             => __( 'No stories found.', 'luma-core' ),
        'not_found_in_trash'    => __( 'No stories found in Trash.', 'luma-core' ),
        'featured_image'        => __( 'Cover image', 'luma-core' ),
        'set_featured_image'    => __( 'Set cover image', 'luma-core' ),
        'archives'              => __( 'Journal', 'luma-core' ),
    ];

    register_post_type( 'luma_artist_post', [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => 'journal',
        'rewrite'            => [ 'slug' => 'journal', 'with_front' => false ],
        'menu_icon'          => 'dashicons-book-alt',
        'menu_position'      => 26,
        'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ],
        'show_in_rest'       => true,
        'publicly_queryable' => true,
        'hierarchical'       => false,
    ] );
}

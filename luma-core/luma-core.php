<?php
/**
 * Plugin Name:       Luma Core
 * Plugin URI:        https://luma-gallery.local/
 * Description:       Core business logic for the Luma Gallery theme — custom post types, taxonomies and demo data helpers. Keeps data structures independent from the active theme.
 * Version:           1.0.0
 * Author:            Luma Gallery
 * Text Domain:       luma-core
 * Requires PHP:      7.4
 *
 * Activate this plugin, then visit Settings → Permalinks and click "Save Changes"
 * once so the /journal/ archive and single post URLs resolve.
 */

defined( 'ABSPATH' ) || exit;

define( 'LUMA_CORE_VERSION', '1.0.0' );
define( 'LUMA_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'LUMA_CORE_URL', plugin_dir_url( __FILE__ ) );

// ─── Includes ─────────────────────────────────────────────────────────────────

require_once LUMA_CORE_DIR . 'includes/post-types.php';
require_once LUMA_CORE_DIR . 'includes/taxonomies.php';
require_once LUMA_CORE_DIR . 'includes/demo-data.php';

// ─── Registration on init ─────────────────────────────────────────────────────

add_action( 'init', 'luma_core_register_post_types' );
add_action( 'init', 'luma_core_register_taxonomies' );

// ─── Activation / deactivation ────────────────────────────────────────────────

/**
 * On activation: register objects, seed default topic terms, then flush rewrite
 * rules so the new CPT archive + single permalinks work immediately.
 */
function luma_core_activate(): void {
    luma_core_register_post_types();
    luma_core_register_taxonomies();
    luma_core_seed_post_topics();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'luma_core_activate' );

function luma_core_deactivate(): void {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'luma_core_deactivate' );

<?php
/**
 * Luma Core — Admin: Demo Content tool.
 *
 * Adds a "Luma Gallery → Demo Content" admin page that seeds / clears the
 * portfolio demo pages and Journal content. All actions are capability- and
 * nonce-guarded and run through admin-post.php; results are surfaced via a
 * short-lived per-user transient + an admin notice.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Per-user transient key for the last action report.
 */
function luma_core_demo_report_key(): string {
    return 'luma_core_demo_report_' . get_current_user_id();
}

// ─── Menu ───────────────────────────────────────────────────────────────────

add_action( 'admin_menu', 'luma_core_register_admin_menu' );

function luma_core_register_admin_menu(): void {
    add_menu_page(
        __( 'Luma Gallery', 'luma-core' ),
        __( 'Luma Gallery', 'luma-core' ),
        'manage_options',
        'luma-demo-content',
        'luma_core_render_demo_admin_page',
        'dashicons-art',
        58
    );

    add_submenu_page(
        'luma-demo-content',
        __( 'Demo Content', 'luma-core' ),
        __( 'Demo Content', 'luma-core' ),
        'manage_options',
        'luma-demo-content',
        'luma_core_render_demo_admin_page'
    );
}

// ─── Action handlers (admin-post.php) ─────────────────────────────────────────

add_action( 'admin_post_luma_seed_demo', 'luma_core_handle_seed' );
add_action( 'admin_post_luma_clear_demo', 'luma_core_handle_clear' );

function luma_core_handle_seed(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You are not allowed to manage demo content.', 'luma-core' ) );
    }
    check_admin_referer( 'luma_seed_demo', 'luma_demo_nonce' );

    $report = [
        'type'   => 'seed',
        'topics' => luma_seed_journal_topics(),
        'pages'  => luma_seed_demo_pages(),
        'posts'  => luma_seed_demo_journal_posts(),
    ];

    // New pages / CPT permalinks resolve cleanly after a one-time flush.
    flush_rewrite_rules();

    $has_errors = ! empty( $report['topics']['errors'] )
        || ! empty( $report['pages']['errors'] )
        || ! empty( $report['posts']['errors'] );

    set_transient( luma_core_demo_report_key(), $report, MINUTE_IN_SECONDS );
    luma_core_redirect_back( $has_errors ? 'partial' : 'seeded' );
}

function luma_core_handle_clear(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You are not allowed to manage demo content.', 'luma-core' ) );
    }
    check_admin_referer( 'luma_clear_demo', 'luma_demo_nonce' );

    $report = [
        'type'  => 'clear',
        'clear' => luma_clear_demo_content(),
    ];

    set_transient( luma_core_demo_report_key(), $report, MINUTE_IN_SECONDS );
    luma_core_redirect_back( empty( $report['clear']['errors'] ) ? 'cleared' : 'error' );
}

function luma_core_redirect_back( string $notice ): void {
    wp_safe_redirect( add_query_arg(
        [ 'page' => 'luma-demo-content', 'luma_notice' => $notice ],
        admin_url( 'admin.php' )
    ) );
    exit;
}

// ─── Render ───────────────────────────────────────────────────────────────────

function luma_core_render_demo_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You are not allowed to access this page.', 'luma-core' ) );
    }

    $status = luma_demo_content_status();
    $notice = isset( $_GET['luma_notice'] ) ? sanitize_key( wp_unslash( $_GET['luma_notice'] ) ) : '';

    $report = get_transient( luma_core_demo_report_key() );
    if ( $report ) {
        delete_transient( luma_core_demo_report_key() );
    }
    ?>
    <div class="wrap luma-demo-admin">
        <h1><?php esc_html_e( 'Luma Demo Content', 'luma-core' ); ?></h1>
        <p class="description"><?php esc_html_e( 'Seed demo pages and journal content for the Luma Gallery portfolio demo.', 'luma-core' ); ?></p>

        <style>
            .luma-demo-admin .luma-admin-stats{display:flex;flex-wrap:wrap;gap:12px;margin:18px 0}
            .luma-demo-admin .luma-admin-stat{flex:1 1 150px;border:1px solid #dcdcde;background:#fff;padding:14px 16px}
            .luma-demo-admin .luma-admin-stat strong{display:block;font-size:22px;line-height:1.2;color:#1d2327}
            .luma-demo-admin .luma-admin-stat span{color:#646970;font-size:11px;text-transform:uppercase;letter-spacing:.05em}
            .luma-demo-admin .luma-admin-actions{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin:6px 0 18px}
            .luma-demo-admin .luma-admin-actions form{margin:0}
            .luma-demo-admin .luma-yes{color:#1a7f37;font-weight:600}
            .luma-demo-admin .luma-no{color:#b32d2e;font-weight:600}
            .luma-demo-admin .luma-report ul{margin:4px 0 12px 18px;list-style:disc}
        </style>

        <?php luma_core_render_notice( $notice ); ?>

        <div class="notice notice-warning inline">
            <p><?php esc_html_e( 'This tool is intended for local/demo environments. It creates portfolio demo content and should not be used on production websites with real content.', 'luma-core' ); ?></p>
        </div>

        <?php if ( ! $status['pretty_permalinks'] ) : ?>
            <div class="notice notice-warning inline">
                <p>
                    <?php
                    printf(
                        /* translators: %s: Permalinks settings link */
                        esc_html__( 'Plain permalinks are active. Visit %s and click "Save Changes" so /journal/ and page URLs resolve.', 'luma-core' ),
                        '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">' . esc_html__( 'Settings → Permalinks', 'luma-core' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- ── Status overview ── -->
        <h2><?php esc_html_e( 'Status overview', 'luma-core' ); ?></h2>
        <div class="luma-admin-stats">
            <?php
            $pages_present = count( array_filter( $status['pages'], static fn( $p ) => $p['exists'] ) );
            $pages_total   = count( $status['pages'] );
            ?>
            <div class="luma-admin-stat"><strong><?php echo esc_html( $pages_present . ' / ' . $pages_total ); ?></strong><span><?php esc_html_e( 'Pages present', 'luma-core' ); ?></span></div>
            <div class="luma-admin-stat"><strong><?php echo esc_html( (string) $status['journal_count'] ); ?></strong><span><?php esc_html_e( 'Journal posts', 'luma-core' ); ?></span></div>
            <div class="luma-admin-stat"><strong><?php echo esc_html( (string) $status['demo_posts_count'] ); ?></strong><span><?php esc_html_e( 'Demo posts', 'luma-core' ); ?></span></div>
            <div class="luma-admin-stat"><strong><?php echo esc_html( (string) $status['topics_count'] ); ?></strong><span><?php esc_html_e( 'Journal topics', 'luma-core' ); ?></span></div>
            <div class="luma-admin-stat"><strong class="luma-yes"><?php esc_html_e( 'Active', 'luma-core' ); ?></strong><span><?php esc_html_e( 'Luma Core', 'luma-core' ); ?></span></div>
        </div>

        <table class="widefat striped" style="max-width:760px">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Page', 'luma-core' ); ?></th>
                    <th><?php esc_html_e( 'Slug', 'luma-core' ); ?></th>
                    <th><?php esc_html_e( 'Exists', 'luma-core' ); ?></th>
                    <th><?php esc_html_e( 'Template', 'luma-core' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $status['pages'] as $p ) : ?>
                    <tr>
                        <td><?php echo esc_html( $p['title'] ); ?></td>
                        <td><code>/<?php echo esc_html( $p['slug'] ); ?>/</code></td>
                        <td><?php echo $p['exists'] ? '<span class="luma-yes">&#10003;</span>' : '<span class="luma-no">&#10007;</span>'; ?></td>
                        <td>
                            <?php
                            if ( ! $p['exists'] ) {
                                echo '<span class="luma-no">&mdash;</span>';
                            } else {
                                echo $p['template_ok'] ? '<span class="luma-yes">&#10003;</span>' : '<span class="luma-no">&#10007;</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- ── Actions ── -->
        <h2 style="margin-top:24px"><?php esc_html_e( 'Actions', 'luma-core' ); ?></h2>
        <div class="luma-admin-actions">
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="luma_seed_demo">
                <?php wp_nonce_field( 'luma_seed_demo', 'luma_demo_nonce' ); ?>
                <?php submit_button( __( 'Seed Demo Content', 'luma-core' ), 'primary', 'submit', false ); ?>
            </form>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'This will permanently delete the seeded demo pages and Journal posts. Continue?', 'luma-core' ) ); ?>');">
                <input type="hidden" name="action" value="luma_clear_demo">
                <?php wp_nonce_field( 'luma_clear_demo', 'luma_demo_nonce' ); ?>
                <?php submit_button( __( 'Clear Demo Content', 'luma-core' ), 'secondary', 'submit', false ); ?>
            </form>
        </div>

        <?php luma_core_render_report( $report ); ?>
    </div>
    <?php
}

/**
 * Top-of-page result notice based on the redirect arg.
 */
function luma_core_render_notice( string $notice ): void {
    $map = [
        'seeded'  => [ 'success', __( 'Demo content seeded successfully.', 'luma-core' ) ],
        'partial' => [ 'warning', __( 'Demo content processed with some skipped items or errors.', 'luma-core' ) ],
        'cleared' => [ 'success', __( 'Demo content cleared.', 'luma-core' ) ],
        'error'   => [ 'error',   __( 'Demo content action failed. Please check permissions and try again.', 'luma-core' ) ],
    ];
    if ( ! isset( $map[ $notice ] ) ) {
        return;
    }
    printf(
        '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
        esc_attr( $map[ $notice ][0] ),
        esc_html( $map[ $notice ][1] )
    );
}

/**
 * Detailed report of the last seed / clear action.
 *
 * @param mixed $report
 */
function luma_core_render_report( $report ): void {
    if ( ! is_array( $report ) || empty( $report['type'] ) ) {
        return;
    }

    echo '<div class="luma-report card" style="max-width:760px;padding:4px 16px 12px"><h2>' . esc_html__( 'Last action report', 'luma-core' ) . '</h2>';

    if ( 'seed' === $report['type'] ) {
        luma_core_render_report_group( __( 'Pages', 'luma-core' ), [
            __( 'Created', 'luma-core' ) => $report['pages']['created'] ?? [],
            __( 'Updated', 'luma-core' ) => $report['pages']['updated'] ?? [],
            __( 'Skipped', 'luma-core' ) => $report['pages']['skipped'] ?? [],
            __( 'Errors', 'luma-core' )  => $report['pages']['errors'] ?? [],
        ] );
        luma_core_render_report_group( __( 'Journal posts', 'luma-core' ), [
            __( 'Created', 'luma-core' ) => $report['posts']['created'] ?? [],
            __( 'Skipped', 'luma-core' ) => $report['posts']['skipped'] ?? [],
            __( 'Errors', 'luma-core' )  => $report['posts']['errors'] ?? [],
        ] );
        luma_core_render_report_group( __( 'Topics', 'luma-core' ), [
            __( 'Created', 'luma-core' ) => $report['topics']['created'] ?? [],
            __( 'Skipped', 'luma-core' ) => $report['topics']['skipped'] ?? [],
            __( 'Errors', 'luma-core' )  => $report['topics']['errors'] ?? [],
        ] );
    } elseif ( 'clear' === $report['type'] ) {
        luma_core_render_report_group( __( 'Cleared', 'luma-core' ), [
            __( 'Pages deleted', 'luma-core' ) => $report['clear']['pages_deleted'] ?? [],
            __( 'Posts deleted', 'luma-core' ) => $report['clear']['posts_deleted'] ?? [],
            __( 'Errors', 'luma-core' )        => $report['clear']['errors'] ?? [],
        ] );
    }

    echo '</div>';
}

/**
 * Render one labelled group of result lines.
 *
 * @param string                    $heading
 * @param array<string,string[]>    $groups  label => items
 */
function luma_core_render_report_group( string $heading, array $groups ): void {
    echo '<h3 style="margin-bottom:4px">' . esc_html( $heading ) . '</h3>';
    foreach ( $groups as $label => $items ) {
        $count = is_array( $items ) ? count( $items ) : 0;
        echo '<p style="margin:2px 0"><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( (string) $count );
        if ( $count ) {
            echo ' &mdash; ' . esc_html( implode( ', ', array_map( 'strval', $items ) ) );
        }
        echo '</p>';
    }
}

<?php
namespace DMG\ReadMoreLink\Taxonomy;

use WP_Post;
use const DMG\ReadMoreLink\READ_MORE_LINK_BLOCK_NAME;

/**
 * Constants
 */
const TAXONOMY_SLUG = 'read_more_link';
const TAXONOMY_TERM = 'true';
function bootstrap(): void {
    add_action( 'init', __NAMESPACE__ . '\\register_util_taxonomy' );
    add_action( 'save_post', __NAMESPACE__ . '\\update_post_taxonomy', 10, 2 );
}

/**
 * Register the taxonomy.
 *
 * Hidden taxonomy for posts using the "dmg-read-more-link" block.
 *
 * This post is not visible in the WP admin UI.
 */
function register_util_taxonomy(): void {
    $taxonomy_name = __( 'DMG Read More Link', 'dmg-read-more-link' );

    $args = [
        'labels' => [ 'name' => $taxonomy_name ],
        'public'            => false,
        'show_ui'           => false,
        'show_in_menu'      => false,
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'show_in_rest'      => false,
        'hierarchical'      => false,
        'rewrite'           => false,
        'query_var'         => false,
    ];

    // Add taxonomy to all post types
    $post_types = get_post_types( [ 'public' => true ], 'names' );

    register_taxonomy( TAXONOMY_SLUG, $post_types, $args );
}

function update_post_taxonomy( int $post_id, WP_Post $post ): void {
    if (
        defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
        wp_is_post_revision( $post_id ) ||
        $post->post_type !== 'post'
    ) {
        return;
    }

    $content = $post->post_content;
    $existing_terms = wp_get_object_terms( $post_id, TAXONOMY_SLUG, [ 'fields' => 'names' ] );

    // If the block is present, ensure the term exists
    if ( has_block( READ_MORE_LINK_BLOCK_NAME, $content ) ) {
        if ( ! in_array( TAXONOMY_TERM, $existing_terms, true ) ) {
            wp_set_object_terms( $post_id, TAXONOMY_TERM, TAXONOMY_SLUG, false );
        }
        return;
    }

    // Block not present: remove the term if it exists
    if ( in_array( TAXONOMY_TERM, $existing_terms, true ) ) {
        wp_set_object_terms( $post_id, [], TAXONOMY_SLUG, false );
    }
}
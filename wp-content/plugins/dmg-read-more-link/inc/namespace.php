<?php
/**
 * Main plugin namespace.
 *
 * @package DMG_Read_More_Link
 *
 */
namespace DMG\ReadMoreLink;

/**
 * Bootstrap main plugin functionality.
 *
 * @return void
 */
function bootstrap(): void {
    add_action( 'init', __NAMESPACE__ . '\\read_more_link_register_block' );

    CLI\bootstrap();
    Taxonomy\bootstrap();
}

function  read_more_link_register_block() {
    register_block_type( PLUGIN_ROOT );
}
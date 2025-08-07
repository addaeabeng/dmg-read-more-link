<?php
namespace DMG\ReadMoreLink\CLI;

require_once __DIR__ . '/read-more-search.php';

use WP_CLI;

/**
 * Bootstrap the CLI.
 *
 * @return void
 */
function bootstrap(): void {
    if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
       return;
    }
    WP_CLI::add_command( 'dmg-read-more-link', __NAMESPACE__ . '\\Read_More_Search' );
}
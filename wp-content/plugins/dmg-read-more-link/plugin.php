<?php
/**
 * Plugin Name: Read More Link Block
 * Description: Adds a Gutenberg block to insert stylized "Read More" links and a CLI to search for them.
 * Version: 1.0
 * Author: Addae Abeng
 * Text Domain: dmg-read-more-link
 *
 * @package DMG_Read_More_Link
 */
namespace DMG\ReadMoreLink;

defined('ABSPATH') || exit;

/**
 * Define constants.
 */
const PLUGIN_ROOT = __DIR__;
const READ_MORE_LINK_BLOCK_NAME = 'dmg/read-more-link';

/**
 * Require dependencies.
 */
require_once PLUGIN_ROOT . '/inc/namespace.php';
require_once PLUGIN_ROOT . '/inc/cli.php';
require_once PLUGIN_ROOT . '/inc/taxonomy.php';

/**
 * Initialize plugin.
 */
if (function_exists(__NAMESPACE__ . '\\bootstrap')) {
    bootstrap();
}

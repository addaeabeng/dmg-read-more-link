<?php
$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
}


$_tests_dir = getenv('WP_TESTS_DIR') ?: '/tmp/wordpress-tests-lib';

// Load WP testing environment
require_once $_tests_dir . '/includes/functions.php';

// Load your plugin
tests_add_filter('muplugins_loaded', function () {
    require dirname(dirname(__FILE__)) . '/plugin.php';
});

require $_tests_dir . '/includes/bootstrap.php';

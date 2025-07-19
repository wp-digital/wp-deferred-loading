<?php

$libraryPath = dirname(__DIR__);
$vendorPath = "$libraryPath/vendor";

if (!realpath($vendorPath)) {
    die('Please install via Composer before running tests.');
}

putenv('LIBRARY_PATH=' . $libraryPath);

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', "{$vendorPath}/autoload.php");
}

require_once "$vendorPath/antecedent/patchwork/Patchwork.php";
require_once "$vendorPath/autoload.php";

use Brain\Monkey;

Monkey\setUp();

require_once "$libraryPath/src/deferred-loading.php";

unset($libraryPath, $vendorPath);

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
error_reporting(E_ALL);
date_default_timezone_set('UTC');

foreach ([__DIR__ . '/../../autoload.php', __DIR__ . '/vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        return require $file;
    }
}

throw new RuntimeException('Required file \'autoload.php\' was not found.');

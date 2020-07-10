<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches;

/**
 * Provides basic information about quality patches package.
 */
class Info
{
    /**
     * Returns path to patches directory.
     *
     * @return string
     */
    public function getPatchesDirectory()
    {
        return __DIR__ . '/../patches';
    }

    /**
     * Returns path to patches configuration file.
     *
     * @return string
     */
    public function getPatchesConfig()
    {
        return __DIR__ . '/../patches.json';
    }
}

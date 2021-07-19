<?php
/**
 * Copyright 2013-present Adobe. All rights reserved.
 * Each source file included in this directory is licensed under OSL 3.0 or your existing
 * commercial license or subscription agreement with Magento or its Affiliates (the "Agreement).
 *
 * http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * Please see LICENSE_OSL.txt for the full text of the OSL 3.0 license or contact engcom@adobe.com for a copy.
 *
 * Subject to your payment of fees and compliance with the terms and conditions of the Agreement,
 * the Agreement supersedes the OSL 3.0 license for each source file included in this directory.
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
    public function getSupportPatchesConfig()
    {
        return __DIR__ . '/../support-patches.json';
    }

    /**
     * Returns path to patches configuration file.
     *
     * @return string
     */
    public function getCommunityPatchesConfig()
    {
        return __DIR__ . '/../community-patches.json';
    }

    /**
     * Returns path to categories configuration file.
     *
     * @return string
     */
    public function getCategoriesConfig()
    {
        return __DIR__ . '/../config/patch-categories.json';
    }
}

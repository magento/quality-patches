<?php
/**
 * © Copyright 2013-present Adobe. All rights reserved.
 *
 * This file is licensed under OSL 3.0 or your existing commercial license or subscription
 * agreement with Magento or its Affiliates (the "Agreement).
 *
 * You may obtain a copy of the OSL 3.0 license at http://opensource.org/licenses/osl-3.0.php Open
 * Software License (OSL 3.0) or by contacting engcom@adobe.com for a copy.
 *
 * Subject to your payment of fees and compliance with the terms and conditions of the Agreement,
 * the Agreement supersedes the OSL 3.0 license with respect to this file.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Integrity\Lib;

use InvalidArgumentException;
use Magento\QualityPatches\Info;

/**
 * Contains config.
 */
class Config
{
    /**
     * @var Info
     */
    private $info;

    public function __construct()
    {
        $this->info = new Info();
    }

    /**
     * Return patch configuration.
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function get(): array
    {
        $configPath = $this->info->getSupportPatchesConfig();
        $content = file_get_contents($configPath);
        $result = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(
                "Unable to unserialize patches configuration '{$configPath}'. Error: " . json_last_error_msg()
            );
        }

        return $result;
    }
}

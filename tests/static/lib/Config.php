<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Lib;

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
        $configPath = $this->info->getPatchesConfig();
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

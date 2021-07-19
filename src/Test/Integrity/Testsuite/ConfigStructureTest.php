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

namespace Magento\QualityPatches\Test\Integrity\Testsuite;

use Magento\QualityPatches\Info;
use Magento\QualityPatches\Test\Integrity\Lib\Config;
use PHPUnit\Framework\TestCase;

/**
 * @inheritDoc
 */
class ConfigStructureTest extends TestCase
{
    /**
     * Configuration JSON property.
     *
     * Contains patch filename, type string.
     */
    const PROP_FILE = 'file';

    /**
     * Configuration JSON property.
     *
     * Contains required patch ids, type array.
     */
    const PROP_REQUIRE = 'require';

    /**
     * Configuration JSON property.
     *
     * Contains patch id that current patch replaced with, type string.
     */
    const PROP_REPLACED_WITH = 'replaced-with';

    /**
     * Configuration JSON property.
     *
     * Defines whether patch is deprecated, type boolean.
     */
    const PROP_DEPRECATED = 'deprecated';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Info
     */
    private $info;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->info = new Info();
        $this->config = new Config();
    }

    /**
     * Validates patch configuration structure.
     *
     * @doesNotPerformAssertions
     */
    public function testConfigStructure()
    {
        try {
            $config = $this->config->get();
        } catch (\InvalidArgumentException $e) {
            $this->fail($e->getMessage());
        }

        $errors = $this->validateConfiguration($config);
        if (!empty($errors)) {
            $this->fail(
                implode(PHP_EOL, $errors)
            );
        }
    }

    /**
     * Validates patch configuration.
     *
     * @param array $config
     *
     * @return array
     */
    private function validateConfiguration(array $config): array
    {
        $errors = [];
        foreach ($config as $patchId => $patchGeneralConfig) {
            $patchErrors = [];
            foreach ($patchGeneralConfig['packages'] as $packageConfiguration) {
                foreach ($packageConfiguration as $packageConstraint => $patchInfo) {
                    $patchErrors = $this->validateProperties($patchInfo, $packageConstraint, $patchErrors);
                }
            }

            if (!empty($patchErrors)) {
                $errors[] = "Patch {$patchId} has invalid configuration:";
                $errors = array_merge($errors, $patchErrors);
            }
        }

        return $errors;
    }

    /**
     * Validates properties.
     *
     * @param array $patchData
     * @param string $packageConstraint
     * @param string[] $errors
     * @return array
     */
    private function validateProperties(
        array $patchData,
        string $packageConstraint,
        array $errors
    ): array {
        if (!isset($patchData[static::PROP_FILE])) {
            $errors[] = sprintf(
                " - Property '%s' is not found in '%s'",
                static::PROP_FILE,
                $packageConstraint
            );
        } elseif (!file_exists($this->info->getPatchesDirectory() . '/' . $patchData[static::PROP_FILE])) {
            $errors[] = sprintf(
                " - File '%s' from '%s' does not exist",
                $this->info->getPatchesDirectory() . '/' . $patchData[static::PROP_FILE],
                $packageConstraint
            );
        }

        if (isset($patchData[static::PROP_REQUIRE]) &&
            !is_array($patchData[static::PROP_REQUIRE])
        ) {
            $errors[] = sprintf(
                " - Property '%s' from '%s' should have an array type",
                static::PROP_REQUIRE,
                $packageConstraint
            );
        }

        if (isset($patchData[static::PROP_REPLACED_WITH]) &&
            !is_string($patchData[static::PROP_REPLACED_WITH])
        ) {
            $errors[] = sprintf(
                " - Property '%s' from '%s' should have a string type",
                static::PROP_REPLACED_WITH,
                $packageConstraint
            );
        }

        if (isset($patchData[static::PROP_DEPRECATED]) &&
            !is_bool($patchData[static::PROP_DEPRECATED])
        ) {
            $errors[] = sprintf(
                " - Property '%s' from '%s' should have a boolean type",
                static::PROP_DEPRECATED,
                $packageConstraint
            );
        }

        return $errors;
    }
}

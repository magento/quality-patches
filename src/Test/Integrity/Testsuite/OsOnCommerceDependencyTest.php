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

namespace Integrity\Testsuite;

use Magento\QualityPatches\Info;
use Magento\QualityPatches\Test\Integrity\Lib\Config;
use PHPUnit\Framework\TestCase;

/**
 * @inheritDoc
 */
class OsOnCommerceDependencyTest extends TestCase
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->config = new Config();
    }

    /**
     * Tests that OS patches don't depend on Commerce patches.
     *
     * @doesNotPerformAssertions
     */
    public function testOsOnCommerceDependency()
    {
        $data = $this->getPatchesFilesData();

        $errors = [];
        foreach ($data as $patchId => $item) {
            if ($this->isOsPatch($item['files']) && $this->hasCommerceDependency($item)) {
                $errors[] = sprintf(
                    " - CE patch %s has a dependency on a EE patch",
                    $patchId
                );
            }
        }

        if (!empty($errors)) {
            array_unshift(
                $errors,
                "Found invalid dependency of an OS patch on a Commerce patch:"
            );
            $this->fail(
                implode(PHP_EOL, $errors)
            );
        }
    }

    /**
     * @return array
     */
    private function getPatchesFilesData(): array
    {
        $result = [];
        foreach ($this->config->get() as $patchId => $patchGeneralConfig) {
            $result[$patchId] = [];
            foreach ($patchGeneralConfig['packages'] as $packageConfiguration) {
                foreach ($packageConfiguration as $patchInfo) {
                    $result[$patchId]['files'][] = $patchInfo['file'];
                    if (isset($patchInfo['require'])) {
                        if (!isset($result[$patchId]['require'])) {
                            $result[$patchId]['require'] = [];
                        }
                        $result[$patchId]['require'] = array_merge($result[$patchId]['require'], $patchInfo['require']);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Checks if all patch files are from the 'os' directory.
     *
     * @param array $files
     * @return bool
     */
    private function isOsPatch(array $files): bool
    {
        foreach ($files as $file) {
            if (!str_starts_with($file, 'os/')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the patch files contain 'commerce' patches.
     *
     * @param array $files
     * @return bool
     */
    private function isCommercePatch(array $files): bool
    {
        foreach ($files as $file) {
            if (!str_starts_with($file, 'commerce/')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the patch has a dependency on a patch that has 'commerce' patches in files.
     *
     * @param array $item
     * @return bool
     */
    private function hasCommerceDependency(array $item): bool
    {
        if (isset($item['require'])) {
            foreach ($item['require'] as $requiredPatchId) {
                if (isset($this->getPatchesFilesData()[$requiredPatchId])) {
                    $requiredPatch = $this->getPatchesFilesData()[$requiredPatchId];
                }
                if (isset($requiredPatch) && $this->isCommercePatch($requiredPatch['files'])) {
                    return true;
                }
            }
        }
        return false;
    }
}

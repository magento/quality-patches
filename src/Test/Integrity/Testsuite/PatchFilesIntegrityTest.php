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

use Magento\QualityPatches\Test\Integrity\Lib\Config;
use PHPUnit\Framework\TestCase;

/**
 * Tests patch files integrity.
 */
class PatchFilesIntegrityTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->config = new Config();
    }

    /**
     * Verifies if the same patch file is added in multiple places under different QPT IDs.
     */
    public function testPatchUniquenessAcrossPackages()
    {
        $config = $this->config->get();
        $filesMap = $this->mapFilesToPatchIds($config);
        $errors = $this->findDuplicateUsages($filesMap);

        if (!empty($errors)) {
            $this->fail(implode(PHP_EOL, array_unique($errors)));
        }

        $this->assertTrue(true);
    }

    /**
     * Checks if we have files in patches that are not mentioned in support-patches.json
     */
    public function testForOrphanedPatches()
    {
        $patchesDir = $this->getPatchesDirectory();
        $physicalFiles = $this->scanPatchDirectory($patchesDir);
        $mentionedFiles = $this->extractFilePathsFromConfig($this->config->get());

        $orphans = array_diff($physicalFiles, $mentionedFiles);

        if (!empty($orphans)) {
            $this->fail(
                sprintf(
                    'Found files in patches directory that are not mentioned in support-patches.json: %s',
                    implode(', ', $orphans)
                )
            );
        }
        $this->assertTrue(true);
    }

    /**
     * Collects patch files and maps them to patch IDs.
     *
     * @param array $config
     * @return array
     */
    private function mapFilesToPatchIds(array $config): array
    {
        $filesMap = [];

        foreach ($config as $patchId => $patchData) {
            foreach ($patchData['packages'] as $packageName => $packageData) {
                foreach ($packageData as $versionData) {
                    if (isset($versionData['file'])) {
                        if (!empty($versionData['deprecated']) || !empty($versionData['replaced-with'])) {
                            continue;
                        }

                        $file = $versionData['file'];
                        $key = $file . '|' . $packageName;

                        $filesMap[$key][] = $patchId;
                    }
                }
            }
        }
        return $filesMap;
    }

    /**
     * Validates that patch files are not reused across different base patch IDs.
     *
     * @param array $filesMap
     * @return array
     */
    private function findDuplicateUsages(array $filesMap): array
    {
        $errors = [];
        foreach ($filesMap as $key => $patchIds) {
            $baseIds = array_map(function ($id) {
                return preg_replace('/-V\d+$/', '', $id);
            }, $patchIds);
            $uniqueBaseIds = array_unique($baseIds);

            if (count($uniqueBaseIds) > 1) {
                list($file, $packageName) = explode('|', $key);
                $errors[] = sprintf(
                    "File '%s' in package '%s' is used in multiple patches: %s",
                    $file,
                    $packageName,
                    implode(', ', array_unique($patchIds))
                );
            }
        }
        return $errors;
    }

    /**
     * Extract all unique file paths from the configuration.
     *
     * @param array $config
     * @return array
     */
    private function extractFilePathsFromConfig(array $config): array
    {
        $files = [];
        foreach ($config as $patchData) {
            foreach ($patchData['packages'] ?? [] as $packageData) {
                $files = array_merge($files, array_column($packageData, 'file'));
            }
        }
        return array_unique($files);
    }

    /**
     * Scan patches directory for patch files.
     *
     * @param string $patchesDir
     * @return array Files in the patches directory
     */
    private function scanPatchDirectory(string $patchesDir): array
    {
        $physicalFiles = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($patchesDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'patch') {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen($patchesDir) + 1);

            if (strpos($relativePath, 'community/') === 0) {
                continue;
            }

            $physicalFiles[] = $relativePath;
        }

        return $physicalFiles;
    }

    /**
     * @return string
     */
    private function getPatchesDirectory(): string
    {
        return realpath((new \Magento\QualityPatches\Info())->getPatchesDirectory());
    }
}

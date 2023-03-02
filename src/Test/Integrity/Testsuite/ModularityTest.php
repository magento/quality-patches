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
class ModularityTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Info
     */
    private $info;

    /**
     * @var string[]
     */
    private $basePackageAliases = [
        'magento/inventory-composer-metapackage' => 'magento/inventory-metapackage',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->info = new Info();
        $this->config = new Config();
    }

    /**
     * Tests that patch content is separated on CE, EE, B2B, Inventory and PageBuilder parts.
     *
     * @doesNotPerformAssertions
     */
    public function testModularity()
    {
        $config = $this->getModularityConfig();
        $data = $this->getPatchesData();

        $errors = [];
        foreach ($data as $item) {
            foreach ($config as $basePackage => $modules) {
                $intersect = array_intersect($modules, $item['modules']);
                $itemPackageName = $this->basePackageAliases[$item['packageName']] ?? $item['packageName'];
                if (!empty($intersect)
                    && $basePackage !== $itemPackageName
                    && !in_array($itemPackageName, $intersect)) {
                    $basePackageNames = [$basePackage];
                    array_push($basePackageNames, ...array_keys(
                        array_filter(
                            $this->basePackageAliases,
                            function ($value) use ($basePackage) {
                                return $value === $basePackage;
                            }
                        )
                    ));
                    $errors[] = sprintf(
                        " - %s contains diffs '%s' that have to be under '%s' configuration",
                        $item['file'],
                        implode("','", $intersect),
                        implode("' or '", $basePackageNames)
                    );
                }
            }
        }

        if (!empty($errors)) {
            array_unshift(
                $errors,
                "Found invalid patch source assignments for next patches in patches.json:"
            );
            $this->fail(
                implode(PHP_EOL, $errors)
            );
        }
    }

    /**
     * Returns modularity config.
     *
     * @return array
     * @throws \Exception
     */
    private function getModularityConfig(): array
    {
        $data = file_get_contents(__DIR__ . '/../Config/modularity.json');
        $result = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(__DIR__ . '/../Config/modularity.json has invalid format');
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getPatchesData(): array
    {
        $result = [];
        foreach ($this->config->get() as $patchId => $patchGeneralConfig) {
            foreach ($patchGeneralConfig['packages'] as $packageName => $packageConfiguration) {
                foreach ($packageConfiguration as $versionConstraint => $patchInfo) {
                    $result[] = [
                        'id' => $patchId,
                        'packageName' => $packageName,
                        'packageConstraint' => $versionConstraint,
                        'file' => $patchInfo['file'],
                        'modules' => $this->extractModules($patchInfo['file'])
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Returns list of affected modules.
     *
     * @param string $path
     * @return array
     */
    private function extractModules(string $path): array
    {
        $content = file_get_contents($this->info->getPatchesDirectory() . '/' . $path);

        $result = [];
        if (preg_match_all(
            '#^.* [ab]/vendor/(?<vendor>.*?)/(?<component>.*?)/.*$#mi',
            $content,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                $result[] = $match['vendor'] . '/' . $match['component'];
            }
        }

        if (preg_match_all(
            '#^.* [ab]/(?<folder>.*?)/(?<subfolder>.*?)[/ ].*$#mi',
            $content,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                if ($match['folder'] !== 'vendor') {
                    $result[] = $match['folder'] . '/' . $match['subfolder'];
                }
            }
        }

        $result = array_unique($result);
        sort($result);

        return $result;
    }
}

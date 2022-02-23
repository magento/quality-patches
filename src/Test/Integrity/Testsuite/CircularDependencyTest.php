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

use Magento\QualityPatches\Test\Integrity\Lib\CircularDependency;
use Magento\QualityPatches\Test\Integrity\Lib\Config;
use PHPUnit\Framework\TestCase;

/**
 * @inheritDoc
 */
class CircularDependencyTest extends TestCase
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
     * Check patch configuration structure for circular dependencies.
     *
     * @doesNotPerformAssertions
     */
    public function testCircularDependencies()
    {
        $dependencies = $this->getConfigDependencies();

        $circularPatchDependencies = (new CircularDependency($dependencies))->get();

        if ($circularPatchDependencies) {
            $result = '';
            foreach ($circularPatchDependencies as $patch => $chains) {
                $result .= 'Patch ' . $patch . ' dependencies:' . PHP_EOL;
                foreach ($chains as $chain) {
                    $result .= 'Chain : ' . implode('->', $chain) . PHP_EOL;
                }
                $result .= PHP_EOL;
            }

            $this->fail('Detected next circular dependencies:' . PHP_EOL . $result);
        }
    }

    /**
     * @return array
     */
    private function getConfigDependencies(): array
    {
        $dependencies = [];
        foreach ($this->config->get() as $patchId => $patchGeneralConfig) {
            $dependencies[$patchId] = [];
            foreach ($patchGeneralConfig['packages'] as $packageConfiguration) {
                foreach ($packageConfiguration as $patchInfo) {
                    if (isset($patchInfo['require'])) {
                        $dependencies[$patchId] = array_merge($dependencies[$patchId], $patchInfo['require']);
                    }
                }
            }
        }

        return $dependencies;
    }
}

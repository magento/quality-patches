<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
    protected function setUp()
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
            foreach ($patchGeneralConfig as $packageConfiguration) {
                foreach ($packageConfiguration as $patchInfo) {
                    foreach ($patchInfo as $patchData) {
                        if (isset($patchData['require'])) {
                            $dependencies[$patchId] = array_merge($dependencies[$patchId], $patchData['require']);
                        }
                    }
                }
            }
        }

        return $dependencies;
    }
}

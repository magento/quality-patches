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

use Composer\Semver\VersionParser;
use Magento\QualityPatches\Test\Integrity\Lib\Config;
use PHPUnit\Framework\TestCase;

/**
 * @inheritDoc
 */
class DeprecatedDependencyTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var VersionParser
     */
    private $versionParser;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->config = new Config();
        $this->versionParser = new VersionParser();
    }

    /**
     * Checks patch configuration structure for existing dependencies on deprecated patches.
     *
     * @doesNotPerformAssertions
     */
    public function testAbsenceOfDependenciesOnDeprecatedPatches()
    {
        $errors = $this->checkDeprecatedDependencies();

        if (!empty($errors)) {
            array_unshift($errors, 'Detected dependencies on deprecated patches:');
            array_push(
                $errors,
                'Patches dependent on the deprecated patch, should be deprecated or replaced as well.'
            );

            $this->fail(
                implode(PHP_EOL, $errors)
            );
        }
    }

    /**
     * Checks that the package constraint of patch replacement is compatible with the current patch package constraint.
     *
     * @doesNotPerformAssertions
     */
    public function testReplacedWithPatchConstraint()
    {
        $errors = $this->checkReplacedConstraint();

        if (!empty($errors)) {
            array_unshift($errors, 'Detected package version constraint mismatch for replaced patches:');
            array_push(
                $errors,
                'The package version constraint of patch replacement must be compatible ' .
                'with the package version constraint of replaceable patch.'
            );

            $this->fail(
                implode(PHP_EOL, $errors)
            );
        }
    }

    /**
     * Returns list of errors if any.
     *
     * @return array
     */
    private function checkReplacedConstraint(): array
    {
        $all = $this->getPatchData();
        $replaced = array_filter(
            $all,
            function ($item) {
                return (bool)$item['replacedWith'];
            }
        );

        $result = [];
        foreach ($replaced as $item) {
            $matchedItems = array_filter(
                $all,
                function ($patchData) use ($item) {
                    if ($patchData['patchId'] !== $item['replacedWith'] ||
                        $patchData['packageName'] !== $item['packageName']) {
                        return false;
                    }
                    $replacedConstraint = $this->versionParser->parseConstraints(
                        $item['packageConstraint']
                    );
                    $replacementConstraint = $this->versionParser->parseConstraints(
                        $patchData['packageConstraint']
                    );

                    return $replacementConstraint->matches($replacedConstraint);
                }
            );

            if (empty($matchedItems)) {
                $result[] = sprintf(
                    ' - %1$s patch is a replace for %2$s (%3$s), but %1$s doesn\'t have any' .
                    ' package version constraint that matches %3$s',
                    $item['replacedWith'],
                    $item['patchId'],
                    $item['packageName'] . ' ' . $item['packageConstraint']
                );
            }
        }

        return $result;
    }

    /**
     * Returns list of errors if there are dependencies on deprecated patches.
     *
     * @return array
     */
    private function checkDeprecatedDependencies(): array
    {
        $deprecated = $this->getDeprecated();
        $notDeprecatedWithRequire = $this->getNotDeprecatedWithRequire();
        $deprecatedIds = array_unique(array_column($deprecated, 'patchId'));

        $result = [];
        foreach ($notDeprecatedWithRequire as $patchData) {
            $requiredDeprecatedIds = array_intersect($patchData['require'], $deprecatedIds);
            if (empty($requiredDeprecatedIds)) {
                continue;
            }

            $matchedDependencies = array_filter(
                $deprecated,
                function ($deprecatedPatch) use ($patchData, $requiredDeprecatedIds) {
                    if (!in_array($deprecatedPatch['patchId'], $requiredDeprecatedIds) ||
                        $deprecatedPatch['packageName'] !== $patchData['packageName']) {
                        return false;
                    }
                    $deprecatedConstraint = $this->versionParser->parseConstraints(
                        $deprecatedPatch['packageConstraint']
                    );
                    $notDeprecatedConstraint = $this->versionParser->parseConstraints(
                        $patchData['packageConstraint']
                    );

                    return $notDeprecatedConstraint->matches($deprecatedConstraint);
                }
            );
            $errorMessages = array_map(
                function ($deprecatedPatch) use ($patchData) {
                    return sprintf(
                        ' - deprecated %s (%s) is a dependency for non-deprecated %s (%s)',
                        $deprecatedPatch['patchId'],
                        $deprecatedPatch['packageName'] . ' ' . $deprecatedPatch['packageConstraint'],
                        $patchData['patchId'],
                        $patchData['packageName'] . ' ' . $patchData['packageConstraint']
                    );
                },
                $matchedDependencies
            );
            $result = array_merge($result, $errorMessages);
        }

        return $result;
    }

    /**
     * Returns patch data.
     *
     * @return array
     */
    private function getPatchData(): array
    {
        $result = [];
        foreach ($this->config->get() as $patchId => $patchGeneralConfig) {
            foreach ($patchGeneralConfig['packages'] as $packageName => $packageConfiguration) {
                foreach ($packageConfiguration as $packageConstraint => $patchInfo) {
                    $isDeprecated = $patchInfo['replaced-with'] ?? $patchInfo['deprecated'] ?? false;
                    $result[] = [
                        'patchId' => $patchId,
                        'packageName' => $packageName,
                        'packageConstraint' => $packageConstraint,
                        'require' => $patchInfo['require'] ?? [],
                        'replacedWith' => $patchInfo['replaced-with'] ?? '',
                        'deprecated' => (bool)$isDeprecated
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Returns deprecated patches.
     *
     * @return array
     */
    private function getDeprecated(): array
    {
        return array_filter(
            $this->getPatchData(),
            function ($item) {
                return $item['deprecated'];
            }
        );
    }

    /**
     * Returns not-deprecated patches that require other patches.
     *
     * @return array
     */
    private function getNotDeprecatedWithRequire(): array
    {
        return array_filter(
            $this->getPatchData(),
            function ($item) {
                return !$item['deprecated'] && $item['require'];
            }
        );
    }
}

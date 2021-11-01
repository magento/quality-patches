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
class ConstraintsIntersectionTest extends TestCase
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
    protected function setUp(): void
    {
        $this->config = new Config();
        $this->versionParser = new VersionParser();
    }

    /**
     * Verifies for constraint intersections.
     *
     * @doesNotPerformAssertions
     */
    public function testConstraintsIntersection()
    {
        $config = $this->getConfig();

        $errors = [];
        foreach ($config as $item) {
            $intersection = $this->getIntersection($item['packageConstraints']);

            if (!empty($intersection)) {
                $errors[] = sprintf(
                    " - %s has a dependency on '%s' with next constraints intersection: %s",
                    $item['id'],
                    $item['packageName'],
                    array_reduce(
                        $intersection,
                        function ($result, $item) {
                            $result .= PHP_EOL . '   - "' . $item[0] . '" and "' . $item[1] . '"';

                            return $result;
                        }
                    )
                );
            }
        }

        if (!empty($errors)) {
            array_unshift(
                $errors,
                "Found composer constraints intersection for next patch configurations in patches.json:"
            );
            array_push(
                $errors,
                'Please, eliminate constraint intersections'. PHP_EOL
            );
            $this->fail(
                implode(PHP_EOL, $errors)
            );
        }
    }

    /**
     * Search for intersections.
     *
     * @param array $versionConstraints
     * @return array
     */
    private function getIntersection(array $versionConstraints): array
    {
        $result = [];
        foreach ($versionConstraints as $key => $constraint) {
            $constraintsToCompare = array_diff_key($versionConstraints, [$key => $constraint]);
            foreach ($constraintsToCompare as $constraintToCompare) {
                $packageConstraint = $this->versionParser->parseConstraints(
                    $constraint
                );
                if ($packageConstraint->matches($this->versionParser->parseConstraints($constraintToCompare))
                    && !in_array([$constraintToCompare, $constraint], $result)) {
                    $result[] = [$constraint, $constraintToCompare];
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getConfig(): array
    {
        $result = [];
        foreach ($this->config->get() as $patchId => $patchGeneralConfig) {
            foreach ($patchGeneralConfig['packages'] as $packageName => $packageConfiguration) {
                $result[] = [
                    'id' => $patchId,
                    'packageName' => $packageName,
                    'packageConstraints' => array_keys($packageConfiguration)
                ];
            }
        }

        return $result;
    }
}

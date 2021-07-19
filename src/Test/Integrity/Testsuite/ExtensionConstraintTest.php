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
class ExtensionConstraintTest extends TestCase
{
    const REFERENCE_WIKI_PAGE = 'https://wiki.corp.magento.com/display/CENG/Table+of+Magento+Releases';
    /**
     * @var Config
     */
    private $config;

    /**
     * @var VersionParser
     */
    private $versionParser;

    /**
     * @var string[]
     *
     * General version constraints for extensions.
     * @see https://wiki.corp.magento.com/display/CENG/Table+of+Magento+Releases
     */
    private $extensionConstraints = [
        'magento/magento2-b2b-base' => '<2.0.0',
        'magento/inventory-composer-metapackage' => '<1.2.0',
        'magento/inventory-metapackage' => '>=1.2.0 <2.0.0',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->config = new Config();
        $this->versionParser = new VersionParser();
    }

    /**
     * Validates version constraint for some extensions.
     *
     * @doesNotPerformAssertions
     */
    public function testExtensionVersion()
    {
        $data = $this->getPatchesConstraints();

        $errors = [];
        foreach ($data as $item) {
            $validConstraint = $this->extensionConstraints[$item['packageName']] ?? null;
            if ($validConstraint === null) {
                continue;
            }

            $packageConstraint = $this->versionParser->parseConstraints(
                $item['packageConstraint']
            );
            $extensionConstraint = $this->versionParser->parseConstraints(
                $validConstraint
            );

            if (!$extensionConstraint->matches($packageConstraint)) {
                $errors[] = sprintf(
                    " - %s has a dependency on '%s' with constraint '%s', but correct constraint should match '%s'",
                    $item['id'],
                    $item['packageName'],
                    $item['packageConstraint'],
                    $validConstraint
                );
            }
        }

        if (!empty($errors)) {
            array_unshift(
                $errors,
                "Found invalid composer constraints for next patch configurations in patches.json:"
            );
            array_push(
                $errors,
                'Please, use ' . self::REFERENCE_WIKI_PAGE . ' for reference'. PHP_EOL
            );
            $this->fail(
                implode(PHP_EOL, $errors)
            );
        }
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function getPatchesConstraints(): array
    {
        $result = [];
        foreach ($this->config->get() as $patchId => $patchGeneralConfig) {
            foreach ($patchGeneralConfig['packages'] as $packageName => $packageConfiguration) {
                foreach ($packageConfiguration as $versionConstraint => $patchInfo) {
                    $result[] = [
                        'id' => $patchId,
                        'packageName' => $packageName,
                        'packageConstraint' => $versionConstraint
                    ];
                }
            }
        }

        return $result;
    }
}

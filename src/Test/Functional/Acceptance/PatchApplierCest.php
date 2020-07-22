<?php
/**
 * © Copyright 2020 Adobe. All rights reserved.
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

namespace Magento\QualityPatches\Test\Functional\Acceptance;

use Magento\CloudDocker\Test\Functional\Codeception\Docker;

/**
 * This test runs on the latest version of PHP
 */
class PatchApplierCest extends AbstractCest
{
    /**
     * @param \CliTester $I
     * @throws \Robo\Exception\TaskException
     */
    public function testApplyingPatch(\CliTester $I): void
    {
        $this->prepareTemplate($I, 'master');
        $I->copyFileToWorkDir('files/debug_logging/.magento.env.yaml', '.magento.env.yaml');
        $I->runEceDockerCommand('build:compose --mode=production');
        $I->copyFileToWorkDir('files/patches/target_file.md', 'target_file.md');
        $I->copyFileToWorkDir('files/patches/patch.patch', 'm2-hotfixes/patch.patch');

        // For this test, only the build phase is enough
        $I->runDockerComposeCommand('run build cloud-build');
        $I->startEnvironment();

        $targetFile = $I->grabFileContent('/target_file.md', Docker::BUILD_CONTAINER);
        $I->assertContains('# Hello Magento', $targetFile);
        $I->assertContains('## Additional Info', $targetFile);
        $log = $I->grabFileContent('/var/log/cloud.log', Docker::BUILD_CONTAINER);
        $I->assertContains('Patch "/app/m2-hotfixes/patch.patch" applied', $log);
    }

    /**
     * @param \CliTester $I
     * @throws \Robo\Exception\TaskException
     */
//    public function testApplyingExistingPatch(\CliTester $I): void
//    {
//        $this->prepareTemplate($I, 'master');
//        $I->copyFileToWorkDir('files/debug_logging/.magento.env.yaml', '.magento.env.yaml');
//        $I->runEceDockerCommand('build:compose --mode=production');
//        $I->copyFileToWorkDir('files/patches/target_file_applied_patch.md', 'target_file.md');
//        $I->copyFileToWorkDir('files/patches/patch.patch', 'm2-hotfixes/patch.patch');
//
//        // For this test, only the build phase is enough
//        $I->runDockerComposeCommand('run build cloud-build');
//        $I->startEnvironment();
//
//        $targetFile = $I->grabFileContent('/target_file.md', Docker::BUILD_CONTAINER);
//        $I->assertContains('# Hello Magento', $targetFile);
//        $I->assertContains('## Additional Info', $targetFile);
//        $I->assertContains(
//            'Patch "/app/m2-hotfixes/patch.patch" was already applied',
//            $I->grabFileContent('/var/log/cloud.log', Docker::BUILD_CONTAINER)
//        );
//    }
}

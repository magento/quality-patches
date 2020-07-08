<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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

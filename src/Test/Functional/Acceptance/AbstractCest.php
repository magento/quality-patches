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

namespace Magento\QualityPatches\Test\Functional\Acceptance;

/**
 * Abstract class with implemented before/after Cest steps.
 */
class AbstractCest
{
    /**
     * @var string
     */
    protected $edition = 'B2B';

    /**
     * @param \CliTester $I
     * @param \Codeception\Example $data
     * @throws \Robo\Exception\TaskException
     * @dataProvider patchesDataProvider
     */
    public function testPatches(\CliTester $I, \Codeception\Example $data): void
    {
        $this->prepareTemplate($I, $data['templateVersion'], $data['magentoVersion'] ?? null);
        $I->copyFileToWorkDir('files/patches/.apply_quality_patches.env.yaml', '.magento.env.yaml');
        $I->generateDockerCompose(sprintf(
            '--mode=production --env-vars="%s"',
            $this->convertEnvFromArrayToJson(
                [
                    'MAGENTO_CLOUD_PROJECT' => 'travis-testing',
                    'COMPOSER_MEMORY_LIMIT' => '-1'
                ]
            )
        ));
        $I->assertTrue($I->runDockerComposeCommand('run build cloud-build'));
        $I->assertTrue($I->startEnvironment());
        $this->writeToConsole($I->grabFileContent('/init/var/log/patch.log'));
        $I->assertTrue($I->runDockerComposeCommand('run deploy cloud-deploy'));
        $I->assertTrue($I->runDockerComposeCommand('run deploy cloud-post-deploy'));
        $I->amOnPage('/');
        $I->see('Home page');
        $I->see('CMS homepage content goes here.');
    }

    /**
     * @return array
     */
    protected function patchesDataProvider(): array
    {
        return [];
    }

    /**
     * @param \CliTester $I
     */
    public function _before(\CliTester $I): void
    {
        $I->cleanupWorkDir();
    }

    /**
     * @param \CliTester $I
     * @param string $templateVersion
     * @param string $magentoVersion
     */
    protected function prepareTemplate(\CliTester $I, string $templateVersion, string $magentoVersion = null): void
    {
        $I->cloneTemplateToWorkDir($templateVersion);
        $I->createAuthJson();
        $I->createArtifactsDir();
        $I->createArtifactCurrentTestedCode('patches', '1.1.999');
        $I->addArtifactsRepoToComposer();
        $I->addEceDockerGitRepoToComposer();
        $I->addCloudPatchesGitRepoToComposer();
        $I->addEceToolsGitRepoToComposer();
        $I->addCloudComponentsGitRepoToComposer();
        $I->addDependencyToComposer('magento/quality-patches', '1.1.999');
        $I->addDependencyToComposer(
            'magento/magento-cloud-patches',
            $I->getDependencyVersion('magento/magento-cloud-patches')
        );
        $I->addDependencyToComposer(
            'magento/magento-cloud-docker',
            $I->getDependencyVersion('magento/magento-cloud-docker')
        );
        $I->addDependencyToComposer(
            'magento/magento-cloud-components',
            $I->getDependencyVersion('magento/magento-cloud-components')
        );
        $I->addDependencyToComposer(
            'magento/ece-tools',
            $I->getDependencyVersion('magento/ece-tools')
        );

        if ($this->edition === 'CE' || $magentoVersion) {
            $version = $magentoVersion ?: $this->getVersionRangeForMagento($I);
            $I->removeDependencyFromComposer('magento/magento-cloud-metapackage');
            $I->addDependencyToComposer(
                $this->edition === 'CE' ? 'magento/product-community-edition' : 'magento/product-enterprise-edition',
                $version
            );
        }

        // Add B2B if Magento version >= 2.2.0 and B2B
        if ($this->edition === 'B2B' && version_compare($templateVersion, '2.2.0', '>=')) {
            $I->addDependencyToComposer('magento/extension-b2b', '*');
        }

        $I->composerUpdate();
    }

    /**
     * @param \CliTester $I
     * @return string
     */
    protected function getVersionRangeForMagento(\CliTester $I): string
    {
        $composer = json_decode(file_get_contents($I->getWorkDirPath() . '/composer.json'), true);

        return $composer['require']['magento/magento-cloud-metapackage'] ?? '';
    }

    /**
     * @param array $data
     * @return string
     */
    protected function convertEnvFromArrayToJson(array $data): string
    {
        return addslashes(json_encode($data));
    }

    /**
     * @param string $data
     */
    protected function writeToConsole(string $data): void
    {
        $output = new \Codeception\Lib\Console\Output([]);
        $output->writeln($data);
    }

    /**
     * @param \CliTester $I
     */
    public function _after(\CliTester $I): void
    {
        $I->stopEnvironment();
        $I->removeWorkDir();
    }
}

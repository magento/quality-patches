<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
    protected $edition = 'EE';

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
     */
    protected function prepareTemplate(\CliTester $I, string $templateVersion): void
    {
        $I->cloneTemplateToWorkDir($templateVersion);
        $I->createAuthJson();
        $I->createArtifactsDir();
        $I->createArtifactCurrentTestedCode('patches', '1.0.99');
        $I->addArtifactsRepoToComposer();
        $I->addEceDockerGitRepoToComposer();
        $I->addCloudPatchesGitRepoToComposer();
        $I->addDependencyToComposer('magento/quality-patches', '1.0.99');
        $I->addDependencyToComposer(
            'magento/magento-cloud-patches',
            $I->getDependencyVersion('magento/magento-cloud-patches')
        );
        $I->addDependencyToComposer(
            'magento/magento-cloud-docker',
            $I->getDependencyVersion('magento/magento-cloud-docker')
        );

        if ($this->edition === 'CE') {
            $version = $this->getVersionRangeForMagento($I);
            $I->removeDependencyFromComposer('magento/magento-cloud-metapackage');
            $I->addDependencyToComposer('magento/ece-tools', '^2002.1.0');
            $I->addDependencyToComposer('magento/product-community-edition', $version);
        }

        $I->composerUpdate();
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
     * @param \CliTester $I
     */
    public function _after(\CliTester $I): void
    {
        $I->stopEnvironment();
        $I->removeWorkDir();
    }
}

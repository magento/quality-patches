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
abstract class AbstractCest
{
    /**
     * @var string
     */
    protected $edition = 'B2B';

    /**
     * @var array
     */
    private $dependencyListFor244 = [
        "magento/module-re-captcha-admin-ui" => "1.1.2",
        "magento/module-re-captcha-checkout" => "1.1.2",
        "magento/module-re-captcha-contact" => "1.1.1",
        "magento/module-re-captcha-customer" => "1.1.2",
        "magento/module-re-captcha-frontend-ui" => "1.1.2",
        "magento/module-re-captcha-migration" => "1.1.2",
        "magento/module-re-captcha-newsletter" => "1.1.2",
        "magento/module-re-captcha-paypal" => "1.1.2",
        "magento/module-re-captcha-review" => "1.1.2",
        "magento/module-re-captcha-send-friend" => "1.1.2",
        "magento/module-re-captcha-store-pickup" => "1.0.1",
        "magento/module-re-captcha-ui" => "1.1.2",
        "magento/module-re-captcha-user" => "1.1.2",
        "magento/module-re-captcha-validation" => "1.1.1",
        "magento/module-re-captcha-validation-api" => "1.1.1",
        "magento/module-re-captcha-version-2-checkbox" => "2.0.2",
        "magento/module-re-captcha-version-2-invisible" => "2.0.2",
        "magento/module-re-captcha-version-3-invisible" => "2.0.2",
        "magento/module-re-captcha-webapi-api" => "1.0.1",
        "magento/module-re-captcha-webapi-rest" => "1.0.1",
        "magento/module-re-captcha-webapi-graph-ql" => "1.0.1",
        "magento/module-re-captcha-webapi-ui" => "1.0.1",
        "magento/module-securitytxt" => "1.1.1",
        "magento/module-two-factor-auth" => "1.1.3",
        "magento/module-re-captcha-checkout-sales-rule" => "1.1.0",
        "magento/inventory-composer-installer" => "1.2.0",
        "magento/module-inventory" => "1.2.2",
        "magento/module-inventory-admin-ui" => "1.2.2",
        "magento/module-inventory-advanced-checkout" => "1.2.1",
        "magento/module-inventory-api" => "1.2.2",
        "magento/module-inventory-bundle-product" => "1.2.1",
        "magento/module-inventory-bundle-product-admin-ui" => "1.2.2",
        "magento/module-inventory-bundle-product-indexer" => "1.1.1",
        "magento/module-inventory-bundle-import-export" => "1.1.1",
        "magento/module-inventory-cache" => "1.2.2",
        "magento/module-inventory-catalog" => "1.2.2",
        "magento/module-inventory-catalog-admin-ui" => "1.2.2",
        "magento/module-inventory-catalog-api" => "1.3.2",
        "magento/module-inventory-catalog-search" => "1.2.2",
        "magento/module-inventory-configurable-product" => "1.2.2",
        "magento/module-inventory-configurable-product-admin-ui" => "1.2.2",
        "magento/module-inventory-configurable-product-indexer" => "1.2.2",
        "magento/module-inventory-configuration" => "1.2.2",
        "magento/module-inventory-configuration-api" => "1.2.1",
        "magento/module-inventory-distance-based-source-selection" => "1.2.2",
        "magento/module-inventory-distance-based-source-selection-admin-ui" => "1.2.1",
        "magento/module-inventory-distance-based-source-selection-api" => "1.2.1",
        "magento/module-inventory-elasticsearch" => "1.2.1",
        "magento/module-inventory-export-stock" => "1.2.1",
        "magento/module-inventory-export-stock-api" => "1.2.1",
        "magento/module-inventory-graph-ql" => "1.2.1",
        "magento/module-inventory-grouped-product" => "1.2.2",
        "magento/module-inventory-grouped-product-admin-ui" => "1.2.2",
        "magento/module-inventory-grouped-product-indexer" => "1.2.2",
        "magento/module-inventory-import-export" => "1.2.2",
        "magento/module-inventory-indexer" => "2.1.2",
        "magento/module-inventory-in-store-pickup" => "1.1.1",
        "magento/module-inventory-in-store-pickup-admin-ui" => "1.1.1",
        "magento/module-inventory-in-store-pickup-api" => "1.1.1",
        "magento/module-inventory-in-store-pickup-frontend" => "1.1.2",
        "magento/module-inventory-in-store-pickup-graph-ql" => "1.1.1",
        "magento/module-inventory-in-store-pickup-multishipping" => "1.1.1",
        "magento/module-inventory-in-store-pickup-quote" => "1.1.1",
        "magento/module-inventory-in-store-pickup-quote-graph-ql" => "1.1.1",
        "magento/module-inventory-in-store-pickup-sales" => "1.1.1",
        "magento/module-inventory-in-store-pickup-sales-admin-ui" => "1.1.2",
        "magento/module-inventory-in-store-pickup-sales-api" => "1.1.1",
        "magento/module-inventory-in-store-pickup-shipping" => "1.1.1",
        "magento/module-inventory-in-store-pickup-shipping-admin-ui" => "1.1.1",
        "magento/module-inventory-in-store-pickup-shipping-api" => "1.1.1",
        "magento/module-inventory-in-store-pickup-webapi-extension" => "1.1.1",
        "magento/module-inventory-low-quantity-notification" => "1.2.1",
        "magento/module-inventory-low-quantity-notification-admin-ui" => "1.2.2",
        "magento/module-inventory-low-quantity-notification-api" => "1.2.1",
        "magento/module-inventory-multi-dimensional-indexer-api" => "1.2.1",
        "magento/module-inventory-product-alert" => "1.2.2",
        "magento/module-inventory-quote-graph-ql" => "1.0.1",
        "magento/module-inventory-requisition-list" => "1.2.2",
        "magento/module-inventory-reservation-cli" => "1.2.2",
        "magento/module-inventory-reservations" => "1.2.1",
        "magento/module-inventory-reservations-api" => "1.2.1",
        "magento/module-inventory-sales" => "1.2.2",
        "magento/module-inventory-sales-admin-ui" => "1.2.2",
        "magento/module-inventory-sales-api" => "1.2.1",
        "magento/module-inventory-sales-frontend-ui" => "1.2.2",
        "magento/module-inventory-setup-fixture-generator" => "1.2.1",
        "magento/module-inventory-shipping" => "1.2.2",
        "magento/module-inventory-shipping-admin-ui" => "1.2.2",
        "magento/module-inventory-source-deduction-api" => "1.2.2",
        "magento/module-inventory-source-selection" => "1.2.1",
        "magento/module-inventory-source-selection-api" => "1.4.1",
        "magento/module-inventory-visual-merchandiser" => "1.1.2",
        "magento/module-inventory-swatches-frontend-ui" => "1.0.1",
        "magento/module-inventory-catalog-frontend-ui" => "1.0.2",
        "magento/module-inventory-configurable-product-frontend-ui" => "1.0.2",
        "magento/module-inventory-wishlist" => "1.0.1",
        "magento/module-inventory-catalog-search-bundle-product" => "1.0.1",
        "magento/module-inventory-catalog-search-configurable-product" => "1.0.1",
        "magento/module-page-builder" => "2.2.2",
        "magento/module-page-builder-analytics" => "1.6.2",
        "magento/module-cms-page-builder-analytics" => "1.6.2",
        "magento/module-page-builder-admin-analytics" => "1.1.2",
        "magento/module-catalog-page-builder-analytics" => "1.6.2",
        "magento/module-aws-s3-page-builder" => "1.0.2",
        "magento/module-banner-page-builder" => "2.2.2",
        "magento/module-banner-page-builder-analytics" => "1.7.1",
        "magento/module-catalog-staging-page-builder" => "1.7.1",
        "magento/module-staging-page-builder" => "2.2.2",
        "magento/module-cms-page-builder-analytics-staging" => "1.7.1",
        "magento/module-catalog-page-builder-analytics-staging" => "1.7.1",
        "magento/module-page-builder-admin-gws-admin-ui" => "1.7.1"
    ];

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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareTemplate(\CliTester $I, string $templateVersion, string $magentoVersion = null): void
    {
        $I->cloneTemplateToWorkDir($templateVersion);
        $I->createAuthJson();
        $I->createArtifactsDir();
        $I->createArtifactCurrentTestedCode('patches', '1.1.999');
        $I->addArtifactsRepoToComposer();
        $I->addDependencyToComposer('magento/quality-patches', '1.1.999');

        $I->addEceDockerGitRepoToComposer();
        $I->addCloudPatchesGitRepoToComposer();
        $I->addEceToolsGitRepoToComposer();
        $I->addCloudComponentsGitRepoToComposer();

        $dependencies = [
            'magento/magento-cloud-patches',
            'magento/magento-cloud-docker',
            'magento/magento-cloud-components',
            'magento/ece-tools',
        ];

        foreach ($dependencies as $dependency) {
            $I->assertTrue(
                $I->addDependencyToComposer($dependency, $I->getDependencyVersion($dependency)),
                'Can not add dependency ' . $dependency
            );
        }

        if ($magentoVersion === '2.4.4') {
            foreach ($this->dependencyListFor244 as $package => $version) {
                $I->assertTrue(
                    $I->addDependencyToComposer($package, $version),
                    "Can not override dependency {$package} with version {$version} for Adobe Commerce 2.4.4"
                );
            }
        }

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

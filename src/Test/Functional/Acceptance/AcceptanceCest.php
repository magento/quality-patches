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
 * @group php74
 */
class AcceptanceCest extends AbstractCest
{
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
        $I->runEceDockerCommand(sprintf(
            'build:compose --mode=production --env-vars="%s"',
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
        return [
            ['templateVersion' => '2.4.0', 'magentoVersion' => '2.4.0'],
            ['templateVersion' => '2.4.0', 'magentoVersion' => '2.4.0-p1'],
            ['templateVersion' => '2.4.0', 'magentoVersion' => '2.4.1'],
            ['templateVersion' => 'master'],
        ];
    }
}

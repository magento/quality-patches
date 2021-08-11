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
 * @group php74ce
 */
class CEx240x243Cest extends B2Bx240x243Cest
{
    /**
     * @var string
     */
    protected $edition = 'CE';

    /**
     * Remove this method when https://jira.corp.adobe.com/browse/ACMP-1212 will be fixed
     */
    protected function patchesDataProvider(): array
    {
        return [
            ['templateVersion' => '2.4.0', 'magentoVersion' => '2.4.0'],
            ['templateVersion' => '2.4.0', 'magentoVersion' => '2.4.0-p1'],
            ['templateVersion' => '2.4.1', 'magentoVersion' => '2.4.1'],
            ['templateVersion' => '2.4.1', 'magentoVersion' => '2.4.1-p1'],
            ['templateVersion' => '2.4.2', 'magentoVersion' => '2.4.2'],
            ['templateVersion' => '2.4.2', 'magentoVersion' => '2.4.2-p1'],
            ['templateVersion' => '2.4.2', 'magentoVersion' => '2.4.2-p2'],
            //['templateVersion' => '2.4.3', 'magentoVersion' => '2.4.3'],
        ];
    }
}

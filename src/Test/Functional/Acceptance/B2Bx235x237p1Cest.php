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
 * @group php73
 */
class B2Bx235x237p1Cest extends AbstractCest
{
    /**
     * @return array
     */
    protected function patchesDataProvider(): array
    {
        return [
            ['templateVersion' => '2.3.5', 'magentoVersion' => '2.3.5'],
            ['templateVersion' => '2.3.5', 'magentoVersion' => '2.3.5-p1'],
            ['templateVersion' => '2.3.5', 'magentoVersion' => '2.3.5-p2'],
            ['templateVersion' => '2.3.6', 'magentoVersion' => '2.3.6'],
            ['templateVersion' => '2.3.6', 'magentoVersion' => '2.3.6-p1'],
            ['templateVersion' => '2.3.7', 'magentoVersion' => '2.3.7'],
            ['templateVersion' => '2.3.7', 'magentoVersion' => '2.3.7-p1'],
            ['templateVersion' => '2.3.7', 'magentoVersion' => '2.3.7-p2'],
            ['templateVersion' => '2.3.7', 'magentoVersion' => '2.3.7-p3'],
            ['templateVersion' => '2.3.7', 'magentoVersion' => '2.3.7-p4'],
        ];
    }
}

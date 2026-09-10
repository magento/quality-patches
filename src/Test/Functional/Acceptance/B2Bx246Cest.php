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
 * @group php82
 */
class B2Bx246Cest extends AbstractCest
{
    /**
     * @return array<string, string>[]
     */
    protected function patchesDataProvider(): array
    {
        $p11p14 = ['mariaDbVersion' => '10.11', 'openSearchVersion' => '2', 'valkeyVersion' => '8.0'];
        $p15 = ['mariaDbVersion' => '10.11', 'openSearchVersion' => '2', 'valkeyVersion' => '8.1'];
        $p1p10 = ['mariaDbVersion' => '10.6', 'openSearchVersion' => '2'];

        return [
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6', 'b2bVersion' => '1.3.5'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p1', 'b2bVersion' => '1.3.5-p1'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p2', 'b2bVersion' => '1.3.5-p2'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p3', 'b2bVersion' => '1.3.5-p3'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p4', 'b2bVersion' => '1.3.5-p4'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p5', 'b2bVersion' => '1.3.5-p5'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p6', 'b2bVersion' => '1.3.5-p6'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p7', 'b2bVersion' => '1.3.5-p7'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p8', 'b2bVersion' => '1.5.0'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p9', 'b2bVersion' => '1.5.1'] + $p1p10,
            ['templateVersion' => '2.4.6-p1-p10', 'magentoVersion' => '2.4.6-p10', 'b2bVersion' => '1.5.2'] + $p1p10,
            ['templateVersion' => '2.4.6', 'magentoVersion' => '2.4.6-p11', 'b2bVersion' => '1.5.2-p1'] + $p11p14,
            ['templateVersion' => '2.4.6', 'magentoVersion' => '2.4.6-p12', 'b2bVersion' => '1.5.2-p2'] + $p11p14,
            ['templateVersion' => '2.4.6', 'magentoVersion' => '2.4.6-p13', 'b2bVersion' => '1.5.2-p3'] + $p11p14,
            ['templateVersion' => '2.4.6', 'magentoVersion' => '2.4.6-p14', 'b2bVersion' => '1.5.2-p4'] + $p11p14,
            ['templateVersion' => '2.4.6', 'magentoVersion' => '2.4.6-p15', 'b2bVersion' => '1.5.2-p5'] + $p15,
        ];
    }
}

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
 * @group php81
 */
class B2Bx245Cest extends AbstractCest
{
    /**
     * @return array<string, string>[]
     */
    protected function patchesDataProvider(): array
    {
        $v104 = ['mariaDbVersion' => '10.4', 'openSearchVersion' => '1'];
        $v105 = ['mariaDbVersion' => '10.5', 'openSearchVersion' => '1'];
        $v106os1 = ['mariaDbVersion' => '10.6', 'openSearchVersion' => '1'];
        $v106os2 = ['mariaDbVersion' => '10.6', 'openSearchVersion' => '2'];
        $v106valkey = ['mariaDbVersion' => '10.6', 'openSearchVersion' => '2', 'valkeyVersion' => '8.0'];
        $v1011p16 = ['mariaDbVersion' => '10.11', 'openSearchVersion' => '2', 'valkeyVersion' => '8.0'];
        $v1011p17 = ['mariaDbVersion' => '10.11', 'openSearchVersion' => '2', 'valkeyVersion' => '8.1'];

        return [
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5', 'b2bVersion' => '1.3.4'] + $v104,
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5-p1', 'b2bVersion' => '1.3.4-p1'] + $v104,
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5-p2', 'b2bVersion' => '1.3.4-p2'] + $v104,
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5-p3', 'b2bVersion' => '1.3.4-p3'] + $v104,
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5-p4', 'b2bVersion' => '1.3.4-p4'] + $v104,
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5-p5', 'b2bVersion' => '1.3.4-p5'] + $v104,
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5-p6', 'b2bVersion' => '1.3.4-p6'] + $v104,
            ['templateVersion' => '2.4.5-p1-p7', 'magentoVersion' => '2.4.5-p7', 'b2bVersion' => '1.3.4-p7'] + $v104,
            ['templateVersion' => '2.4.5-p8-p10', 'magentoVersion' => '2.4.5-p8', 'b2bVersion' => '1.3.4-p8'] + $v105,
            ['templateVersion' => '2.4.5-p8-p10', 'magentoVersion' => '2.4.5-p9', 'b2bVersion' => '1.3.4-p9'] + $v105,
            ['templateVersion' => '2.4.5-p8-p10', 'magentoVersion' => '2.4.5-p10', 'b2bVersion' => '1.3.4-p10'] + $v105,
            ['templateVersion' => '2.4.5-p11-p16', 'magentoVersion' => '2.4.5-p11', 'b2bVersion' => '1.3.4-p11'] + $v106os1,
            ['templateVersion' => '2.4.5-p11-p16', 'magentoVersion' => '2.4.5-p12', 'b2bVersion' => '1.3.4-p12'] + $v106os2,
            ['templateVersion' => '2.4.5-p11-p16', 'magentoVersion' => '2.4.5-p13', 'b2bVersion' => '1.3.4-p13'] + $v106valkey,
            ['templateVersion' => '2.4.5-p11-p16', 'magentoVersion' => '2.4.5-p14', 'b2bVersion' => '1.3.4-p14'] + $v106valkey,
            ['templateVersion' => '2.4.5-p11-p16', 'magentoVersion' => '2.4.5-p15', 'b2bVersion' => '1.3.4-p15'] + $v106valkey,
            ['templateVersion' => '2.4.5', 'magentoVersion' => '2.4.5-p16', 'b2bVersion' => '1.3.4-p16'] + $v1011p16,
            ['templateVersion' => '2.4.5', 'magentoVersion' => '2.4.5-p17', 'b2bVersion' => '1.3.4-p17'] + $v1011p17,
        ];
    }
}

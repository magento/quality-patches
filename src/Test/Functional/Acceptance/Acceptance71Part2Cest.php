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
 * @group php71Part2
 */
class Acceptance71Part2Cest extends AcceptanceCest
{
    /**
     * @return array
     */
    protected function patchesDataProvider(): array
    {
        return [
            ['templateVersion' => '2.2.4'],
            ['templateVersion' => '2.2.5'],
            ['templateVersion' => '2.2.6'],
            ['templateVersion' => '2.2.7'],
            ['templateVersion' => '2.2.8'],
            ['templateVersion' => '2.2.9'],
            ['templateVersion' => '2.2.10'],
            ['templateVersion' => '2.2.11'],
        ];
    }
}

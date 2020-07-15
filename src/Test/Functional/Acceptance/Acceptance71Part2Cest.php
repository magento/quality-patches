<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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

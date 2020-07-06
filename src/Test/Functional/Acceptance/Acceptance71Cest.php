<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Functional\Acceptance;

/**
 * @group php71
 */
class Acceptance71Cest extends AcceptanceCest
{
    /**
     * @return array
     */
    protected function patchesDataProvider(): array
    {
        return [
            ['templateVersion' => '2.1.16'],
            ['templateVersion' => '2.1.17'],
            ['templateVersion' => '2.1.18'],
            ['templateVersion' => '2.2.0'],
            ['templateVersion' => '2.2.1'],
            ['templateVersion' => '2.2.2'],
            ['templateVersion' => '2.2.3'],
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

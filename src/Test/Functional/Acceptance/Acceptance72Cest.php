<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Functional\Acceptance;

/**
 * @group php72
 */
class Acceptance72Cest extends AcceptanceCest
{
    /**
     * @return array
     */
    protected function patchesDataProvider(): array
    {
        return [
            ['templateVersion' => '2.3.0'],
            ['templateVersion' => '2.3.1'],
            ['templateVersion' => '2.3.2', 'magentoVersion' => '2.3.2'],
            ['templateVersion' => '2.3.2', 'magentoVersion' => '2.3.2-p2'],
        ];
    }
}

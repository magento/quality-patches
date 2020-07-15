<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Functional\Acceptance;

/**
 * @group php71cePart2
 */
class Acceptance71CePart2Cest extends Acceptance71Part2Cest
{
    /**
     * @var string
     */
    protected $edition = 'CE';
}

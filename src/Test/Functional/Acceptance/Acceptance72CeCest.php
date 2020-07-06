<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Functional\Acceptance;

/**
 * @group php72ce
 */
class Acceptance72CeCest extends Acceptance72Cest
{
    /**
     * @var string
     */
    protected $edition = 'CE';
}

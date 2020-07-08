<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Functional\Acceptance;

/**
 * @group php71ce
 */
class Acceptance71CeCest extends Acceptance71Cest
{
    /**
     * @var string
     */
    protected $edition = 'CE';
}

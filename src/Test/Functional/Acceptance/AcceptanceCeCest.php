<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Functional\Acceptance;

/**
 * @group php73ce
 */
class AcceptanceCeCest extends AcceptanceCest
{
    /**
     * @var string
     */
    protected $edition = 'CE';
}

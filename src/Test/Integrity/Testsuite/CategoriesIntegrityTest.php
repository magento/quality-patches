<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Integrity\Testsuite;

use Exception;
use Magento\QualityPatches\Info;
use Magento\QualityPatches\Test\Integrity\Lib\Config;
use PHPUnit\Framework\TestCase;

/**
 * @inheritDoc
 */
class CategoriesIntegrityTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Info
     */
    private $info;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->config = new Config();
        $this->info = new Info();
    }

    public function testCategoriesIntegrity()
    {
        $config = $this->getCategoriesConfig();
        $predefinedCategories = $this->getPatchCategories();

        $errors = [];
        foreach ($config as $patchId => $categories) {
            foreach ($categories as $category) {
                if (!in_array($category, $predefinedCategories)) {
                    $errors[] = $patchId . ' contains incorrect categories.';
                }
            }
        }

        if (!empty($errors)) {
            $this->fail(implode(PHP_EOL, $errors));
        }
    }

    /**
     * @return array
     */
    private function getCategoriesConfig(): array
    {
        $categoriesConfig = [];
        foreach ($this->config->get() as $patchId => $patchGeneralConfig) {
            if (isset($patchGeneralConfig['categories'])) {
                $categoriesConfig[$patchId] = $patchGeneralConfig['categories'];
            }
        }
        return $categoriesConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getPatchCategories(): array
    {
        $data = file_get_contents($this->info->getCategoriesConfig());
        $result = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception($this->info->getCategoriesConfig() . ' has invalid format');
        }

        return array_column($result, 'name');
    }
}

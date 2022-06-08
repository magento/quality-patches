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

namespace Magento\QualityPatches\Test\Integrity\Testsuite;

use Exception;
use Magento\QualityPatches\Info;
use Magento\QualityPatches\Test\Integrity\Lib\Config;
use PHPUnit\Framework\TestCase;

/**
 * @inheritDoc
 */
class MetadataTest extends TestCase
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
    protected function setUp(): void
    {
        $this->config = new Config();
        $this->info = new Info();
    }

    /**
     * Verifies that metadata configuration related to logs has corespondent fixtures and
     * fixtures content match the patterns.
     *
     * @return void
     * @throws Exception
     * @see https://wiki.corp.adobe.com/display/CENG/Patch+Recommendations
     */
    public function testLogPatterns()
    {
        $items = $this->getLogMetadata();
        $errors = [];
        foreach ($items as $patchId => $metadata) {
            foreach ($metadata as $logPath => $patterns) {
                $fixturePath = $this->info->getTestsDirectory() . '/metadata/' . $patchId . '/' . $logPath;
                if (!file_exists($fixturePath)) {
                    $errors[] = "Metadata fixture for $patchId doesn't exist. "  .
                    "Please create fixture file $fixturePath with an unmodified error message from instance.";
                    continue;
                }

                foreach ($patterns as $pattern) {
                    $regExp = $this->convertToRegExp($pattern);
                    $cmd = sprintf('grep -m1 "%s" %s;', $regExp, $fixturePath);
                    $result = shell_exec($cmd);
                    if (is_null($result)) {
                        $errors[] = "Metadata test fail. Content of $fixturePath doesn't match the pattern '$pattern'";
                    }
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
    private function getLogMetadata(): array
    {
        $result = [];
        foreach ($this->config->get() as $patchId => $patchConfig) {
            if (isset($patchConfig['metadata'])) {
                $result[$patchId] = $this->getLogPatterns($patchConfig['metadata']);
            }
        }

        return $result;
    }

    /**
     * Recursively collects log paths and match patterns.
     *
     * @return array
     */
    private function getLogPatterns(array $metadata): array
    {
        $result = [];
        foreach ($metadata as $node) {
            if (isset($node['type'])) {
                if ($node['type'] === 'log') {
                    $result[$node['path']][] = $node['match'];
                }
            } else {
                $result = array_merge_recursive($result, $this->getLogPatterns($node));
            }
        }

        return $result;
    }

    /**
     * Returns regexp based on pattern from metadata config.
     *
     * @param string $pattern
     * @return string
     * @throws Exception
     */
    private function convertToRegExp(string $pattern): string
    {
        $searchReplace = [
            // replacing complex "search" or "replace" symbol(s) by unique phrases
            '\*' => 'escaped-star',
            '\?' => 'escaped-question-mark',
            '[' => 'escaped-left-square-bracket',
            ']' => 'escaped-right-square-bracket',
            '{' => 'escaped-left-figure-bracket',
            '}' => 'escaped-right-figure-bracket',
            '"' => 'escaped-double-quote',
            '`' => 'escaped-back-quote',
            '^' => 'escaped-caret',
            '$' => 'escaped-dollar',
            '@' => 'escaped-ampersat',
            '#' => 'escaped-sharp',
            '.' => 'escaped-dot',
            '+' => 'escaped-plus',
            // simple symbols replacing should follow complex symbols
            '?' => '.',
            '*' => '.*',
            '\\' => 'escaped-backslash',
            // backward replacing; unique phrases => escaped symbol(s)
            'escaped-star' => '[*]',
            'escaped-question-mark' => '[?]',
            'escaped-backslash' => '[\]',
            'escaped-left-square-bracket' => '\[',
            'escaped-right-square-bracket' => '\]',
            'escaped-left-figure-bracket' => '[{]',
            'escaped-right-figure-bracket' => '[}]',
            'escaped-double-quote' => '\"',
            'escaped-back-quote' => '\`',
            'escaped-caret' => '\^',
            'escaped-dollar' => '\$',
            'escaped-ampersat' => '\@',
            'escaped-sharp' => '\#',
            'escaped-dot' => '[.]',
            'escaped-plus' => '[+]',
        ];

        return str_replace(array_keys($searchReplace), array_values($searchReplace), $pattern);
    }
}

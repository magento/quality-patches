<?php
/**
 * Copyright 2013-present Adobe. All rights reserved.
 * Each source file included in this directory is licensed under OSL 3.0 or your existing
 * commercial license or subscription agreement with Magento or its Affiliates (the "Agreement).
 *
 * http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * Please see LICENSE_OSL.txt for the full text of the OSL 3.0 license or contact engcom@adobe.com for a copy.
 *
 * Subject to your payment of fees and compliance with the terms and conditions of the Agreement,
 * the Agreement supersedes the OSL 3.0 license for each source file included in this directory.
 */
declare(strict_types=1);

namespace Magento\QualityPatches;

include __DIR__ . '/../vendor/autoload.php';
use Composer\Semver\Semver;

$export = new UpdateInfoJson();
$export->run();

/**
 * Updates patch-info.json (data source for QPT landing page).
 */
class UpdateInfoJson
{
    private const KB_ARTICLES_SECTION = '360010506631';

    /**
     * Generates JSON file.
     */
    public function run()
    {
        $supportPatches = $this->getSourceData(
            $this->readJson(__DIR__ . '/../support-patches.json'),
            'Adobe Commerce Support'
        );
        $cloudPatches = $this->getSourceData(
            $this->convertCloudToQPT(
                'https://raw.githubusercontent.com/magento/magento-cloud-patches/develop/patches.json'
            ),
            'Adobe Commerce Support'
        );
        $communityPatches = $this->getSourceData(
            $this->readJson(__DIR__ . '/../community-patches.json'),
            'Magento OS Community'
        );
        $patches = array_merge($supportPatches, $cloudPatches, $communityPatches);

        $articles = $this->getPatchArticles();
        foreach ($patches as $key => $item) {
            if (isset($articles[$item['id']])) {
                $patches[$key]['link'] = $articles[$item['id']];
            }
        }

        $filePath = __DIR__ . '/../patches-info.json';
        $version = $this->getQptVersion();
        file_put_contents(
            $filePath,
            json_encode(['version' => $version, 'patches' => $patches])
        );
        echo  "$filePath updated successfully. Contains " . count($patches) . " patches.\n".
            "Version of magento/quality-patches is {$version}";
    }

    /**
     * Converts Cloud patch config to QPT config format.
     *
     * @param string $sourceUrl
     * @return array
     */
    private function convertCloudToQPT(string $sourceUrl): array
    {
        $config = $this->readJson($sourceUrl);
        $result = [];
        $path = 'https://raw.githubusercontent.com/magento/magento-cloud-patches/develop/patches/';
        foreach ($config as $packageName => $packagePatches) {
            foreach ($packagePatches as $patchTitle => $patchConfiguration) {
                foreach ($patchConfiguration as $packageConstraint => $patchData) {
                    $patchFile = $patchData;
                    preg_match(
                        '#(?<id>.*?)__(?<description>.*?)__(?<version>.*?)\.patch#',
                        $patchFile,
                        $patch
                    );
                    $patchId = $patch['id'];
                    $packageConfig = [
                        $packageName => [
                            $packageConstraint => ['file' => $path . $patchFile]
                        ]
                    ];
                    if (isset($result[$patchId])) {
                        $result[$patchId]['packages'] = array_merge_recursive(
                            $result[$patchId]['packages'],
                            $packageConfig
                        );
                    } else {
                        $result[$patchId] = [
                            'categories' => ['Other'],
                            'title' => $patchTitle,
                            'packages' => $packageConfig
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $config
     * @param string $origin
     * @return array
     */
    private function getSourceData(array $config, string $origin): array
    {
        $result = [];
        $releases = json_decode(file_get_contents(__DIR__ . '/../magento_releases.json'), true);
        foreach ($config as $patchId => $patchGeneralConfig) {
            $compatibleReleases = [];
            $patchConstraints = [];
            $data = [];

            foreach ($patchGeneralConfig['packages'] as $packageName => $packageConstraints) {
                if ($this->isDeprecated($packageConstraints)) {
                    $data['deprecated'] = true;
                };

                if ($replacedWith = $this->getReplacedWith($packageConstraints)) {
                    $data['replacedWith'] = $replacedWith;
                }

                if (!empty($packageConstraints)) {
                    $patchConstraints[$packageName] = array_keys($packageConstraints);
                }
            }

            foreach ($releases as $release => $dependencies) {
                if ($this->isApplicable($patchConstraints, $dependencies)) {
                    $compatibleReleases[] = $release;
                }
            }

            if (empty($compatibleReleases)) {
                continue;
            }
            $data['id'] = $patchId;
            $data['description'] = $patchGeneralConfig['title'];
            $data['categories'] = $patchGeneralConfig['categories'];
            $data['releases'] = $compatibleReleases;
            $data['origin'] = $origin;
            $data['components'] = $this->getAffectedComponents($patchGeneralConfig['packages']);
            array_push($result, $data);
        }

        return $result;
    }

    /**
     * Returns source json.
     *
     * @param string $configPath
     * @return array
     */
    private function readJson(string $configPath): array
    {
        $content = file_get_contents($configPath);

        return json_decode($content, true);
    }

    /**
     * Returns magento/quality-patches release version.
     *
     * @return string
     */
    private function getQptVersion(): string
    {
        $result = $this->readJson(__DIR__ . '/../composer.json');

        return $result['version'];
    }

    /**
     * Determines if patch is applicable to Magento version.
     *
     * @param array $patchConstraints
     * @param array $releaseDependencies
     * @return bool
     */
    private function isApplicable(array $patchConstraints, array $releaseDependencies): bool
    {
        foreach ($patchConstraints as $package => $versionConstraints) {
            if (!isset($releaseDependencies[$package])) {
                continue;
            }
            foreach ($versionConstraints as $versionConstraint) {
                if (Semver::satisfies($releaseDependencies[$package], $versionConstraint)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns list of affected modules.
     *
     * @param string $path
     * @return array
     */
    private function extractModules(string $path): array
    {
        $path = strpos($path, 'http') === 0 ? $path : __DIR__ . '/../patches/' . $path;
        $content = file_get_contents($path);

        $result = [];
        if (preg_match_all(
            '#^.* [ab]/vendor/(?<vendor>.*?)/(?<component>.*?)/.*$#mi',
            $content,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                $result[] = $match['vendor'] . '/' . $match['component'];
            }
        }

        if (preg_match_all(
            '#^.* [ab]/(?<folder>.*?)/(?<subfolder>.*?)[/ ].*$#mi',
            $content,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                if ($match['folder'] !== 'vendor') {
                    $result[] = $match['folder'] . '/' . $match['subfolder'];
                }
            }
        }

        $result = array_unique($result);
        sort($result);

        return $result;
    }

    /**
     * Returns the list of patch articles from support.magento.com
     *
     * @return array
     */
    private function getPatchArticles(): array
    {
        $result = [];
        $page = 1;
        do {
            $apiUrl = sprintf(
                'https://support.magento.com/api/v2/help_center/articles/search.json?section=%d&page=%d',
                self::KB_ARTICLES_SECTION,
                $page
            );
            $response = json_decode(file_get_contents($apiUrl), true);
            $result = array_merge($result, $response['results']);
            $page = $response['next_page'] ? (int)$response['page'] + 1 : null;
        } while ($page);

        $data = [];
        foreach ($result as $article) {
            $matches = [];
            if (preg_match('/(MDVA|MC)-(\d+)/i', $article['title'], $matches)) {
                $data[$matches[0]] = $article['html_url'];
            }
        }

        return $data;
    }

    /**
     * Returns affected components.
     *
     * @param array $config
     * @return array
     */
    private function getAffectedComponents(array $config): array
    {
        $affectedComponents = [];
        foreach ($config as $packageConfiguration) {
            foreach ($packageConfiguration as $patchInfo) {
                $affectedComponents = array_unique(
                    array_merge($affectedComponents, $this->extractModules($patchInfo['file']))
                );
            }
        }

        return array_values($affectedComponents);
    }

    /**
     * Returns id of the patch replacement.
     *
     * @param array $packageConstraints
     * @return string
     */
    private function getReplacedWith(array $packageConstraints): string
    {
        $result = '';
        foreach ($packageConstraints as $item) {
            if (isset($item['replaced-with'])) {
                $result = $item['replaced-with'];
            }
        }

        return $result;
    }

    /**
     * Check if patch is marked as deprecated.
     *
     * @param array $packageConstraints
     * @return bool
     */
    private function isDeprecated(array $packageConstraints): bool
    {
        return !empty(array_filter(
            $packageConstraints,
            function ($item) {
                return isset($item['deprecated']);
            }
        ));
    }
}

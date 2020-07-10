<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QualityPatches\Test\Integrity\Lib;

/**
 * Build circular dependencies by modules map
 */
class CircularDependency
{
    /**
     * Map where the key is the vertex and the value are the adjacent vertices(dependencies) of this vertex
     *
     * @var array
     */
    private $dependencies = [];

    /**
     * Modules circular dependencies map
     *
     * @var array
     */
    private $circularDependencies = [];

    /**
     * Graph object
     *
     * @var Graph
     */
    private $graph;

    /**
     * @param array $dependencies
     */
    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
        $this->graph = new Graph(array_keys($this->dependencies), []);
    }

    /**
     * Returns circular dependencies if any.
     *
     * @return array
     */
    public function get(): array
    {
        foreach (array_keys($this->dependencies) as $vertex) {
            $this->expandDependencies($vertex);
        }

        $circulars = $this->graph->findCycle(null, false);
        foreach ($circulars as $circular) {
            array_shift($circular);
            $this->buildCircular($circular);
        }

        return $this->divideByItems($this->circularDependencies);
    }

    /**
     * Expand items dependencies from chain.
     *
     * @param string $vertex
     * @param array $path nesting path
     * @return void
     */
    private function expandDependencies(string $vertex, array $path = [])
    {
        if (!$this->dependencies[$vertex]) {
            return;
        }

        $path[] = $vertex;
        foreach ($this->dependencies[$vertex] as $dependency) {
            if (!isset($this->dependencies[$dependency])) {
                // dependency vertex is not described in basic definition
                continue;
            }
            $relations = $this->graph->getRelations();
            if (isset($relations[$vertex][$dependency])) {
                continue;
            }
            $this->graph->addRelation($vertex, $dependency);

            $searchResult = array_search($dependency, $path);

            if (false !== $searchResult) {
                $this->buildCircular(array_slice($path, $searchResult));
                break;
            } else {
                $this->expandDependencies($dependency, $path);
            }
        }
    }

    /**
     * Build all circular dependencies based on chain.
     *
     * @param array $items
     * @return void
     */
    private function buildCircular(array $items)
    {
        $path = '/' . implode('/', $items);
        if (isset($this->circularDependencies[$path])) {
            return;
        }

        $this->circularDependencies[$path] = $items;
        $items[] = array_shift($items);
        $this->buildCircular($items);
    }

    /**
     * Divide dependencies by items
     *
     * @param array $circularDependencies
     * @return array
     */
    private function divideByItems(array $circularDependencies): array
    {
        $dependenciesByModule = [];
        foreach ($circularDependencies as $circularDependency) {
            $module = $circularDependency[0];
            $circularDependency[] = $module;
            $dependenciesByModule[$module][] = $circularDependency;
        }

        return $dependenciesByModule;
    }
}

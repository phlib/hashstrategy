<?php

declare(strict_types=1);

namespace Phlib\HashStrategy;

/**
 * Interface HashStrategy
 * @package Phlib\HashStrategy
 */
interface HashStrategyInterface
{
    /**
     * @return $this
     */
    public function add(string $node, int $weight = 1);

    /**
     * @return $this
     */
    public function remove(string $node);

    public function get(string $key, int $count = 1): array;
}

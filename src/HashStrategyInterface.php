<?php

declare(strict_types=1);

namespace Phlib\HashStrategy;

/**
 * @package Phlib\HashStrategy
 */
interface HashStrategyInterface
{
    public function add(string $node, int $weight = 1): self;

    public function remove(string $node): self;

    public function get(string $key, int $count = 1): array;
}

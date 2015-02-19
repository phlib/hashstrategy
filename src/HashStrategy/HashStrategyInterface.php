<?php

namespace Phlib\HashStrategy;

/**
 * Interface HashStrategy
 * @package Phlib\HashStrategy
 */
interface HashStrategyInterface
{
    /**
     * Add
     *
     * @param string $node
     * @param int $weight
     * @return HashStrategy
     */
    public function add($node, $weight = 1);

    /**
     * Remove
     *
     * @param string $node
     * @return HashStrategy
     */
    public function remove($node);

    /**
     * Get
     *
     * @param string $key
     * @param int $count
     * @return array
     */
    public function get($key, $count = 1);
}

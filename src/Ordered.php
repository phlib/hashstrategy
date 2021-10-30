<?php

namespace Phlib\HashStrategy;

/**
 * Class Ordered
 *
 * @package Phlib\HashStrategy
 */
class Ordered implements HashStrategyInterface
{
    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var int
     */
    protected $counter = 1000;

    /**
     * @var bool
     */
    protected $sorted = false;

    public function add(string $node, int $weight = 1): self
    {
        if (!in_array($node, $this->nodes)) {
            // add the node to the nodes array
            if ($weight) {
                $key = 'w'
                    . str_pad($weight, 3, 0, STR_PAD_LEFT)
                    . '.'
                    . str_pad(--$this->counter, 3, 0, STR_PAD_LEFT);

                $this->nodes[$key] = $node;
                $this->sorted = false;
            }
        }

        return $this;
    }

    public function remove(string $node): self
    {
        $nodeIndex = array_search($node, $this->nodes);
        if ($nodeIndex !== false) {
            // remove the found node
            unset($this->nodes[$nodeIndex]);
        }

        return $this;
    }

    public function get(string $key, int $count = 1): array
    {
        if (!$this->sorted) {
            krsort($this->nodes, SORT_STRING);
            $this->sorted = true;
        }

        return array_slice(array_values($this->nodes), 0, $count);
    }
}

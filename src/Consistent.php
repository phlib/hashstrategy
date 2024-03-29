<?php

declare(strict_types=1);

namespace Phlib\HashStrategy;

/**
 * @package Phlib\HashStrategy
 */
class Consistent implements HashStrategyInterface
{
    /**
     * Allow override so extra hash methods can be added
     * @see Consistent::hash()
     */
    protected const HASH_AVAILABLE = ['crc32', 'md5'];

    private int $replicas = 64;

    private array $nodes = [];

    private array $circle = [];

    private array $positions = [];

    private string $hashType = 'crc32';

    public function __construct(string $hashType = 'crc32')
    {
        $availableTypes = static::HASH_AVAILABLE;
        if (!in_array($hashType, $availableTypes, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid hash type provided '%s'",
                    $hashType
                )
            );
        }

        $this->hashType = $hashType;
    }

    public function add(string $node, int $weight = 1): self
    {
        // make sure we haven't already add this node
        if (!in_array($node, $this->nodes, true)) {
            // reset sorted positions, adding a node invalidates
            $this->positions = [];
            // add the node to the nodes array
            $this->nodes[] = $node;
            // calculate how many replicas to use in the circle
            $replicas = round($this->replicas * $weight);
            for ($index = 0; $index < $replicas; $index++) {
                // hashing the node with the index will give us the position in the circle
                $this->circle[$this->hash("{$node}:{$index}")] = $node;
            }
        }

        return $this;
    }

    public function remove(string $node): self
    {
        // find the node index for removal
        $nodeIndex = array_search($node, $this->nodes, true);
        if ($nodeIndex !== false) {
            // reset sorted positions, removing a node invalidates
            $this->positions = [];
            // remove the found node
            unset($this->nodes[$nodeIndex]);
            // loop the positions in the circle
            $positions = array_keys($this->circle);
            foreach ($positions as $position) {
                // if the position on the circle contains the node we're removing
                // then remove it
                if ($this->circle[$position] === $node) {
                    unset($this->circle[$position]);
                }
            }
        }

        return $this;
    }

    protected function hash(string $value): string
    {
        switch ($this->hashType) {
            case 'md5':
                return substr(md5($value), 0, 8);

            case 'crc32':
                return (string)crc32($value);
        }

        throw new \DomainException(
            sprintf(
                "No hash method configured for '%s'",
                $this->hashType
            )
        );
    }

    public function get(string $key, int $count = 1): array
    {
        // this will be our lookup
        $hash = $this->hash($key);
        // if the stored positions are empty then we need to calculate
        // the positions sorted ready for processing
        if (empty($this->positions)) {
            $this->positions = array_keys($this->circle);
            sort($this->positions);
        }

        $collected = [];
        $found = 0;

        // loop though every position
        foreach ($this->positions as $position) {
            // collect positions matching the hash position or above
            if ($position >= $hash) {
                // fetch the node value
                $node = $this->circle[$position];
                // make sure we haven't collected this node already
                if (!in_array($node, $collected, true)) {
                    // collect the node
                    $collected[] = $node;
                    // increment the found count
                    $found++;
                    // if we've found the amount we need break the loop
                    if ($found === $count) {
                        break;
                    }
                }
            }
        }

        // if the amount we've found is less than the amount we need
        // we have more work to do
        if ($found < $count) {
            // loop though every position
            foreach ($this->positions as $position) {
                // this time we start collecting stright away
                $node = $this->circle[$position];
                // make sure we haven't collected this node already
                if (!in_array($node, $collected, true)) {
                    // collect the node to return
                    $collected[] = $node;
                    // increment the found count
                    $found++;
                    // if we've found the amount we need break the loop
                    if ($found === $count) {
                        break;
                    }
                }
            }
        }

        // return the nodes we've collected
        return $collected;
    }
}

<?php

declare(strict_types=1);

namespace Phlib\HashStrategy;

/**
 * ConfigPool
 *
 * Used for hashing a pool of configs
 *
 * === Example ===
 * $config = array(
 *      'server1' => array('hostname' => 'localhost', 'port' => 11211),
 *      'server2' => array('hostname' => 'localhost', 'port' => 11212),
 *      'server3' => array('hostname' => 'localhost', 'port' => 11213),
 * );
 * $pool = new Phlib\HashStrategy\ConfigPool($config);
 * var_dump($pool->getConfigList('some key', 2));
 *
 * @package Phlib\HashStrategy
 */
class ConfigPool
{
    private array $configList;

    private array $calculatedConfig = [];

    private HashStrategyInterface $hashStrategy;

    public function __construct(array $configList, HashStrategyInterface $hashStrategy = null)
    {
        // store the config array for later retrieval
        $this->configList = $configList;

        if (!$hashStrategy) {
            // no hash strategy was provided use a default
            $hashStrategy = new Ordered();
        }

        // setup the hashing
        $this->setHashStrategy($hashStrategy);
    }

    public function setHashStrategy(HashStrategyInterface $hashStrategy): self
    {
        // loop the config adding the key as a node
        foreach ($this->configList as $key => $value) {
            $hashStrategy->add(
                (string)$key,
                $value['weight'] ?? 1
            );
        }

        $this->hashStrategy = $hashStrategy;

        return $this;
    }

    public function getManyConfigs(string $key, int $count = 1): array
    {
        // find a calculated config list
        if (!array_key_exists("{$key}.{$count}", $this->calculatedConfig)) {
            // check we aren't storing too many calculated configs
            if (count($this->calculatedConfig) >= 100) {
                // remove the fist in the list, should be the oldest
                array_shift($this->calculatedConfig);
            }

            // get a list of config keys using the count and key provided
            $configList = [];
            foreach ($this->hashStrategy->get($key, $count) as $index) {
                // append the config values to the config list
                $configList[] = $this->configList[$index];
            }

            // store for later, a little config cache
            $this->calculatedConfig["{$key}.{$count}"] = $configList;
        }

        return $this->calculatedConfig["{$key}.{$count}"];
    }

    public function getConfig(string $key): array
    {
        // return the first matching config key
        $index = $this->hashStrategy->get($key, 1);

        return $this->configList[$index[0]];
    }

    public function getConfigList(): array
    {
        return $this->configList;
    }
}

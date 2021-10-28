<?php

namespace Phlib\HashStrategy;

/**
 * Pool Config
 *
 * Used for hashing a pool of configs
 *
 * === Example ===
 * $config = array(
 *      'server1' => array('hostname' => 'localhost', 'port' => 11211),
 *      'server2' => array('hostname' => 'localhost', 'port' => 11212),
 *      'server3' => array('hostname' => 'localhost', 'port' => 11213),
 * );
 * $pool = new Phlib\HashStrategy\Config($config);
 * var_dump($pool->getConfigList('some key', 2));
 *
 * Class Config
 * @package Phlib\HashStrategy
 */
class Config
{

    /**
     * @var array
     */
    protected $configList;

    /**
     * @var array
     */
    protected $calculatedConfig = array();

    /**
     * @var HashStrategyInterface
     */
    protected $hashStrategy;

    /**
     * Constructor
     *
     * @param array $configList
     * @param HashStrategyInterface $hashStrategy
     */
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

    /**
     * Set hash strategy
     *
     * @param HashStrategyInterface $hashStrategy
     * @return $this
     */
    public function setHashStrategy(HashStrategyInterface $hashStrategy)
    {
        // loop the config adding the key as a node
        foreach ($this->configList as $key => $value) {
            $weight = isset($value['weight']) ? $value['weight'] : 1;
            $hashStrategy->add($key, $weight);
        }

        $this->hashStrategy = $hashStrategy;

        return $this;
    }

    /**
     * Get many configs
     *
     * @param string $key
     * @param int $count
     * @return array
     */
    public function getManyConfigs($key, $count = 1)
    {
        // find a calculated config list
        if (!array_key_exists("$key.$count", $this->calculatedConfig)) {
            // check we aren't storing too many calculated configs
            if (count($this->calculatedConfig) >= 100) {
                // remove the fist in the list, should be the oldest
                array_shift($this->calculatedConfig);
            }

            // get a list of config keys using the count and key provided
            $configList = array();
            foreach ($this->hashStrategy->get($key, $count) as $index) {
                // append the config values to the config list
                $configList[] = $this->configList[$index];
            }

            // store for later, a little config cache
            $this->calculatedConfig["$key.$count"] = $configList;
        }

        return $this->calculatedConfig["$key.$count"];
    }

    /**
     * Get config
     *
     * @param string $key
     * @return array
     */
    public function getConfig($key)
    {
        // return the first matching config key
        $index = $this->hashStrategy->get($key, 1);

        return $this->configList[$index[0]];
    }

    /**
     * Get config list
     *
     * @return array
     */
    public function getConfigList()
    {
        return $this->configList;
    }
}

<?php

namespace Phlib\HashStrategy;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var array
     */
    protected $config;

    protected function setUp()
    {
        parent::setUp();

        $this->config = [
            0 => [
                'weight' => 2,
                'host' => 'local1',
                'port' => 123,
                'key1' => 'value1',
            ],
            1 => [
                'weight' => 1,
                'host' => 'local2',
                'port' => 456,
                'key1' => 'value2',
            ],
            2 => [
                'weight' => 3,
                'host' => 'local3',
                'port' => 789,
                'key1' => 'value3',
            ],
        ];
    }

    public function testgetManyConfigsLevelOne()
    {
        $hashStrategy = $this->createMock(Ordered::class);
        $hashStrategy->expects(static::exactly(3))
            ->method('add');

        $hashStrategy->expects(static::once())
            ->method('get')
            ->with('key1', 1)
            ->willReturn([0]);

        $poolConfig = new Config($this->config, $hashStrategy);

        $configList = $poolConfig->getManyConfigs('key1');
        static::assertEquals(1, count($configList));
        static::assertEquals($this->config[0], $configList[0]);
    }

    public function testgetManyConfigsLevelTwo()
    {
        $poolConfig = new Config($this->config);

        $configList = $poolConfig->getManyConfigs('key1', 2);

        static::assertEquals(2, count($configList));
        static::assertEquals($this->config[2], $configList[0]);
        static::assertEquals($this->config[0], $configList[1]);
    }

    public function testGetConfigList()
    {
        $poolConfig = new Config($this->config);
        $originalConfig = $poolConfig->getConfigList();
        static::assertEquals(count((array) $this->config), count($originalConfig));
        static::assertEquals($this->config, $originalConfig);
    }

    public function testGetConfig()
    {
        $poolConfig = new Config($this->config);
        static::assertEquals($this->config[2], $poolConfig->getConfig('key1'));
        static::assertEquals($this->config[2], $poolConfig->getConfig('key2a'));
    }

    public function testGetConfigWeighted()
    {
        $this->config[0]['weight'] = 1;
        $this->config[1]['weight'] = 0;
        $this->config[2]['weight'] = 0;
        $poolConfig = new Config($this->config);
        static::assertEquals($this->config[0], $poolConfig->getConfig('key1'));
    }

    public function testGetConfigMany()
    {
        $poolConfig = new Config($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertEquals(1, count($poolConfig->getManyConfigs(uniqid())));
        }
    }

    public function testGetConfigMany2()
    {
        $poolConfig = new Config($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertEquals(2, count($poolConfig->getManyConfigs(uniqid(), 2)));
        }
    }
}

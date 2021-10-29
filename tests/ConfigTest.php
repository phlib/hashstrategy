<?php

namespace Phlib\HashStrategy;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $config;

    public function setUp()
    {
        parent::setUp();

        $this->config = [
            0 => [
                'weight' => 2,
                'host'   => 'local1',
                'port'   => 123,
                'key1'   => 'value1'
            ],
            1 => [
                'weight' => 1,
                'host'   => 'local2',
                'port'   => 456,
                'key1'   => 'value2'
            ],
            2 => [
                'weight' => 3,
                'host'   => 'local3',
                'port'   => 789,
                'key1'   => 'value3'
            ]
        ];
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->config = null;
    }

    public function testgetManyConfigsLevelOne()
    {
        $hashStrategy = $this->getMock('\Phlib\HashStrategy\Ordered');
        $hashStrategy->expects($this->exactly(3))
            ->method('add');

        $hashStrategy->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('key1'),
                $this->equalTo(1)
            )
            ->will(
                $this->returnValue([0])
            );

        $poolConfig = new Config($this->config, $hashStrategy);

        $configList = $poolConfig->getManyConfigs('key1');
        $this->assertEquals(1, count($configList));
        $this->assertEquals($this->config[0], $configList[0]);
    }

    public function testgetManyConfigsLevelTwo()
    {
        $poolConfig = new Config($this->config);

        $configList = $poolConfig->getManyConfigs('key1', 2);

        $this->assertEquals(2, count($configList));
        $this->assertEquals($this->config[2], $configList[0]);
        $this->assertEquals($this->config[0], $configList[1]);
    }

    public function testGetConfigList()
    {
        $poolConfig = new Config($this->config);
        $originalConfig = $poolConfig->getConfigList();
        $this->assertEquals(count($this->config), count($originalConfig));
        $this->assertEquals($this->config, $originalConfig);
    }

    public function testGetConfig()
    {
        $poolConfig = new Config($this->config);
        $this->assertEquals($this->config[2], $poolConfig->getConfig('key1'));
        $this->assertEquals($this->config[2], $poolConfig->getConfig('key2a'));
    }

    public function testGetConfigWeighted()
    {
        $this->config[0]['weight'] = 1;
        $this->config[1]['weight'] = 0;
        $this->config[2]['weight'] = 0;
        $poolConfig = new Config($this->config);
        $this->assertEquals($this->config[0], $poolConfig->getConfig('key1'));
    }

    public function testGetConfigMany()
    {
        $poolConfig = new Config($this->config);

        $counter = 200;
        while($counter--) {
            $this->assertEquals(1, count($poolConfig->getManyConfigs(uniqid())));
        }
    }

    public function testGetConfigMany2()
    {
        $poolConfig = new Config($this->config);

        $counter = 200;
        while($counter--) {
            $this->assertEquals(2, count($poolConfig->getManyConfigs(uniqid(), 2)));
        }
    }
}

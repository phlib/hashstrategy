<?php

declare(strict_types=1);

namespace Phlib\HashStrategy;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var array
     */
    private $config;

    protected function setUp(): void
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

    public function testgetManyConfigsLevelOne(): void
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
        static::assertSame(1, count($configList));
        static::assertSame($this->config[0], $configList[0]);
    }

    public function testgetManyConfigsLevelTwo(): void
    {
        $poolConfig = new Config($this->config);

        $configList = $poolConfig->getManyConfigs('key1', 2);

        static::assertSame(2, count($configList));
        static::assertSame($this->config[2], $configList[0]);
        static::assertSame($this->config[0], $configList[1]);
    }

    public function testGetConfigList(): void
    {
        $poolConfig = new Config($this->config);
        $originalConfig = $poolConfig->getConfigList();
        static::assertSame(count((array) $this->config), count($originalConfig));
        static::assertSame($this->config, $originalConfig);
    }

    public function testGetConfig(): void
    {
        $poolConfig = new Config($this->config);
        static::assertSame($this->config[2], $poolConfig->getConfig('key1'));
        static::assertSame($this->config[2], $poolConfig->getConfig('key2a'));
    }

    public function testGetConfigWeighted(): void
    {
        $this->config[0]['weight'] = 1;
        $this->config[1]['weight'] = 0;
        $this->config[2]['weight'] = 0;
        $poolConfig = new Config($this->config);
        static::assertSame($this->config[0], $poolConfig->getConfig('key1'));
    }

    public function testGetConfigMany(): void
    {
        $poolConfig = new Config($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertSame(1, count($poolConfig->getManyConfigs(uniqid())));
        }
    }

    public function testGetConfigMany2(): void
    {
        $poolConfig = new Config($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertSame(2, count($poolConfig->getManyConfigs(uniqid(), 2)));
        }
    }
}

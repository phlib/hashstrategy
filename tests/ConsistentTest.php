<?php

declare(strict_types=1);

namespace Phlib\HashStrategy;

use PHPUnit\Framework\TestCase;

class ConsistentTest extends TestCase
{
    public function testAddReturn(): void
    {
        $hs = new Consistent();
        static::assertSame($hs, $hs->add('server1'));
    }

    public function testRemoveReturn(): void
    {
        $hs = new Consistent();
        static::assertSame($hs, $hs->remove('server1'));
    }

    public function testGetReturn(): void
    {
        $hs = new Consistent();
        static::assertSame([], $hs->get('key1'));
    }

    public function testGetWithData(): void
    {
        $hs = new Consistent();
        $hs->add('server1');
        static::assertSame(['server1'], $hs->get('key1'));
    }

    public function testGetWithDataTwo(): void
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        static::assertSame(['server1'], $hs->get('key1'));
        static::assertSame(['server1', 'server2'], $hs->get('key1', 2));
        static::assertSame(['server2', 'server1'], $hs->get('key2abc', 2));
    }

    public function testRemoveWithData(): void
    {
        $hs = new Consistent();
        $hs->add('server1');
        static::assertSame(['server1'], $hs->get('key1'));
        $hs->remove('server1');
        static::assertSame([], $hs->get('key1'));
    }

    public function testRemoveWithDataTwo(): void
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        static::assertSame(['server1', 'server2'], $hs->get('key1', 2));
        $hs->remove('server1');
        static::assertSame(['server2'], $hs->get('key1'));
    }

    public function testGetWithDataMax(): void
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        static::assertSame(3, count($hs->get('key1', 10)));
    }

    public function testGetWithRandData(): void
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        $count = 200;
        while ($count--) {
            static::assertSame(2, count($hs->get(uniqid(), 2)));
        }
    }

    public function testGetWithRandDataOther(): void
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        $count = 200;
        while ($count--) {
            static::assertSame(3, count($hs->get(uniqid(), 10)));
        }
    }

    public function testGetWeight(): void
    {
        $hs = new Consistent();
        $hs->add('server1', 1);
        $hs->add('server2', 10);
        $hs->add('server3', 1);

        static::assertSame(['server2'], $hs->get('key1'));
    }

    public function testGetWeightChange(): void
    {
        $hs = new Consistent();
        $hs->add('server1', 1);
        $hs->add('server2', 10);
        $hs->add('server3', 1);

        static::assertSame(['server2'], $hs->get('key1'));

        $hs->add('server4', 100);
        static::assertSame(['server4'], $hs->get('key1'));
    }

    public function testInvalidHashType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Consistent('none');
    }

    public function testHashTypeCrc32(): void
    {
        $hs = new Consistent('crc32');
        $hs->add('server1', 1);
        $hs->add('server2', 1);
        $hs->add('server3', 1);

        static::assertSame(['server1'], $hs->get('key1'));
    }

    public function testHashTypeMd5(): void
    {
        $hs = new Consistent('md5');
        $hs->add('server1', 1);
        $hs->add('server2', 1);
        $hs->add('server3', 1);

        static::assertSame(['server3'], $hs->get('key1'));
    }
}

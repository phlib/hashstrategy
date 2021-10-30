<?php

declare(strict_types=1);

namespace Phlib\HashStrategy;

use PHPUnit\Framework\TestCase;

class OrderedTest extends TestCase
{
    public function testAddReturn(): void
    {
        $hs = new Ordered();
        static::assertSame($hs, $hs->add('server1'));
    }

    public function testRemoveReturn(): void
    {
        $hs = new Ordered();
        static::assertSame($hs, $hs->remove('server1'));
    }

    public function testGetReturn(): void
    {
        $hs = new Ordered();
        static::assertSame([], $hs->get('key1'));
    }

    public function testGetWithData(): void
    {
        $hs = new Ordered();
        $hs->add('server1');
        static::assertSame(['server1'], $hs->get('key1'));
    }

    public function testRemoveWithData(): void
    {
        $hs = new Ordered();
        $hs->add('server1');
        static::assertSame(['server1'], $hs->get('key1'));
        $hs->remove('server1');
        static::assertSame([], $hs->get('key1'));
    }

    public function testRemoveWithDataTwo(): void
    {
        $hs = new Ordered();
        $hs->add('server1');
        $hs->add('server2');

        static::assertCount(2, $hs->get('key1', 2));
        $hs->remove('server1');
        static::assertSame(['server2'], $hs->get('key1'));
    }

    public function testGetWithDataMax(): void
    {
        $hs = new Ordered();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        static::assertSame(3, count($hs->get('key1', 10)));
    }

    public function testGetWeight(): void
    {
        $hs = new Ordered();
        $hs->add('server1', 1);
        $hs->add('server2', 2);
        $hs->add('server2a', 2);
        $hs->add('server3', 3);

        static::assertSame(['server3'], $hs->get('test1'));
        static::assertSame(['server3'], $hs->get('test2'));
        static::assertSame(['server3', 'server2'], $hs->get('test2', 2));
        static::assertSame(['server3', 'server2', 'server2a'], $hs->get('test2', 3));
        static::assertSame(['server3', 'server2', 'server2a', 'server1'], $hs->get('test2', 100));
    }

    public function testGetWeightChange(): void
    {
        $hs = new Ordered();
        $hs->add('server1', 0);
        $hs->add('server2', 0);
        $hs->add('server3', 1);

        static::assertSame(['server3'], $hs->get('key1'));

        $hs->remove('server3');
        $hs->add('server3a', 1);
        static::assertSame(['server3a'], $hs->get('key1'));

        $hs->add('server4', 10);
        static::assertSame(['server4'], $hs->get('key1'));
        static::assertSame(['server4', 'server3a'], $hs->get('key1', 2));
    }
}

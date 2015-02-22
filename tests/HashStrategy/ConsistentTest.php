<?php

namespace Phlib\HashStrategy\Test;

use Phlib\HashStrategy\Consistent;

class ConsistentTest extends \PHPUnit_Framework_TestCase
{

    public function testAddReturn()
    {
        $hs = new Consistent();
        $this->assertEquals($hs, $hs->add('server1'));
    }

    public function testRemoveReturn()
    {
        $hs = new Consistent();
        $this->assertEquals($hs, $hs->remove('server1'));
    }

    public function testGetReturn()
    {
        $hs = new Consistent();
        $this->assertEquals(array(), $hs->get('key1'));
    }

    public function testGetWithData()
    {
        $hs = new Consistent();
        $hs->add('server1');
        $this->assertEquals(array('server1'), $hs->get('key1'));
    }

    public function testGetWithDataTwo()
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $this->assertEquals(array('server1'), $hs->get('key1'));
        $this->assertEquals(array('server1', 'server2'), $hs->get('key1', 2));
        $this->assertEquals(array('server2', 'server1'), $hs->get('key2abc', 2));
    }

    public function testRemoveWithData()
    {
        $hs = new Consistent();
        $hs->add('server1');
        $this->assertEquals(array('server1'), $hs->get('key1'));
        $hs->remove('server1');
        $this->assertEquals(array(), $hs->get('key1'));
    }

    public function testRemoveWithDataTwo()
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $this->assertEquals(array('server1', 'server2'), $hs->get('key1', 2));
        $hs->remove('server1');
        $this->assertEquals(array('server2'), $hs->get('key1'));
    }

    public function testGetWithDataMax()
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        $this->assertEquals(3, count($hs->get('key1', 10)));
    }

    public function testGetWithRandData()
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        $count = 200;
        while($count--) {
            $this->assertEquals(2, count($hs->get(uniqid(), 2)));
        }
    }

    public function testGetWithRandDataOther()
    {
        $hs = new Consistent();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        $count = 200;
        while($count--) {
            $this->assertEquals(3, count($hs->get(uniqid(), 10)));
        }
    }

    public function testGetWeight()
    {
        $hs = new Consistent();
        $hs->add('server1', 1);
        $hs->add('server2', 10);
        $hs->add('server3', 1);

        $this->assertEquals(array('server2'), $hs->get('key1'));
    }

    public function testGetWeightChange()
    {
        $hs = new Consistent();
        $hs->add('server1', 1);
        $hs->add('server2', 10);
        $hs->add('server3', 1);

        $this->assertEquals(array('server2'), $hs->get('key1'));

        $hs->add('server4', 100);
        $this->assertEquals(array('server4'), $hs->get('key1'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidHashType()
    {
        new Consistent('none');
    }

    public function testHashTypeCrc32()
    {
        $hs = new Consistent('crc32');
        $hs->add('server1', 1);
        $hs->add('server2', 1);
        $hs->add('server3', 1);

        $this->assertEquals(array('server1'), $hs->get('key1'));
    }

    public function testHashTypeMd5()
    {
        $hs = new Consistent('md5');
        $hs->add('server1', 1);
        $hs->add('server2', 1);
        $hs->add('server3', 1);

        $this->assertEquals(array('server3'), $hs->get('key1'));
    }
}

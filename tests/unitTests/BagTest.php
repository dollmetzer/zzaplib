<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use PHPUnit\Framework\TestCase;
use dollmetzer\zzaplib\Bag;
use dollmetzer\zzaplib\exception\BagException;

class BagTest extends TestCase
{

    /**
     * Execute once on class test start
     */
    public static function setUpBeforeClass()
    {

        echo "\nStart " . __CLASS__ . "\n";

    }

    /**
     * Execute once after class test finish
     */
    public static function tearDownAfterClass()
    {

        echo "\n";

    }

    /**
     * Execute before test method start
     */
    public function setUp()
    {

    }

    /**
     * Execute after test method finish
     */
    public function tearDown()
    {

    }

    public function testConstruct()
    {

        $class = new Bag();
        $this->assertInstanceOf(Bag::class, $class);

    }

    public function testSetNoObject()
    {

        $this->expectException(BagException::class);
        $class = new Bag();
        $class->set('testObject', 'invalidType');

    }

    public function testSetSuccess()
    {

        $class = new Bag();
        $class->set('testObject', new Bag());
        $this->assertInstanceOf(Bag::class, $class->get('testObject'));

    }

    public function testGetFailed()
    {

        $this->expectException(BagException::class);
        $class = new Bag();
        $this->assertEquals(false, $class->get('unknownObject'));

    }

    public function testHasNot()
    {

        $class = new Bag();
        $this->assertEquals(false, $class->has('unknownObject'));

    }

    public function testHas()
    {

        $class = new Bag();
        $class->set('testObject', new Bag());
        $this->assertEquals(true, $class->has('testObject'));

    }

}
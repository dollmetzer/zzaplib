<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\exception\ApplicationException;
use dollmetzer\zzaplib\session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * Execute once on class test start
     */
    public static function setUpBeforeClass()
    {
        echo "Start " . __CLASS__ . "\n";
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
        $_SESSION = [];
    }

    /**
     * Execute after test method finish
     */
    public function tearDown()
    {
    }

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\session\Session');
        $this->assertEquals(1, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Session($config);
        $this->assertInstanceOf(Session::class, $class);
    }

    public function testInit()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Session($config);
        $this->assertEquals(1, $class->get('sessionHits'));
        $this->assertEquals(0, $class->get('userId'));
        $this->assertEquals('guest', $class->get('userHandle'));
        $this->assertEquals(['guest'], $class->get('userGroups'));
    }

    public function testGetUnknown()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Session($config);
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage(Session::UNDEFINED_EXCEPTION_MESSAGE);
        $this->assertEquals('moo', $class->get('myattribute'));
    }

    public function testSetAndGet()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Session($config);
        $class->set('myattribute', 'moo');
        $this->assertEquals('moo', $class->get('myattribute'));
    }

    public function testUnset()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Session($config);
        $class->delete('myattribute');

        $class->set('myattribute', 'moo');
        $this->assertEquals('moo', $class->get('myattribute'));

        $class->delete('myattribute');
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage(Session::UNDEFINED_EXCEPTION_MESSAGE);
        $this->assertEquals('moo', $class->get('myattribute'));
    }
}
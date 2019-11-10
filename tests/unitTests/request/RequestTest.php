<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\request\Request;
use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\router\Router;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Router
     */
    protected $router;

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
        $configFile = realpath('./tests/data/testConfig.php');
        $this->config = new Config($configFile);
        $this->router = new Router($this->config);
    }

    /**
     * Execute after test method finish
     */
    public function tearDown()
    {
    }

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\request\Request');
        $this->assertEquals(2, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $class = new Request($this->config, $this->router);
        $this->assertInstanceOf(Request::class, $class);
    }

    public function testGetModule() {
        $class = new Request($this->config, $this->router);
        $this->assertEquals('index', $class->getModule());
    }

    public function testGetController() {
        $class = new Request($this->config, $this->router);
        $this->assertEquals('index', $class->getController());
    }

    public function testGetAction() {
        $class = new Request($this->config, $this->router);
        $this->assertEquals('index', $class->getAction());
    }

    public function testGetParams() {
        $class = new Request($this->config, $this->router);
        $this->assertEquals([], $class->getParams());
    }

    public function testGetQueryString() {
        $class = new Request($this->config, $this->router);
        $this->assertEquals('', $class->getQueryString());
    }
}
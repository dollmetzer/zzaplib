<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

namespace unitTests\view;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\logger\Logger;
use dollmetzer\zzaplib\router\Router;
use dollmetzer\zzaplib\translator\Translator;
use dollmetzer\zzaplib\view\Viewhelper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ViewhelperTest extends TestCase
{
    /**
     * @var Viewhelper
     */
    private $viewhelper;

    /**
     * Execute once on class test start
     */
    public static function setUpBeforeClass(): void
    {
        echo "Start " . __CLASS__ . "\n";
    }

    /**
     * Execute once after class test finish
     */
    public static function tearDownAfterClass(): void
    {
        echo "\n";
    }

    /**
     * Execute before test method start
     */
    public function setUp(): void
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $logger = new Logger($config);
        $router = new Router();
        $translate = new Translator($config, $logger);
        $this->viewhelper = new Viewhelper($config, $router, $translate);
    }

    /**
     * Execute after test method finish
     */
    public function tearDown(): void
    {
    }

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\view\Viewhelper');
        $this->assertEquals(3, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Viewhelper::class, $this->viewhelper);
    }

    public function testBuildURL()
    {
        $this->assertEquals('http://testserver/index.php?q=this/is/a/test', $this->viewhelper->buildURL('this/is/a/test', false));
    }

    public function testBuildMediaURL()
    {
        $this->assertEquals('http://testserver/img/test.jpg', $this->viewhelper->buildMediaURL('img/test.jpg', false));
    }

    public function testTranslate()
    {
        $this->assertEquals('###_TEST-SNIPPET_###', $this->viewhelper->translate('test-snippet', false));
    }
}
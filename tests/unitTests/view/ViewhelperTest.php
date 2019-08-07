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
use dollmetzer\zzaplib\router\Router;
use dollmetzer\zzaplib\response\Response;
use dollmetzer\zzaplib\session\Session;
use dollmetzer\zzaplib\translator\Translator;
use dollmetzer\zzaplib\view\Viewhelper;
use PHPUnit\Framework\TestCase;

class ViewhelperTest extends TestCase
{
    /**
     * @var Viewhelper
     */
    private $viewhelper;

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
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $router = new Router();
        $session = new Session($config);
        $response = new Response($config, $session);
        $translate = new Translator($config);
        $this->viewhelper = new Viewhelper($config, $router, $translate);
    }

    /**
     * Execute after test method finish
     */
    public function tearDown()
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
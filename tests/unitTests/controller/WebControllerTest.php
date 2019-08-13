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
use dollmetzer\zzaplib\controller\WebController;
use dollmetzer\zzaplib\request\Request;
use dollmetzer\zzaplib\response\Response;
use dollmetzer\zzaplib\router\Router;
use dollmetzer\zzaplib\session\Session;
use dollmetzer\zzaplib\translator\Translator;
use dollmetzer\zzaplib\logger\Logger;
use PHPUnit\Framework\TestCase;

class WebControllerTest extends TestCase
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

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\controller\WebController');
        $this->assertEquals(7, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $logger = new Logger($config);
        $session = new Session($config);
        $router = new Router($config);
        $request = new Request($config, $router);
        $response = new Response($config, $session);
        $translate = new Translator($config, $logger);
        $view = new dollmetzer\zzaplib\view\View($config, $router, $request,$response, $session, $translate);
        $class = new WebController($config, $router, $request, $response, $session, $translate, $view);
        $this->assertInstanceOf(WebController::class, $class);
    }
}
<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\router\Router;
use PHPUnit\Framework\TestCase;

class PathRouterTest extends TestCase
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
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\router\Router');
        $this->assertEquals(0, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {

        $class = new Router();
        $this->assertInstanceOf(Router::class, $class);

    }

    public function testResolve()
    {
        $queryString = 'this/is/a/simple/test';
        $_GET['q'] = $queryString;
        $class = new Router();
        $class->resolve();
        $this->assertEquals($queryString, $class->getQueryString());
        $this->assertEquals('this', $class->getModule());
        $this->assertEquals('is', $class->getController());
        $this->assertEquals('a', $class->getAction());
        $this->assertEquals(['simple','test'], $class->getParams());
    }

    public function testBuildURL()
    {
        $class = new Router();
        $this->assertEquals('http://testserver/index.php?q=this/is/a/test', $class->buildURL('this/is/a/test'));
    }

    public function testBuildMediaURL()
    {
        $class = new Router();
        $this->assertEquals('http://testserver/img/profile/2265d_128.jpg', $class->buildMediaURL('img/profile/2265d_128.jpg'));
    }

}

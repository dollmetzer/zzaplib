<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\response\Response;
use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\session\Session;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    protected $session;

    protected $response;

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
        $this->session = new Session($config);
        $this->response = new Response($config, $this->session);

    }

    /**
     * Execute after test method finish
     */
    public function tearDown()
    {

    }

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\response\Response');
        $this->assertEquals(2, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function testRedirect()
    {
        $this->response->redirect('http://www.spiegel.de', 'this is a fake redirect', $this->response::MESSAGE_TYPE_NOTIFICATION);
        $this->assertEquals('this is a fake redirect', $this->session->get('flashMessage'));
        $this->assertEquals($this->response::MESSAGE_TYPE_NOTIFICATION, $this->session->get('flashMessageType'));
    }
}
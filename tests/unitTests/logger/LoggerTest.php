<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

namespace unitTests\logger;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\logger\Logger;
use dollmetzer\zzaplib\Bag;
use \PHPUnit\Framework\TestCase;
use ReflectionClass;

class LoggerTest extends TestCase
{
    /**
     * @var Bag $bag
     */
    protected $bag;

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
        $this->bag = new Bag();
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $this->bag->set('config', $config);
    }

    /**
     * Execute after test method finish
     */
    public function tearDown(): void
    {
    }

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\logger\Logger');
        $this->assertEquals(1, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $this->assertInstanceOf(Logger::class, $class);

    }

    public function testLevelEmergency()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->emergency('This is an error message');
        $this->assertStringEndsWith('[EMERGENCY] This is an error message', $class->getMessage());
    }

    public function testLevelAlert()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->alert('This is an error message');
        $this->assertStringEndsWith('[ALERT] This is an error message', $class->getMessage());
    }

    public function testLevelCritical()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->critical('This is an error message');
        $this->assertStringEndsWith('[CRITICAL] This is an error message', $class->getMessage());
    }

    public function testLevelError()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->error('This is an error message');
        $this->assertStringEndsWith('[ERROR] This is an error message', $class->getMessage());
    }

    public function testLevelWarning()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->warning('This is an error message');
        $this->assertStringEndsWith('[WARNING] This is an error message', $class->getMessage());
    }

    public function testLevelNotice()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->notice('This is an error message');
        $this->assertStringEndsWith('[NOTICE] This is an error message', $class->getMessage());
    }

    public function testLevelInfo()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->info('This is an error message');
        $this->assertStringEndsWith('[INFO] This is an error message', $class->getMessage());
    }

    public function testLevelDebug()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->debug('This is an error message');
        $this->assertStringEndsWith('[DEBUG] This is an error message', $class->getMessage());
    }

    public function testContext()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Logger($config);
        $class->debug('This is an error message', ['contextKey' => 'contextValue']);
        $this->assertStringEndsWith('Array (     [contextKey] => contextValue ) ', $class->getMessage());
    }

}
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
use dollmetzer\zzaplib\logger\Logger;
use dollmetzer\zzaplib\model\DbModel;
use dollmetzer\zzaplib\exception\ApplicationException;
use PHPUnit\Framework\TestCase;

class DbModelTest extends TestCase
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
    }

    /**
     * Execute after test method finish
     */
    public function tearDown()
    {
    }

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\model\DbModel');
        $this->assertEquals(2, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstructFailedDsn()
    {
        $configFile = realpath('./tests/data/wrongConfig.php');
        $config = new Config($configFile);
        $logger = new Logger($config);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage(DbModel::ERROR_CONFIG_MISSING_DSN);
        $class = new DbModel($config, $logger);
    }

    public function testConstructFailedTablename()
    {
        $configFile = realpath('./tests/data/wrongConfig.php');
        $config = new Config($configFile);
        $logger = new Logger($config);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage(DbModel::ERROR_CONFIG_MISSING_DSN);
        $class = new DbModel($config, $logger);
    }
}
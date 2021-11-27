<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

namespace unitTests\translator;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\translator\Translator;
use dollmetzer\zzaplib\logger\Logger;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TranslatorTest extends TestCase
{
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
    }

    /**
     * Execute after test method finish
     */
    public function tearDown(): void
    {
    }

    public function testConstructorParameters()
    {
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\translator\Translator');
        $this->assertEquals(2, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $logger = new Logger($config);
        $class = new Translator($config, $logger);
        $this->assertInstanceOf(Translator::class, $class);
    }

    public function testImportLanguageFail()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $logger = new Logger($config);
        $class = new Translator($config, $logger);
        $this->assertFalse($class->importLanguage('de'));
    }

    public function testImportLanguageSuccess()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $logger = new Logger($config);
        $class = new Translator($config, $logger);
        $this->assertTrue($class->importLanguage('en'));
        $this->assertEquals('Homepage', $class->translate('link_home'));
        $this->assertEquals('###_UNDEFINED_SNIPPET_###', $class->translate('undefined_snippet'));
    }

}
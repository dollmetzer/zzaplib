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
use dollmetzer\zzaplib\translator\Translator;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
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
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\translator\Translator');
        $this->assertEquals(1, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Translator($config);
        $this->assertInstanceOf(Translator::class, $class);
    }

    public function testImportLanguageFail()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Translator($config);
        $this->assertFalse($class->importLanguage('de'));
    }

    public function testImportLanguageSuccess()
    {
        $configFile = realpath('./tests/data/testConfig.php');
        $config = new Config($configFile);
        $class = new Translator($config);
        $this->assertTrue($class->importLanguage('en'));
        $this->assertEquals('Homepage', $class->translate('link_home'));
        $this->assertEquals('###_UNDEFINED_SNIPPET_###', $class->translate('undefined_snippet'));
    }

}
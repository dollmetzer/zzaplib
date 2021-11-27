<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

namespace unitTests;

use dollmetzer\zzaplib\Command;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
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

    public function testConstruct()
    {
        $class = new Command();
        $this->assertInstanceOf(Command::class, $class);
    }
}
<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\data\Pagination;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
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
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\data\Pagination');
        $this->assertEquals(3, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $pagination = new Pagination();
        $this->assertInstanceOf(Pagination::class, $pagination);
    }

    public function testNewPagination()
    {
        $pagination = new Pagination();
        $this->assertEquals(10, $pagination->getPageLength());
        $this->assertEquals(0, $pagination->getPage());
        $this->assertEquals(0, $pagination->getPageMax());
        $this->assertEquals(4, $pagination->getDisplayWidth());
        $this->assertEquals(0, $pagination->getDisplayFirst());
        $this->assertEquals(0, $pagination->getDisplayLast());
    }

    public function test53Entries()
    {
        $pagination = new Pagination(2);
        $pagination->calculate(53);
        $this->assertEquals(10, $pagination->getPageLength());
        $this->assertEquals(2, $pagination->getPage());
        $this->assertEquals(6, $pagination->getPageMax());
        $this->assertEquals(4, $pagination->getDisplayWidth());
        $this->assertEquals(0, $pagination->getDisplayFirst());
        $this->assertEquals(6, $pagination->getDisplayLast());
    }

    public function testSetAll()
    {
        $pagination = new Pagination();
        $this->assertEquals(10, $pagination->getPageLength());
        $this->assertEquals(0, $pagination->getPage());
        $this->assertEquals(0, $pagination->getPageMax());
        $this->assertEquals(4, $pagination->getDisplayWidth());
        $this->assertEquals(0, $pagination->getDisplayFirst());
        $this->assertEquals(0, $pagination->getDisplayLast());

        $pagination->setPage(2);
        $pagination->setPageLength(15);
        $pagination->setDisplayWidth(5);
        $pagination->calculate(153);

        $this->assertEquals(15, $pagination->getPageLength());
        $this->assertEquals(2, $pagination->getPage());
        $this->assertEquals(11, $pagination->getPageMax());
        $this->assertEquals(5, $pagination->getDisplayWidth());
        $this->assertEquals(0, $pagination->getDisplayFirst());
        $this->assertEquals(7, $pagination->getDisplayLast());

    }
}
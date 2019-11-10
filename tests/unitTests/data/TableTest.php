<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\data\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
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
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\data\Table');
        $this->assertEquals(0, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {
        $class = new Table();
        $this->assertInstanceOf(Table::class, $class);
    }

    public function testTitle()
    {
        $title = "Test Title";
        $class = new Table();
        $class->setTitle($title);
        $this->assertEquals($title, $class->getTitle());
    }

    public function testDetails()
    {
        $description = 'This is a test description';
        $class = new Table();
        $class->setDescription($description);
        $this->assertEquals($description, $class->getDescription());
    }

    public function testColumns()
    {
        $columns = $this->getColumns();

        $expected = [
            'id' => [
                'type' => 'id',
                'width' => '',
                'sortable' => false
            ],
            'name' => [
                'type' => 'text',
                'width' => '50%',
                'sortable' => true
            ],
            'start' => [
                'type' => 'datetime',
                'width' => '',
                'sortable' => true
            ]
        ];
        $class = new Table();
        $class->setColumns($columns);
        $this->assertEquals($expected, $class->getColumns());
    }

    public function testRowsFail() {
        $columns = $this->getColumns();

        $rows = [
            [
                'id' => 1,
                'name' => 'One',
                'start' => '2019-03-21 12:01:34'
            ],
            [
                'id' => 2,
                'name' => 'Two',
                'start' => '2019-07-22 21:02:56'
            ],
            [
                'id' => 3,
                'owner' => 'Karl May',
                'start' => '2017-12-23 02:33:01'
            ],
        ];

        $class = new Table();
        $class->setRows($rows);
        $this->assertNull($class->getRows());
    }

    public function testRows() {
        $columns = $this->getColumns();

        $rows = [
            [
                'id' => 1,
                'name' => 'One',
                'start' => '2019-03-21 12:01:34'
            ],
            [
                'id' => 2,
                'name' => 'Two',
                'start' => '2019-07-22 21:02:56'
            ],
            [
                'id' => 3,
                'owner' => 'Karl May',
                'start' => '2017-12-23 02:33:01'
            ],
        ];

        $expected = [
            [
                'id' => 1,
                'name' => 'One',
                'start' => '2019-03-21 12:01:34'
            ],
            [
                'id' => 2,
                'name' => 'Two',
                'start' => '2019-07-22 21:02:56'
            ],
            [
                'id' => 3,
                'start' => '2017-12-23 02:33:01'
            ],
        ];

        $class = new Table();
        $class->setColumns($columns);
        $class->setRows($rows);
        $this->assertEquals($expected, $class->getRows());
    }

    private function getColumns()
    {
        return [
            'id' => [
                'type' => 'id',
            ],
            'name' => [
                'width' => '50%',
                'sortable' => true
            ],
            'start' => [
                'type' => 'datetime',
                'sortable' => true
            ]
        ];
    }
}
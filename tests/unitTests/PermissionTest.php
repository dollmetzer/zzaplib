<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\Permission;
use dollmetzer\zzaplib\exception\ValidationException;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
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
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\Permission');
        $this->assertEquals(3, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstructorValidation1()
    {
        $this->expectException(ValidationException::class);
        $class = new Permission(-1, 0, 31);
    }

    public function testConstructorValidation2()
    {
        $this->expectException(ValidationException::class);
        $class = new Permission(0, -1, 31);
    }

    public function testConstructorValidation3()
    {
        $this->expectException(ValidationException::class);
        $class = new Permission(0, 0, 64536);
    }

    public function testGetOwnerId()
    {
        $class = new Permission();
        $this->assertEquals(0, $class->getOwnerId());
    }

    public function testGetGroupId()
    {
        $class = new Permission();
        $this->assertEquals(0, $class->getGroupId());
    }

    public function testStandardPermissions()
    {
        $class = new Permission();
        $this->assertFalse($class->canRead());
        $this->assertFalse($class->canWrite());
        $this->assertFalse($class->canExecute());
    }

    public function testOwnerPermission()
    {
        $class = new Permission(12, 0, 0b111000000);
        $this->assertEquals(448, $class->getPermissions());

        $this->assertFalse($class->canRead());
        $this->assertFalse($class->canRead(999));
        $this->assertTrue($class->canRead(12));
        $this->assertFalse($class->canWrite());
        $this->assertFalse($class->canWrite(999));
        $this->assertTrue($class->canWrite(12));
        $this->assertFalse($class->canExecute());
        $this->assertFalse($class->canExecute(999));
        $this->assertTrue($class->canExecute(12));
    }

    public function testGroupPermission()
    {
        $class = new Permission(0, 35, 0b000111000);
        $this->assertEquals(56, $class->getPermissions());

        $this->assertFalse($class->canRead());
        $this->assertFalse($class->canRead(0, [999]));
        $this->assertTrue($class->canRead(0, [35]));
        $this->assertFalse($class->canWrite());
        $this->assertFalse($class->canWrite(0, [999]));
        $this->assertTrue($class->canWrite(0, [35]));
        $this->assertFalse($class->canExecute());
        $this->assertFalse($class->canExecute(0, [999]));
        $this->assertTrue($class->canExecute(0, [35]));
    }

    public function testAllPermission()
    {
        $class = new Permission(0, 0, 0b000000111);
        $this->assertEquals(7, $class->getPermissions());

        $this->assertTrue($class->canRead());
        $this->assertTrue($class->canRead(0, [999]));
        $this->assertTrue($class->canRead(22, [35]));
        $this->assertTrue($class->canWrite());
        $this->assertTrue($class->canWrite(0, [999]));
        $this->assertTrue($class->canWrite(22, [35]));
        $this->assertTrue($class->canExecute());
        $this->assertTrue($class->canExecute(0, [999]));
        $this->assertTrue($class->canExecute(22, [35]));
    }
}
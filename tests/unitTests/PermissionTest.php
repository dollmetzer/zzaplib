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
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\Permission');
        $this->assertEquals(3, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {

        $class = new Permission();
        $this->assertInstanceOf(Permission::class, $class);

    }

    public function testConstructorValidation1()
    {
        $this->expectException(ValidationException::class);
        $class = new Permission(-1, 0,31);
    }


    public function testConstructorValidation2()
    {
        $this->expectException(ValidationException::class);
        $class = new Permission(0, -1,31);
    }

    public function testConstructorValidation3()
    {
        $this->expectException(ValidationException::class);
        $class = new Permission(0, 0,64536);
    }

    public function testStandardPermissionCreate()
    {
        $class = new Permission();
        $this->assertTrue($class->isCreateAllowed());
    }

    public function testStandardPermissionRead()
    {
        $class = new Permission();
        $this->assertTrue($class->isReadAllowed());
    }

    public function testStandardPermissionUpdate()
    {
        $class = new Permission();
        $this->assertTrue($class->isUpdateAllowed());
    }

    public function testStandardPermissionDelete()
    {
        $class = new Permission();
        $this->assertTrue($class->isDeleteAllowed());
    }

    public function testStandardPermissionExecute()
    {
        $class = new Permission();
        $this->assertTrue($class->isExecuteAllowed());
    }


    public function testGroupPermissionCreate()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [2,4,17]);
        $this->assertTrue($class->isCreateAllowed());
    }

    public function testGroupPermissionRead()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [2,4,17]);
        $this->assertTrue($class->isReadAllowed());
    }

    public function testGroupPermissionUpdate()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [2,4,17]);
        $this->assertTrue($class->isUpdateAllowed());
    }

    public function testGroupPermissionDelete()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [2,4,17]);
        $this->assertTrue($class->isDeleteAllowed());
    }

    public function testGroupPermissionExecute()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [2,4,17]);
        $this->assertTrue($class->isExecuteAllowed());
    }


    public function testGroupPermissionCreateFail()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [3,9,27]);
        $this->assertFalse($class->isCreateAllowed());
    }

    public function testGroupPermissionReadFail()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [3,9,27]);
        $this->assertFalse($class->isReadAllowed());
    }

    public function testGroupPermissionUpdateFail()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [3,9,27]);
        $this->assertFalse($class->isUpdateAllowed());
    }

    public function testGroupPermissionDeleteFail()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [3,9,27]);
        $this->assertFalse($class->isDeleteAllowed());
    }

    public function testGroupPermissionExecuteFail()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_GROUP);
        $class->setUser(54, [3,9,27]);
        $this->assertFalse($class->isExecuteAllowed());
    }


    public function testAllPermissionCreate()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_ALL);
        $class->setUser(54, [999]);
        $this->assertTrue($class->isCreateAllowed());
    }

    public function testAllPermissionRead()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_ALL);
        $class->setUser(54, [999]);
        $this->assertTrue($class->isReadAllowed());
    }

    public function testAllPermissionUpdate()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_ALL);
        $class->setUser(54, [999]);
        $this->assertTrue($class->isUpdateAllowed());
    }

    public function testAllPermissionDelete()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_ALL);
        $class->setUser(54, [999]);
        $this->assertTrue($class->isDeleteAllowed());
    }

    public function testAllPermissionExecute()
    {
        $class = new Permission(999, 2, 31*Permission::MULTIPLY_ALL);
        $class->setUser(54, [999]);
        $this->assertTrue($class->isExecuteAllowed());
    }

}
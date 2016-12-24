<?php
/**
 * z z a p l i b   m i n i   f r a m e w o r k
 * ===========================================
 *
 * This library is a mini framework from php web applications
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 3 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 */

use dollmetzer\zzaplib\Base;

/**
 * Class BaseTest
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class BaseTest extends PHPUnit_Framework_TestCase
{

    protected static $config;

    /**
     * Execute once on class test start
     */
    public static function setUpBeforeClass()
    {

        echo "\nStart " . __CLASS__ . "\n";

        $configFile = realpath(__DIR__ . '/app/config.ini');
        $config = parse_ini_file($configFile, true);
        self::$config = $config;

        define('PATH_APP', realpath(__DIR__ . '/app') . '/');
        define('PATH_LOGS', realpath(__DIR__ . '/logs') . '/');

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

    /**
     * Test, if class can be contructed
     */
    public function testConstruct()
    {

        $base = new Base(self::$config);
        $this->assertInstanceOf(Base::class, $base);

    }

    /**
     * Test,module list from the configuration
     */
    public function testGetModuleListFromConfig()
    {

        // test module list in file system
        $list = array(
            'that',
            'this'
        );

        $base = new Base(self::$config);
        $moduleList = $base->getModuleList();
        $this->assertSame($moduleList, $list);

    }


    /**
     * Test, extraction of module list from filesystem
     */
    public function testGetModuleListFromFilesystem()
    {

        // test module list in file system
        $list = array(
            'core',
            'foo'
        );

        $config = array();

        $base = new Base($config);
        $moduleList = $base->getModuleList();
        $this->assertSame($moduleList, $list);

    }

    // todo: test also empty - neither in config, nor in filesystem

    /**
     * Test, get a controller list from the configuration
     */
    public function testGetControllerListFromConfig()
    {

        // test module list in file system
        $list = array(
            'index'
        );

        $base = new Base(self::$config);
        $controllerList = $base->getControllerList('that');
        $this->assertSame($controllerList, $list);

    }

    /**
     * Test, extract a controller list from the filesystem
     */
    public function testGetControllerListFromFilesystem()
    {

        // test module list in file system
        $list = array(
            'bar'
        );

        $config = array();

        $base = new Base(self::$config);
        $controllerList = $base->getControllerList('foo');
        $this->assertSame($controllerList, $list);

    }

    // todo: test also empty - neither in config, nor in filesystem

    public function testLoadLanguageDefault()
    {

        $base = new Base(self::$config);
        $this->assertTrue($base->loadLanguage());

    }

}

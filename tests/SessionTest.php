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

use dollmetzer\zzaplib\Session;

/**
 * Class SessionTest
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class SessionTest extends PHPUnit_Framework_TestCase
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

        $session = new Session(self::$config);
        $this->assertInstanceOf(Session::class, $session);

    }

    /**
     * Test, if user language after init is first in config file
     */
    public function testInitValues()
    {

        $session = new Session(self::$config);
        $this->assertSame($session->user_language, 'de');

    }

    /**
     * Test, if session is detected as none-web-session
     */
    public function testIsWebSession()
    {

        $session = new Session(self::$config);
        $this->assertSame($session->isWeb(), false);

    }

    /**
     * Test, if magic setter and getter are working properly
     */
    public function testSetGet()
    {

        $session = new Session(self::$config);
        $session->user_language = 'en';
        $this->assertSame($session->user_language, 'en');

    }

    /**
     * Test, if session destroy resets main values
     */
    public function testDestroySession() {

        $session = new Session(self::$config);
        $session->user_language = 'en';
        $session->destroy();
        $this->assertSame($session->user_language, 'de');

    }

    // some more tests, if destroy really cleans up everything

}

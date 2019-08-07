<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 */

use dollmetzer\zzaplib\data\Form;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    const NAME='login';
    const TITLE = 'Anmeldung';
    const ACTION = 'https://example.com/account/login';
    const CSRF_TOKEN = 'a098fcc11735fe693947bba5c4c3c821';
    const HANDLE = 'testuser';
    const PASSWORD = 'ThisIsSecret';

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
        $reflectionClass = new ReflectionClass('dollmetzer\zzaplib\data\Form');
        $this->assertEquals(0, $reflectionClass->getConstructor()->getNumberOfParameters());
    }

    public function testConstruct()
    {

        $class = new Form();
        $this->assertInstanceOf(Form::class, $class);

    }

    public function testSetters()
    {
        $fields = $this->getLoginForm();
        $form = new Form();
        $form->setName(self::NAME);
        $form->setTitle(self::TITLE);
        $form->setAction(self::ACTION);
        $form->setFields($fields);
        $viewData = $form->getViewdata();
        $this->assertEquals(self::NAME, $viewData['name']);
        $this->assertEquals(self::TITLE, $viewData['title']);
        $this->assertEquals(self::ACTION, $viewData['action']);
        $this->assertEquals($fields, $viewData['fields']);
    }

    public function testProcessFailed() {
        $_POST = [
            'CSRF' => self::CSRF_TOKEN,
            'handle' => self::HANDLE
        ];
        $fields = $this->getLoginForm();
        $form = new Form();
        $form->setFields($fields);
        $this->assertFalse($form->process());
        $values = $form->getValues();

        $this->assertEquals(self::CSRF_TOKEN, $values['CSRF']);
        $this->assertEquals(self::HANDLE, $values['handle']);
        $this->assertFalse(isset($values['password']));
    }

    private function getLoginForm()
    {
        return [
            'CSRF' => [
                'type' => 'hidden',
                'value' => self::CSRF_TOKEN
            ],
            'handle' => [
                'type' => 'text',
                'minlength' => 4,
                'maxlength' => 32,
                'required' => true
            ],
            'password' => [
                'type' => 'password',
                'minlength' => 6,
                'maxlength' => 16,
                'required' => true
            ],
            'login' => [
                'type' => 'submit',
            ]
        ];
    }
}
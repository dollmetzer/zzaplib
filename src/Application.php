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

namespace dollmetzer\zzaplib;

/**
 * Description of Application
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2014 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Application extends \dollmetzer\zzaplib\Base
{

    /**
     * @var Session Holds the instance of the session 
     */
    public $session;

    /**
     * @var View Holds the instance of the view 
     */
    public $view;

    /**
     * @var array Language snippets 
     */
    public $lang;

    /**
     * Construct the application
     * 
     * @param array $config Configuration array
     */
    public function __construct($config)
    {

        $this->config = $config;
        $this->dbh = NULL;
    }

    /**
     * Run the application
     */
    public function run()
    {

        // start session
        $this->session = new \dollmetzer\zzaplib\Session($this);

        // start view
        $this->view = new \dollmetzer\zzaplib\View($this);

        // split query path into module, controller, action and params
        $this->processQueryPath();
        
        if(DEBUG_REQUEST) {
            echo "\n<!-- REQUEST\n";
            echo 'Module     : '.$this->moduleName."\n";
            echo 'Controller : '.$this->controllerName."\n";
            echo 'Action     : '.$this->actionName."\n";
            echo "Parameters : \n";
            var_dump($this->params);
            echo "\n-->\n";
        }


        // load core language file for core module
        $this->lang = array();
        $this->loadLanguage('core', 'core');

        // start controller
        $controllerName = '\Application\modules\\' . $this->moduleName . '\controllers\\' . $this->controllerName . 'Controller';

        try {
            $controller = new $controllerName($this);

            $actionName = (string) $this->actionName . 'Action';
            if (method_exists($controller, $actionName) === false) {
                error_log('Application::run() - method ' . $actionName . ' not found in ' . $this->moduleName . '\controllers\\' . $this->controllerName . 'Controller');
                $this->forward($this->buildURL(''), $this->lang['error_illegal_parameter'], 'error');
            }

            $controller->preAction();
            if ($controller->isAllowed($this->actionName)) {
                $controller->$actionName();
                $controller->postAction();
            } else {
                $this->forward($this->buildURL(''), $this->lang('error_access_denied'), 'error');
            }
            $this->view->render();

            $this->session->flasherror = '';
            $this->session->flashmessage = '';
        } catch (\Exception $e) {

            $message = 'Application error in ';
            $message .= $e->getFile() . ' in Line ';
            $message .= $e->getLine() . ' : ';
            $message .= $e->getMessage();
            error_log($message);
            $this->forward($this->buildURL(''), $this->lang['error_application'], 'error');
        }
    }

    /**
     * Forward to another page
     * 
     * @param string $_url         Target URL 
     * @param string $_message     (optional) flash message to be displayed on next page
     * @param string $_messageType (optinal) Type if flash message. Either 'error' or 'message'
     */
    public function forward($_url = '', $_message = '', $_messageType = '')
    {

        if (!empty($_message)) {
            if ($_messageType == 'error') {
                $this->session->flasherror = $_message;
            } else {
                $this->session->flashmessage = $_message;
            }
        }
        if (empty($_url)) {
            $_url = $this->buildURL('');
        }
        header('Location: ' . $_url);
        exit;
    }

    /**
     * Return a language snippet in the current language
     * 
     * @param string $_snippet Name of the snippet
     * @return string either the snippet, or - if snippet wasn't defined - the name of the snippet, wrapped in ###_ _###
     */
    public function lang($_snippet)
    {

        if (empty($this->lang[$_snippet])) {
            $text = '###_' . $_snippet . '_###';
        } else {
            $text = $this->lang[$_snippet];
        }

        return $text;
    }

    /**
     * Build a complete URL from a query string
     * 
     * @param string $_path       Query string like controller/action/param_1/param_n 
     * @param array  $_attributes Additional Attributes. Array of key=>value pairs
     * @return string
     */
    public function buildURL($_path, $_attributes = array())
    {

        if (empty($_SERVER['SERVER_NAME'])) {
            return '';
        }
        
        $url = 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        if(URL_REWRITE) {
            $url .= '/'.$_path;
        } else {
            if (!empty($_path))
                $url .= '/index.php?q=' . $_path;
        }

        if (!empty($_attributes)) {
            $addition = array();
            foreach ($_attributes as $key => $val) {
                $addition[] = $key . '=' . urlencode($val);
            }
            $url .= '&' . join('&', $addition);
        }

        return $url;
    }

    /**
     * Build a complete URL from a query string
     * 
     * @param string $_path       Path to the picture on the media server
     * @return string
     */
    public function buildMediaURL($_path)
    {

        $url = 'http://' . URL_MEDIA . $_path;
        return $url;
    }

    /**
     * Try to load language snippets and store them in $this->lang
     * The desired language is stored in $_SESSION['user_language'].
     * The name of the file is [language]_[snippet].ini
     * E.g. 'de_account.ini' holds the snippets for the account controller in german.
     * 
     * @param string $_snippet Name of the snippet - mostly the controller name
     * @param string $_module  Name of the module, if language file shouldn't be for current module
     * @return boolean success
     */
    public function loadLanguage($_snippet, $_module = '')
    {

        if ($_module == '') {
            $filename = PATH_APP . 'modules/' . $this->moduleName . '/data/' . $this->session->user_language . '_' . $_snippet . '.ini';
        } else {
            $filename = PATH_APP . 'modules/' . $_module . '/data/' . $this->session->user_language . '_' . $_snippet . '.ini';
        }

        if (file_exists($filename)) {
            $lang = parse_ini_file($filename);
            $this->lang = array_merge($this->lang, $lang);
            return true;
        } else {
            error_log('Language File ' . $filename . ' not found');
        }
        return false;
    }

    /**
     * Get Query parameters from the get parameter 'q', like
     * 
     * <pre>http://SERVER/?q=module/controller/action/param1/param2/...</pre>
     * 
     * If the first parameter is a module name (registered in $this->config['modules']), extract it from the query and set $this->moduleName
     * If the then first parameter is a controller name, extract ist from the query and set $this->controllerName
     * If the then first parameter is an action name, extract it from the query and set $this->actionName 
     * The now remaining parameters are going to $this->params
     */
    protected function processQueryPath()
    {

        // set default values
        $this->moduleName = 'core';
        $this->controllerName = 'index';
        $this->actionName = 'index';
        $this->params = array();

        // escape, if querypath is empty
        if (empty($_GET['q']))
            return;

        // clean query path
        $queryRaw = explode('/', $_GET['q']);
        $query = array();
        for ($i = 0; $i < sizeof($queryRaw); $i++) {
            if ($queryRaw[$i] != '')
                array_push($query, $queryRaw[$i]);
        }

        // test if first entry is a module name
        if (sizeof($query) > 0) {
            if (in_array($query[0], $this->getModuleList())) {
                $this->moduleName = array_shift($query);
            }
        }

        // test if first entry is a controller name
        if (sizeof($query) > 0) {
            if (in_array($query[0], $this->getControllerList())) {
                $this->controllerName = array_shift($query);
            }
        }

        // action name remaining?
        if (sizeof($query) > 0) {
            $this->actionName = array_shift($query);
        }
        
        // still any additional parameter remaining?
        $this->params = $query;
        
    }

}

?>

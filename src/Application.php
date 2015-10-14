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
 * Main Application class as base for the web application
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2015 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Application extends \dollmetzer\zzaplib\Base {

    /**
     * @var Session Holds the instance of the session 
     */
    public $session;

    /**
     * @var string Hold the URL query string
     */
    public $queryString;
    
    /**
     * @var View Holds the instance of the view 
     */
    public $view;

    /**
     * Construct the application
     * 
     * @param array $config Configuration array
     */
    public function __construct($config) {

        $this->config = $config;
        $this->dbh = NULL;
    }

    /**
     * Run the application
     */
    public function run() {

        // start session
        $this->session = new \dollmetzer\zzaplib\Session($this);

        // start view
        $this->view = new \dollmetzer\zzaplib\View($this);

        // Routing (split query path into module, controller, action and params)
        $this->moduleName = 'core';
        $this->controllerName = 'index';
        $this->actionName = 'index';
        $this->params = array();
        $this->routing();

        if (DEBUG_REQUEST) {
            echo "\n<!-- REQUEST\n";
            echo 'Module     : ' . $this->moduleName . "\n";
            echo 'Controller : ' . $this->controllerName . "\n";
            echo 'Action     : ' . $this->actionName . "\n";
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
            if (class_exists($controllerName)) {
                $controller = new $controllerName($this);
            } else {
                throw new \Exception('Controller class ' . $controllerName . ' not found');
            }

            $actionName = (string) $this->actionName . 'Action';
            if (method_exists($controller, $actionName) === false) {
                $this->log('Application::run() - method ' . $actionName . ' not found in ' . $this->moduleName . '\controllers\\' . $this->controllerName . 'Controller');
                $this->forward($this->buildURL(''), $this->lang['error_illegal_parameter'], 'error');
            }

            if ($controller->isAllowed($this->actionName)) {
                $controller->preAction();
                $controller->$actionName();
                $controller->postAction();
            } else {
                if ($this->session->user_id == 0) {

                    // Not logged in
                    // Remeber target page, try quicklogin or jump to login page
                    if($this->config['core']['quicklogin'] === true) {
                        $this->quicklogin();
                    }
                    $this->session->queryString = $this->queryString;
                    $this->forward($this->buildURL('account/login'), $this->lang('error_not_logged_in'), 'error');
                    
                } else {
                    
                    // logged in, but no access rights
                    $this->forward($this->buildURL(''), $this->lang('error_access_denied'), 'error');
                    
                }
            }
            $this->view->render();

            $this->session->flasherror = '';
            $this->session->flashmessage = '';
        } catch (\Exception $e) {

            $message = 'Application error in ';
            $message .= $e->getFile() . ' in Line ';
            $message .= $e->getLine() . ' : ';
            $message .= $e->getMessage();
            $this->log($message);
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
    public function forward($_url = '', $_message = '', $_messageType = '') {

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
     * Build a complete URL from a query string
     * 
     * @param string $_path       Path to the picture on the media server
     * @return string
     */
    public function buildMediaURL($_path) {

        $url = 'http://' . URL_MEDIA . $_path;
        return $url;
    }

    /**
     * Standard routing processes URL to define module, controller, action and additional params.
     * 
     * Get Query parameters from the get parameter 'q', like
     * 
     * <pre>http://SERVER/?q=module/controller/action/param1/param2/...</pre>
     * 
     * If the first parameter is a module name (registered in $this->config['modules']), extract it from the query and set $this->moduleName
     * If the then first parameter is a controller name, extract ist from the query and set $this->controllerName
     * If the then first parameter is an action name, extract it from the query and set $this->actionName 
     * The now remaining parameters are going to $this->params
     */
    protected function routing() {

        // escape, if querypath is empty
        if (empty($_GET['q']))
            return;
        $this->queryString = $_GET['q'];

        // clean query path
        $queryRaw = explode('/', $this->queryString);
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

    
    protected function quicklogin() {

        if(!empty($_COOKIE['qltoken'])) {
            $this->log('qltoken is '.$_COOKIE['qltoken'], 'notice');
            $userModel = new \Application\modules\core\models\userModel($this);
            $user = $userModel->getByToken($_COOKIE['qltoken']);
            $this->log(print_r($user, true), 'notice');
            if(!empty($user)) {
                if($user['useragent'] == $_SERVER['HTTP_USER_AGENT']) {
                    $this->session->login($user);
                    if(empty($this->queryString)) {
                        $this->forward($this->buildURL('/'), $this->lang('msg_logged_in'));
                    } else {
                        
                        $this->forward($this->buildURL($this->queryString), $this->lang('msg_logged_in'));
                    }
                }
            }
        }

    }
    
}

?>

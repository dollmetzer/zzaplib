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
class Application
{

    /**
     * @var array $config The configuration of the application
     */
    public $config;

    /**
     * @var \PDO $dbh Database handle
     */
    public $dbh;

    /**
     * @var Session $session Holds the instance of the session
     */
    public $session;

    /**
     * @var Request $request
     */
    public $request;

    /**
     * @var View Holds the instance of the view 
     */
    public $view;

    /**
     * Construct the application
     * 
     * @param array $config Configuration array
     */
    public function __construct($config)
    {

        $this->config = $config;
        $this->dbh = null;

        // start session
        $this->session = new \dollmetzer\zzaplib\Session($config);

    }

    /**
     * Run the application
     */
    public function run()
    {

        // construct request element
        $this->request = new \dollmetzer\zzaplib\Request($this->config, $this->session);

        // start view
        $this->view = new \dollmetzer\zzaplib\View($this->session, $this->request);

        // Routing (split query path into module, controller, action and params)
        $this->request->routing();

        if (DEBUG_REQUEST) {
            echo "\n<!-- REQUEST\n";
            echo 'Module     : '.$this->request->moduleName."\n";
            echo 'Controller : '.$this->request->controllerName."\n";
            echo 'Action     : '.$this->request->actionName."\n";
            echo "Parameters : \n";
            print_r($this->request->params);
            echo "\n-->\n";
        }

        // load core language file for core module
        $this->view->loadLanguage('core', 'core', $this->session->user_language);

        // start controller
        $controllerName = '\Application\modules\\'.$this->request->moduleName.'\controllers\\'.$this->request->controllerName.'Controller';

        try {

            // exists controller class?
            if (class_exists($controllerName)) {
                $controller = new $controllerName(
                    $this->session,
                    $this->request,
                    $this->view
                );
            } else {
                throw new \Exception('Controller class '.$controllerName.' not found');
            }

            // exists action method?
            $actionName = (string) $this->request->actionName.'Action';
            if (method_exists($controller, $actionName) === false) {
                $this->request->log('Application::run() - method '.$actionName.' not found in '.$this->request->moduleName.'\controllers\\'.$this->request->controllerName.'Controller');
                $this->request->forward($this->request->buildURL(''),
                    $this->view->lang('error_illegal_parameter'), 'error');
            }

            // is access to action method allowed?
            if(method_exists($controller, 'isAllowed')) {
                $isAllowed = $controller->isAllowed($this->request->actionName);
            } else {
                $isAllowed = true;
            }

            if ($isAllowed) {

                if(method_exists($controller, 'preAction')) {
                    $controller->preAction();
                }
                $controller->$actionName();
                if(method_exists($controller, 'postAction')) {
                    $controller->postAction();
                }
            } else {
                if ($this->session->user_id == 0) {

                    // Not logged in
                    // Remeber target page, try quicklogin or jump to login page
                    if ($this->config['quicklogin'] === true) {
                        if(method_exists($controller, 'quicklogin')) {
                            $controller->quicklogin();
                        }
                    }
                    $this->session->queryString = $this->request->queryString;
                    $this->request->forward($this->request->buildURL('account/login'),
                        $this->view->lang('error_not_logged_in', false), 'error');
                } else {

                    // logged in, but no access rights
                    $this->request->forward($this->request->buildURL(''),
                        $this->view->lang('error_access_denied', false), 'error');
                }
            }
            $this->view->render();

            $this->session->flasherror   = '';
            $this->session->flashmessage = '';

        } catch (\Exception $e) {

            $message = 'Application error in ';
            $message .= $e->getFile().' in Line ';
            $message .= $e->getLine().' : ';
            $message .= $e->getMessage();
            $this->request->log($message);
            $this->request->forward($this->request->buildURL(''),
                $this->view->lang('error_application', false), 'error');

        }
    }

}
?>

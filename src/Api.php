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
 * Main Api class as base for a REST API
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2018 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Api
{

    /**
     * @var array Configuration
     */
    protected $config;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var Response $response
     */
    protected $response;

    /**
     * @var View Holds the instance of the view (for access to the formatting methods)
     */
    public $view;


    /**
     * Construct the API
     *
     * @param array $config Configuration array
     */
    public function __construct($config)
    {

        $this->config = $config;
        $this->dbh = null;

    }

    /**
     * Run the API
     */
    public function run()
    {

        // construct request and response elements
        $this->request = new Request($this->config, null);
        $this->response = new Response();

        // start view
        $this->session = new Session($this->config);
        $this->view = new View($this->session, $this->request);

        // split query path into module, controller, action and params
        $routing = $this->request->ApiRouting();

        if (DEBUG_API) {
            $msg = 'API Call module ' . $this->request->moduleName;
            $msg .= ', controller ' . $this->request->controllerName;
            $msg .= ', action ' . $this->request->actionName;
            $msg .= ', parameters ' . print_r($this->request->params, true);
            error_log($msg);
        }

        if ($routing === true) {

            // start controller
            $controllerName = '\Application\modules\\' . $this->request->moduleName . '\api\\' . $this->request->controllerName . 'Controller';

            try {

                // exists controller class?
                if (class_exists($controllerName)) {
                    $controller = new $controllerName(
                        $this->config,
                        $this->request,
                        $this->response,
                        $this->view
                    );
                } else {
                    throw new \Exception('Controller class ' . $controllerName . ' not found');
                }

                // exists action method?
                $actionName = (string)$this->request->actionName . 'Action';
                if (method_exists($controller, $actionName) === false) {
                    $this->request->log('Application::run() - method ' . $actionName . ' not found in ' . $controllerName);
                    $this->response->setStatusCode(405);
                } else {

                    // is access to action method allowed?
                    if (method_exists($controller, 'isAllowed')) {
                        $isAllowed = $controller->isAllowed($this->actionName);
                    } else {
                        $isAllowed = true;
                    }

                    if ($isAllowed) {

                        $this->response->setData($controller->$actionName());

                    } else {
                        $this->request->log('Application::run() - access to ' . $controllerName . '::' . $actionName . ' is forbidden');
                        $this->response->setStatusCode(403);
                    }

                }
            } catch (\Exception $e) {

                $message = 'Application error in ';
                $message .= $e->getFile() . ' in Line ';
                $message .= $e->getLine() . ' : ';
                $message .= $e->getMessage();
                $this->request->log($message);
                $this->response->setStatusCode(500);
            }

        } else {

            // no endpoint found
            $this->request->log('Api::run() - 400 - Endpoint not available');
            $this->response->setStatusCode(400);
            $this->response->setStatusInfo('Endpoint not available');

        }

        header('Content-Type: application/json');
        header('HTTP/1.0 ' . $this->response->getStatusCode() . ' ' . $this->response->getStatusMessage());
        echo json_encode($this->response->getAsArray());
    }

}
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
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Api
{
    public $HTTP_STATUS;
    public $response;
    public $params;

    protected $config;
    protected $request;

    /**
     * Construct the API
     *
     * @param array $config Configuration array
     */
    public function __construct($config)
    {

        $this->config = $config;
        $this->dbh = null;

        // register autoloader for models, no class not found exception, not prepend
        //spl_autoload_register(array($this, 'autoloadModels'), false, false);

        $this->HTTP_STATUS = array(
            // 1xx - Informations not implemented
            // 2xx - Successful operations
            200 => 'OK', // success
            201 => 'Created', // ressource was created. "Location“-header-field may contain Address of the ressource 
            202 => 'Accepted', // request was queued and maybe later executed
            // 3xx - Redirections not implemented
            // 4xx - Client errors
            400 => 'Bad Request', // syntax errors. 422 is semantic errors
            401 => 'Unauthorized', // no authentication sent
            403 => 'Forbidden', // 
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            422 => 'Unprocessable Entity', // semantic errors
            423 => 'Locked',
            429 => 'Too Many Requests',
            451 => 'Unavailable For Legal Reasons',
            // 5xx - Server errors
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            503 => 'Service Unavailable',
            507 => 'Insufficient Storage'
        );
    }

    /**
     * Run the API
     */
    public function run()
    {

        // construct request element
        $this->request = new Request($this->config, null);

        // default response
        $this->response = array(
            'statusCode' => 200,
            'statusMessage' => $this->HTTP_STATUS[200],
            'data' => array()
        );

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
                        $this->request
                    );
                } else {
                    throw new \Exception('Controller class ' . $controllerName . ' not found');
                }

                // exists action method?
                $actionName = (string)$this->request->actionName . 'Action';
                if (method_exists($controller, $actionName) === false) {
                    $this->request->log('Application::run() - method ' . $actionName . ' not found in ' . $controllerName);
                    $this->response['statusCode'] = 405;
                    $this->response['statusMessage'] = $this->HTTP_STATUS[405];
                } else {

                    // is access to action method allowed?
                    if (method_exists($controller, 'isAllowed')) {
                        $isAllowed = $controller->isAllowed($this->actionName);
                    } else {
                        $isAllowed = true;
                    }

                    if ($isAllowed) {

                        $this->response['data'] = $controller->$actionName();

                    } else {
                        $this->request->log('Application::run() - access to ' . $controllerName . '::' . $actionName . ' is forbidden');
                        $this->response['statusCode'] = 403;
                        $this->response['statusMessage'] = $this->HTTP_STATUS[403];
                    }

                }
            } catch (\Exception $e) {

                $message = 'Application error in ';
                $message .= $e->getFile() . ' in Line ';
                $message .= $e->getLine() . ' : ';
                $message .= $e->getMessage();
                $this->request->log($message);
                $this->response['statusCode'] = 500;
                $this->response['statusMessage'] = $this->HTTP_STATUS[500];
            }

        } else {

            // no endpoint found
            $this->request->log('Api::run() - 400 - Endpoint not available');
            $this->response['statusCode'] = 400;
            $this->response['statusMessage'] = $this->HTTP_STATUS[400];
            $this->response['statusInfo'] = 'Endpoint not available';

        }

        header('Content-Type: application/json');
        header('HTTP/1.0 ' . $this->response['statusCode'] . ' ' . $this->response['statusMessage']);
        echo json_encode($this->response);
    }
    
}
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

/**
 * Description of Api
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2014 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Api extends Base {
    
    public $HTTP_STATUS;
    
    public $response;
    
    /**
     * Construct the API
     * 
     * @param array $config Configuration array
     */
    public function __construct($config) {

        $this->config = $config;
        $this->dbh = NULL;

        // register autoloader for models, no class not found exception, not prepend
        spl_autoload_register(array($this, 'autoloadModels'), false, false);

        $this->HTTP_STATUS = array(
            // 1xx - Informations not implemented
            
            // 2xx - Successful operations
            200 => 'OK',        // success
            201 => 'Created',   // ressource was created. "Location“-header-field may contain Address of the ressource 
            202 => 'Accepted',  // request was queued and maybe later executed
            
            // 3xx - Redirections not implemented
            
            // 4xx - Client errors
            400 => 'Bad Request',       // syntax errors. 422 is semantic errors
            401 => 'Unauthorized',      // no authentication sent
            403 => 'Forbidden',         // 
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
    public function run() {
        
        // default response
        $this->response = array(
            'statusCode' => 200,
            'statusMessage' => $this->HTTP_STATUS[200],
            'data' => array()
        );
        
        // split query path into module, controller, action and params
        $this->processQueryPath();

        /*
        echo '<pre>';
        echo $this->moduleName."\n";
        echo $this->controllerName."\n";
        echo $this->actionName."\n";
        print_r($this->params);
        */
        
        // start controller
        $controllerFile = PATH_APP . 'modules/' . $this->moduleName . '/controllers/' . $this->controllerName . 'Controller.php';

        try {
            include $controllerFile;
            $controllerName = $this->controllerName . 'Controller';
            $controller = new $controllerName($this);
            $actionName = (string) $this->actionName . 'ApiAction';

            if (method_exists($controller, $actionName) === false) {
                error_log('Application::run() - method ' . $actionName . ' not found in ' . $controllerFile);
                $this->response['statusCode'] = 405;
                $this->response['statusMessage'] = $this->HTTP_STATUS[405];                
            } else {
                if($controller->isAllowed($this->actionName)) {
                    $controller->$actionName();            
                } else {
                    error_log('Application::run() - access to '.$controllerName.'::'.$actionName.' is forbidden');
                    $this->response['statusCode'] = 403;
                    $this->response['statusMessage'] = $this->HTTP_STATUS[403];                
                }                
            }
                
        } catch (Exception $e) {

            $message = 'Application error in ';
            $message .= $e->getFile() . ' in Line ';
            $message .= $e->getLine() . ' : ';
            $message .= $e->getMessage();
            error_log($message);
            $this->response['statusCode'] = 500;
            $this->response['statusMessage'] = $this->HTTP_STATUS[500];
            
        }
    
        header('Content-Type: application/json');
        header('HTTP/1.0 '.$this->response['statusCode'].' '.$this->response['statusMessage']);
        echo json_encode($this->response);

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
    protected function processQueryPath() {

        // set default values
        $this->moduleName = 'core';
        $this->controllerName = 'index';
        $this->actionName = 'get';
        $this->params = array();

        // escape, if querypath is empty
        if (empty($_GET['q']))
            return;

        // action = method
        $this->actionName = strtolower($_SERVER['REQUEST_METHOD']);
                
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

        // still any additional parameter remaining?
        $this->params = $query;
    }

    
    /**
     * Dummy method
     * 
     * @param string $_snippet
     * @param string $_module
     */
    public function loadLanguage($_snippet, $_module = '') {
        
        // do nothing
        
    }
    
}

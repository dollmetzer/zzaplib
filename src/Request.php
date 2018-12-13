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
 * Helper class for request handling
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2018 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Request
{

    /**
     * @var string $queryString Hold the URL query string
     */
    public $queryString;

    /**
     * @var string $moduleName Hold the name of the called module. Default is 'core'
     */
    public $moduleName = 'core';

    /**
     * @var string $controllerName Hold the name of the called controller. Default is 'index'
     */
    public $controllerName = 'index';

    /**
     * @var string $actionName Hold the name of the called action. Default is 'index'
     */
    public $actionName = 'index';

    /**
     * @var array $params Array of optional parameters
     */
    public $params = array();

    /**
     * @var string|null Raw input from request body on PUT oder POST requests
     */
    public $rawBody;

    /**
     * @var array $config Configuration array
     */
    public $config;

    /**
     * @var Session|null $session For an API call, session is null
     */
    public $session;

    /**
     * @var Module $module
     */
    public $module;


    /**
     * Constructor
     *
     * @param array $config Configuration array
     * @param Session|NULL $_session
     */
    public function __construct(array $config, $_session)
    {

        $this->config = $config;
        $this->session = $_session;
        $this->module = new Module();

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
    public function routing()
    {

        // escape, if querypath is empty
        if (empty($_GET['q'])) {
            return;
        }
        $this->queryString = $_GET['q'];

        // clean query path
        $queryRaw = explode('/', $this->queryString);
        $query = array();
        for ($i = 0; $i < sizeof($queryRaw); $i++) {
            if ($queryRaw[$i] != '') {
                array_push($query, $queryRaw[$i]);
            }
        }

        // test if first entry is a module name
        if (sizeof($query) > 0) {
            $moduleList = $this->getModuleList();
            if (in_array($query[0], $moduleList)) {
                $this->moduleName = array_shift($query);
            }
        }

        // test if first entry is a controller name
        if (sizeof($query) > 0) {
            if (in_array($query[0], $this->getControllerList($this->moduleName))) {
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

    /**
     * Get Query parameters from the get parameter 'q', like
     *
     * <pre>http://SERVER/?q=module/controller/param1/param2/...</pre>
     *
     * If the first parameter is a valid module name, extract it from the query and set $this->moduleName
     * If the then first parameter is a controller name, extract ist from the query and set $this->controllerName
     * The now remaining parameters are going to $this->params
     *
     * @return bool Success. If false, either module name or controller name are invalid
     */
    public function apiRouting()
    {

        // set default values
        $this->moduleName = 'core';
        $this->controllerName = 'index';
        $this->actionName = 'get';
        $this->params = array();
        $success = true;

        // escape, if querypath is empty
        if (empty($_GET['q'])) {
            return $success;
        }

        // action = method
        $this->actionName = strtolower($_SERVER['REQUEST_METHOD']);

        // clean query path
        $queryRaw = explode('/', $_GET['q']);
        $query = array();
        for ($i = 0; $i < sizeof($queryRaw); $i++) {
            if ($queryRaw[$i] != '') {
                array_push($query, $queryRaw[$i]);
            }
        }

        // test if first entry is a module name
        if (sizeof($query) > 0) {
            if (in_array($query[0], $this->getModuleList())) {
                $this->moduleName = array_shift($query);
            } else {
                $success = false;
            }
        }

        // test if first entry is a controller name
        if (sizeof($query) > 0) {
            if (in_array($query[0], $this->getApiControllerList($this->moduleName))) {
                $this->controllerName = array_shift($query);
            } else {
                $success = false;
            }
        }

        // still any additional parameter remaining?
        $this->params = $query;

        // get request body on POST or PUT
        if( ($this->actionName == 'put') || ($this->actionName == 'post') ) {
            $this->rawBody = file_get_contents('php://input');
        }

        return $success;

    }

    /**
     * Get a list of installed modules. If modules are set in the configuration,
     * get the list from the configuration.
     * If modules are not in the configuration, read list from filesystem
     * in app/modules.
     *
     * @param bool $_onlyNames if true, return only names. If false return complete module config
     * @return array
     */
    public function getModuleList($_onlyNames = true)
    {

        if ($_onlyNames === true) {
            return array_keys($this->module->getConfig());
        } else {
            return $this->module->getConfig();
        }

    }

    /**
     * Get a list of available controllers for the module $this->moduleName
     * If modules are set in the configuration, get the list from the
     * configuration. Without configuration entry, get the list from the filesystem.
     *
     * @return array
     */
    public function getControllerList($_moduleName)
    {

        if (!empty($this->config['modules'][$_moduleName]['controllers'])) {
            $list = $this->config['modules'][$_moduleName]['controllers'];
        } else {

            $list = array();
            $controllerDir = PATH_APP . 'modules/' . $_moduleName . '/controllers/';
            $dir = opendir($controllerDir);
            while ($file = readdir($dir)) {
                if (preg_match('/Controller.php$/', $file)) {
                    $list[] = preg_replace('/Controller.php$/', '', $file);
                }
            }
            closedir($dir);
        }
        sort($list);

        return $list;
    }

    /**
     * Get a list of available API controllers for the module $this->moduleName
     * If modules are set in the configuration, get the list from the
     * configuration. Without configuration entry, get the list from the filesystem.
     *
     * @return array
     */
    public function getApiControllerList($_moduleName)
    {

        if (!empty($this->config['modules'][$_moduleName]['apiControllers'])) {
            $list = $this->config['modules'][$_moduleName]['apiControllers'];
        } else {

            $list = array();
            $controllerDir = PATH_APP . 'modules/' . $_moduleName . '/api/';
            $dir = opendir($controllerDir);
            while ($file = readdir($dir)) {
                if (preg_match('/Controller.php$/', $file)) {
                    $list[] = preg_replace('/Controller.php$/', '', $file);
                }
            }
            closedir($dir);
        }
        sort($list);

        return $list;

    }

    /**
     * Get a list of possible API calls
     *
     * @return array
     */
    public function listApiCalls()
    {

        $calls = array();
        foreach ($this->getModuleList() as $module) {
            $apiControllerList = $this->getApiControllerList($module);
            foreach ($apiControllerList as $controller) {
                $calls[] = $module . '/' . $controller;
            }
        }
        return $calls;

    }

    /**
     * Forward to another page
     *
     * @param string $_url Target URL
     * @param string $_message (optional) flash message to be displayed on next page
     * @param string $_messageType (optinal) Type if flash message. Either 'error' or 'message'
     */
    public function forward($_url = '', $_message = '', $_messageType = '')
    {

        if (!empty($_message) && ($this->session != null)) {
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
     * @param string $_path Query string like controller/action/param_1/param_n
     * @param array $_attributes Additional Attributes. Array of key=>value pairs
     * @return string
     */
    public function buildURL($_path, $_attributes = array())
    {

        if (URL_HTTPS === true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $url = $protocol . URL_BASE;
        if (URL_REWRITE) {
            $url .= '/' . $_path;
        } else {
            if (!empty($_path)) {
                $url .= '/index.php?q=' . $_path;
            }
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
     * Build a complete URL for media files
     *
     * @param string $_path Path to media file
     * @return string
     */
    public function buildMediaURL($_path)
    {

        if (URL_HTTPS === true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $url = $protocol . URL_MEDIA . '/' . $_path;

        return $url;

    }

    /**
     * Write a log entry.
     *
     * The logfile is in PATH_LOGS and its name consist of type and
     * date (e.g. notice_2015_07_25.txt).
     *
     * @param string $_message
     * @param string $_type 'error'(default) or 'notice'
     */
    public function log($_message, $_type = 'error')
    {

        if (in_array($_type, array('error', 'notice'))) {
            $type = $_type;
        } else {
            $type = 'error';
        }

        $message = strftime('%d.%m.%Y %H:%M:%S', time());
        $message .= "\t" . $_message . "\n";

        $logfile = PATH_LOGS . $type . strftime('_%Y_%m_%d.txt', time());
        if (!file_exists($logfile)) {
            $fp = fopen($logfile, 'w+');
            fwrite($fp, "Logfile $logfile\n--------------------\n");
            fclose($fp);
            chmod($logfile, 0664);
        }
        $fp = fopen($logfile, 'a+');
        fwrite($fp, $message);
        fclose($fp);
    }

}

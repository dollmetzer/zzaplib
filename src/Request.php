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
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
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
    public $actionName     = 'index';

    /**
     * @var array $params Array of optional parameters
     */
    public $params         = array();

    public $config;

    public $session;

    /**
     * Constructor
     *
     * @param array $config Configuration array
     */
    public function __construct(array $config, Session $_session)
    {

        $this->config = $config;
        $this->session = $_session;

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
            if (in_array($query[0], $this->getModuleList())) {
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
     * Get a list of installed modules. If modules are set in the configuration,
     * get the list from the configuration. Every module entry must be an array.
     *
     * 'modules' => array (
     *     'core' => array(
     *         'index',
     *         'account'
     *     )
     *  )
     *
     * If modules are not in the configuration, read list from filesystem
     * in app/modules.
     *
     * @return array
     */
    public function getModuleList()
    {

        if (empty($this->config['modules'])) {
            $list = array();
            if (file_exists(PATH_APP . 'modules/')) {
                $dir = opendir(PATH_APP . 'modules/');
                while ($file = readdir($dir)) {
                    if (!preg_match('/^\./', $file)) {
                        if (is_dir(PATH_APP . 'modules/' . $file)) {
                            $list[] = $file;
                        }
                    }
                }
                closedir($dir);
            }
        } else {
            $list = array_keys($this->config['modules']);
        }
        sort($list);

        return $list;
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

        if (!empty($this->config['modules'][$_moduleName])) {

            $list = $this->config['modules'][$_moduleName];
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
     * Forward to another page
     *
     * @param string $_url Target URL
     * @param string $_message (optional) flash message to be displayed on next page
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
     * Build a complete URL from a query string
     *
     * @param string $_path       Query string like controller/action/param_1/param_n
     * @param array  $_attributes Additional Attributes. Array of key=>value pairs
     * @return string
     */
    public function buildURL($_path, $_attributes = array())
    {

        if(URL_HTTPS === true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $url = $protocol.URL_BASE;
        if (URL_REWRITE) {
            $url .= '/'.$_path;
        } else {
            if (!empty($_path)) $url .= '/index.php?q='.$_path;
        }

        if (!empty($_attributes)) {
            $addition = array();
            foreach ($_attributes as $key => $val) {
                $addition[] = $key.'='.urlencode($val);
            }
            $url .= '&'.join('&', $addition);
        }

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
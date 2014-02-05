<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Base
 *
 * @author dirk
 */
class Base {
    
    /**
     * @var array The configuration of the application 
     */
    public $config;
    
    /**
     * @var string Name of the module 
     */
    public $moduleName;

    /**
     * @var string The name of the controller 
     */
    public $controllerName;

    /**
     * @var string The name of the action 
     */
    public $actionName;

    /**
     * @var string $params Holds the URL parameters after controller/action/...
     */
    public $params;

    /**
     * @var Controller Holds the instance of the Controller 
     */
    public $controller;

    /**
     * @var PDO $dbh Database handle
     */
    public $dbh;

    
    /**
     * Model class Autoloader
     * 
     * Model classes should be named like modulename_thingModel with the
     * filename PATH_APP/modules/modulename/models/modulename_thingModel.php
     *     
     * @param type $_className Name of the class to load 
     */
    public function autoloadModels($_className) {
		
        if(preg_match('/[a-zA-Z].Model$/', $_className)) {
            
            $temp = explode('_', $_className);
            if(sizeof($temp) > 1) {
                $filename = PATH_APP.'modules/'.$temp[sizeof($temp)-2].'/models/'.$_className.'.php';
            } else {
                $filename = PATH_APP.'modules/core/models/'.$_className.'.php';
            }
 
            if (file_exists($filename)) {
                require_once $filename;
            } else {
                error_log('Application::autoload - file not found: ' . $filename);
            }            
        }
    
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
     * @return array
     */
    protected function getModuleList() {

        if (empty($this->config['modules'])) {
            $list = array();
            if (file_exists(PATH_APP . 'modules/')) {
                $dir = opendir(PATH_APP . 'modules/');
                while ($file = readdir($dir)) {
                    if (!preg_match('/^\./', $file)) {
                        echo $file . "<br />\n";
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

        return $list;
    }

    /**
     * Get a list of available controllers for the module $this->moduleName
     * If modules are set in the configuration, get the list from the
     * configuration. Without configuration entry, get the list from the filesystem.
     * 
     * @return array
     */
    protected function getControllerList() {

        if (empty($this->config['modules'][$this->moduleName])) {

            $list = $this->config['modules'][$this->moduleName];
        } else {

            $list = array();
            $controllerDir = PATH_APP . 'modules/' . $this->moduleName . '/controllers/';
            $dir = opendir($controllerDir);
            while ($file = readdir($dir)) {
                if (preg_match('/Controller.php$/', $file)) {
                    $list[] = preg_replace('/Controller.php$/', '', $file);
                }
            }
            closedir($dir);
        }
        return $list;
    }


    
}

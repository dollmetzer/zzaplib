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
 * Helper class for module handling
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Module
{

    /**
     * @var string $configFilePath Path and filename to configuration file
     */
    protected $configFilePath;

    /**
     * @var array $config module configuration
     */
    protected $config;

    /**
     * @var array $protectedModules Modules, that must be active
     */
    protected $protectedModules = array('core', 'users');

    /**
     * Module constructor.
     * Tries to load module config. If none is found, create a new one.
     */
    public function __construct()
    {

        $this->configFilePath = PATH_TMP . 'module_config.json';
        if ($this->loadConfig() === false) {
            $this->config = $this->buildConfig();
            $this->saveConfig();
        }

    }

    /**
     * Load module configuration from file
     *
     * @return bool Success
     */
    public function loadConfig()
    {

        if (!file_exists($this->configFilePath)) {
            return false;
        }
        $this->config = json_decode(file_get_contents($this->configFilePath), true);
        return true;

    }

    /**
     * Save module configuration file
     */
    public function saveConfig()
    {

        $fp = fopen($this->configFilePath, 'w+');
        fwrite($fp, json_encode($this->config));
        fclose($fp);

    }

    /**
     * Set a configuration value
     *
     * @param string $_module Name of the module
     * @param string $_name Name of the arameter
     * @param mixed $_value Value
     * @return bool Success
     */
    public function set($_module, $_name, $_value)
    {

        if (!isset($this->config[$_module])) {
            return false;
        }

        if( ($_name == 'active') && in_array($_module, $this->protectedModules)) {
            return false;
        }

        $this->config[$_module][$_name] = $_value;
        return true;

    }

    /**
     * Get a configuration value
     *
     * @param string $_module Name of the module
     * @param string $_name Name of the parameter
     * @return mixed/null If no value is found, NULL is returned
     */
    public function get($_module, $_name)
    {

        if (!isset($this->config[$_module][$_name])) {
            return null;
        }

        return $this->config[$_module][$_name];

    }

    /**
     * Get information if a module is active
     *
     * @param string $_module Name of the module
     * @return bool Active state
     */
    public function isActive($_module) {

        if($this->config[$_module]['active'] === true) {
            return true;
        }
        return false;

    }

    /**
     * Remove entries from deleted modules and add entries from new modules
     */
    public function rebuildConfig() {

        // first remove entries for deleted modles
        $currentModules = $this->buildConfig();
        $currentNames = array_keys($currentModules);
        foreach(array_keys($this->config) as $mName) {
            if(!in_array($mName, $currentNames)) {
                unset($this->config[$mName]);
            }
        }

        // add enties for new modules
        $previousNames = array_keys($this->config);
        foreach($currentNames as $mName) {
            if(!in_array($mName, $previousNames)) {
                $this->config[$mName] = $currentModules[$mName];
            }
        }

        $this->saveConfig();

    }

    /**
     * Activate a module
     *
     * @param string $_module Name of the module
     * @return bool Success
     * @throws \Exception If module can't be activated
     */
    public function activate($_module)
    {

        if ($this->get($_module, 'active') !== false) {
            throw new \Exception("Module '$_module' can't be activated, because it's not inactive");
        }

        $this->set($_module, 'active', true);
        $this->saveConfig();

        return true;
    }

    /**
     * Deactivate Module
     *
     * @param string $_module Name of the module
     * @return bool Success
     * @throws \Exception If module can't be deactivated
     */
    public function deactivate($_module)
    {

        if ($this->get($_module, 'active') !== true) {
            throw new \Exception("Module '$_module' can't be deactivated, because it's not active");
        }
        if ( in_array($_module, $this->protectedModules) ) {
            throw new \Exception("Module '".$_module."' can't be deactivated!");
        }


        $this->set($_module, 'active', false);
        $this->saveConfig();
        return true;
    }

    /**
     * Returns module configuration file
     *
     * @return array
     */
    public function getConfig()
    {

        return $this->config;

    }


    /**
     * Build a new module config from the filesystem
     *
     * @return array
     */
    protected function buildConfig()
    {

        $config = array();
        $moduleDir = PATH_APP . 'modules/';
        $dir = opendir($moduleDir);
        while ($file = readdir($dir)) {

            if (!$this->isValidName($file)) {
                continue;
            }

            // set module active state
            $active = false;
            if ($file == 'core') {
                $active = true;
            }
            $config[$file] = array(
                'active' => $active
            );

            // merge default config, if available
            $dataFile = $moduleDir . $file . '/data/config.php';
            if (file_exists($dataFile)) {
                $config[$file] = array_merge($config[$file], include $dataFile);
            }

        }
        closedir($dir);
        ksort($config);

        return $config;

    }

    /**
     * Check if string is a valid module name
     *
     * @param string $_name
     * @return bool
     */
    protected function isValidName($_name)
    {

        if (preg_match('/^[a-z0-9]/', $_name)) {
            return true;
        }
        return false;

    }

}
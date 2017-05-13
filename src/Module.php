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
     * @var string Path and filename to configuration file
     */
    protected $configFilePath;

    /**
     * @var array module configuration
     */
    protected $config;


    /**
     * Module constructor.
     * Tries to load module config. If none is found, create a new one.
     */
    public function __construct()
    {

        $this->configFilePath = PATH_TMP . 'module_config.json';
        if ($this->loadConfig() === false) {
            $this->buildConfig();
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
     * @param string $_module
     * @param string $_name
     * @param mixed $_value
     * @return bool
     */
    public function set($_module, $_name, $_value)
    {

        if (!isset($this->config[$_module])) {
            return false;
        }

        if (($_module == 'core') && ($_name == 'active')) {
            return false;
        }

        $this->config[$_module][$_name] = $_value;
        return true;

    }

    /**
     * Get a configuration value
     *
     * @param string $_module
     * @param string $_name
     * @return mixed/null If no value is found, NULL is returned
     */
    public function get($_module, $_name)
    {

        if (!isset($this->config[$_module][$_name])) {
            return null;
        }

        return $this->config[$_module][$_name];

    }


    public function activate($_module)
    {

        if ($this->get($_module, 'active') !== false) {
            throw new \Exception("Module '$_module' can't be activated, because it's not inactive");
        }


        $this->set($_module, 'active', true);
        $this->saveConfig();

        return true;
    }

    public function deactivate($_module)
    {

        if ($this->get($_module, 'active') !== true) {
            throw new \Exception("Module '$_module' can't be deactivated, because it's not active");
        }
        if ($_module == 'core') {
            throw new \Exception("Module 'core' can't be deactivated!");
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
     */
    protected function buildConfig()
    {

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
            $this->config[$file] = array(
                'active' => $active
            );

            // merge defult config, if available
            $dataFile = $moduleDir . $file . '/data/config.php';
            if (file_exists($dataFile)) {
                $this->config[$file] = array_merge($this->config[$file], include $dataFile);
            }

        }
        closedir($dir);
        ksort($this->config);
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
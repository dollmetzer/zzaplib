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
 * Main Console class as base for console scripts
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2015 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Console extends \dollmetzer\zzaplib\Base {

    /**
     * @var string $scriptname Name of the script (usually 'console' 
     */
    public $scriptname;

    /**
     * @var string $action Name of the action
     */
    public $action;

    /**
     * @var array $commands 
     */
    protected $commands;

    /**
     * Construct the application
     * 
     * @param array $config Configuration array
     */
    public function __construct($config) {

        $this->config = $config;
        $this->dbh = NULL;
        $this->commands = array();
    }

    /**
     * Run the command
     */
    public function run($argv) {

        // only commandline execution allowed 
        if (sizeof($argv) < 1) {
            die("\nRemote call prohibited");
        }

        $this->scriptname = array_shift($argv);

        $this->getCommands();
        if (sizeof($argv) < 1) {
            die("\nNo action given. Valid actions are : " . join(', ', array_keys($this->commands)) . "\n");
        }
        if (!in_array($argv[0], array_keys($this->commands))) {
            die("\nNo valid action given. Valid actions are : " . join(', ', array_keys($this->commands)) . "\n");
        }

        $this->action = array_shift($argv);

        $this->params = $argv;

        //include $this->commands[$this->action];

        $commandName = '\Application\modules\\' . $this->commands[$this->action] . '\commands\\' . $this->action . 'Command';
        try {
            $command = new $commandName($this);
            $command->run();
        } catch (\Exception $e) {
            $message = 'Commandline error in ';
            $message .= $e->getFile() . ' in Line ';
            $message .= $e->getLine() . ' : ';
            $message .= $e->getMessage();
            $this->log($message);
        }
    }

    /**
     * Get a list of valid commands
     */
    public function getCommands() {

        $modules = $this->getModuleList();

        foreach ($modules as $module) {
            $modDir = PATH_APP . 'modules/' . $module . '/commands/';
            if (is_dir($modDir)) {
                $dir = opendir($modDir);
                while ($file = readdir($dir)) {
                    if (preg_match('/Command\.php$/', $file)) {
                        $cname = preg_replace('/Command\.php$/', '', $file);
                        //$this->commands[$cname] = $modDir.$file;
                        $this->commands[$cname] = $module;
                    }
                }
            }
        }
    }

}

?>

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
 * Session wrapper class
 * 
 * Wraps a session to enable controllers to be executed from web (with real PHP session)
 * or from a command line script (without real PHP session)
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2014 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Session {

    /**
     * @var Application $app The application object (singleton)
     */
    protected $app;

    /**
     * @var boolean $is_web If true, use a real session
     */
    protected $is_web;

    /**
     * @var array $data Session repository, if $is_web is false
     */
    protected $data;

	
    /**
     * Constructor
     * 
     * @param Application $_app The application object
     */
    public function __construct($_app) {

        $this->app = $_app;
        $this->init();
		
    }



    /**
     * Initialize a session.
     * 
     * Sets the main session variables.
     * If a mobile channel is in the config, try to detect mobile clients
     * If a mobile client is detectec, try to performs a mobile quicklogin.
     */
    public function init() {

		session_start();

        $hits = $this->hits;
        if (empty($hits)) {
            $this->start = time();
            $this->hits = 1;
            $this->user_id = 0;
            $this->user_handle = 'guest';
            $this->user_lastlogin = 0;
            $this->user_language = $this->app->config['languages'][0];
			$this->theme = $this->app->config['themes'][0];
        } else {
            $hits++;
            $this->hits = $hits;
        }
		
    }



    /**
     * Set a session variable
     * 
     * Depending on $this->is_web stores the value in $_SESSION or in the session object
     * 
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value) {
		$_SESSION[$name] = $value;
    }



    /**
     * Returns a session variable
     * 
     * Depending on $this->is_web stores gets the value from $_SESSION or from the session object
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
		if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        } else {
			return null;
        }
    }



    /**
     * Return Session data as an array
     * 
     * @return array
     */
    public function getAsArray() {
		return $_SESSION;
    }



}

?>
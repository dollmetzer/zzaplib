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
 * Session wrapper class
 *
 * Wraps a session to enable controllers to be executed from web (with real PHP session)
 * or from a command line script (without real PHP session)
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Session
{

    /**
     * @var array $config Configuration array
     */
    protected $config;

    /**
     * @var boolean $is_web If true, use a real session
     */
    protected $is_web;

    /**
     * @var array $data Session repository
     */
    protected $data;

    /**
     * Constructor
     * Deternines, if script is running in web- or cli-context and calls init
     *
     * @param array $_config
     */
    public function __construct(array $_config)
    {

        $this->config = $_config;

        if (empty($_SERVER['REMOTE_ADDR'])) {
            // running from CLI or CRON
            $this->is_web = false;
        } else {
            // running in web context
            session_start();
            $this->is_web = true;
        }

        $this->init();
    }

    /**
     * Initialize a session.
     *
     * Sets the main session variables.
     */
    public function init()
    {

        if ($this->is_web === true) {

            $hits = $this->hits;
            if (empty($hits)) {
                $this->start = time();
                $this->hits = 1;
                $this->user_id = 0;
                $this->user_handle = 'guest';
                $this->user_lastlogin = 0;
                $this->user_language = $this->config['languages'][0];
                $this->user_haspassword = false;
                $this->theme = $this->config['themes'][0];
                $this->groups = array(1 => 'guest');
            } else {
                $hits++;
                $this->hits = $hits;
            }

        } else {
            $this->start = time();
            $this->user_id = 0;
            $this->user_handle = 'guest';
            $this->user_language = $this->config['languages'][0];
            $this->theme = $this->config['themes'][0];
            $this->groups = array(1 => 'guest');
        }

    }

    /**
     * Load User data into the session
     *
     * @param array $_user
     * @param array $_groups
     */
    public function login(array $_user, array $_groups)
    {

        $this->user_id = $_user['id'];
        $this->user_handle = $_user['handle'];
        $this->user_lastlogin = $_user['lastlogin'];
        $this->user_language = $_user['language'];
        $this->user_haspassword = true;
        $groups = array();
        foreach ($_groups as $pos => $group) {
            print_r($group);
            $groups[$group['id']] = $group['name'];
        }
        $this->groups = $groups;
    }

    /**
     * Destroys a session and creates a new one
     */
    public function destroy()
    {

        if ($this->is_web === true) {
            session_destroy();
            session_unset();
            session_start();
        } else {
            $this->data = array();
        }
        $this->init();

    }

    /**
     * Set a session variable
     *
     * Depending on $this->is_web stores the value in $_SESSION or in the session object
     *
     * @param string $_name
     * @param mixed $_value
     */
    public function __set($_name, $_value)
    {
        $_SESSION[$_name] = $_value;
    }

    /**
     * Returns a session variable
     *
     * Depending on $this->is_web stores gets the value from $_SESSION or from the session object
     *
     * @param string $_name
     * @return mixed
     */
    public function __get($_name)
    {
        if (isset($_SESSION[$_name])) {
            return $_SESSION[$_name];
        } else {
            return null;
        }
    }

    /**
     * Return Session data as an array
     *
     * @return array
     */
    public function getAsArray()
    {
        return $_SESSION;
    }

    /**
     * Return, if session is a web session
     *
     * @return bool true if web session, false, if cli call
     */
    public function isWeb()
    {
        return $this->is_web;
    }

}

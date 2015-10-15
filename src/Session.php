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
 * @copyright 2006 - 2015 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Session
{
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
    public function __construct($_app)
    {

        $this->app = $_app;

        $this->init();
    }

    /**
     * Initialize a session.
     * 
     * Sets the main session variables.
     */
    public function init()
    {

        session_start();
        $hits = $this->hits;
        if (empty($hits)) {
            $this->start            = time();
            $this->hits             = 1;
            $this->user_id          = 0;
            $this->user_handle      = 'guest';
            $this->user_lastlogin   = 0;
            $this->user_language    = $this->app->config['core']['languages'][0];
            $this->user_haspassword = false;
            $this->theme            = $this->app->config['core']['themes'][0];
            $this->groups           = array(1 => 'guest');
        } else {
            $hits++;
            $this->hits = $hits;
        }
    }

    /**
     * Login with user credentials
     * 
     * @param array $_user
     */
    public function login($_user)
    {

        $userModel            = new \Application\modules\core\models\userModel($this->app);
        $userModel->setLastlogin($_user['id']);
        $data                 = array(
            'token' => md5($_user['handle'].time().$_user['lastlogin']),
            'useragent' => $_SERVER['HTTP_USER_AGENT']
        );
        $userModel->update($_user['id'], $data);
        $this->user_id        = $_user['id'];
        $this->user_handle    = $_user['handle'];
        $this->user_lastlogin = $_user['lastlogin'];
        $this->user_language  = $_user['language'];
        if (empty($_user['password'])) {
            $this->user_haspassword = false;
        } else {
            $this->user_haspassword = true;
        }

        // get user groups
        $groupModel    = new \Application\modules\core\models\groupModel($this->app);
        $groups        = $groupModel->getUserGroups($_user['id']);
        $sessionGroups = array();
        for ($i = 0; $i < sizeof($groups); $i++) {
            $sessionGroups[$groups[$i]['id']] = $groups[$i]['name'];
        }
        $this->groups = $sessionGroups;

        $this->groups = array('user');
        if ($this->app->config['core']['quicklogin'] === true) {
            // Set cookie for 90 days
            setcookie('qltoken', $data['token'], time() + 60 * 60 * 24 * 90,
                null, null, false, true);
        }
    }

    /**
     * Destroys a session and creates a new one
     */
    public function destroy()
    {

        session_destroy();
        session_unset();
        $this->init();
    }

    /**
     * Set a session variable
     * 
     * Depending on $this->is_web stores the value in $_SESSION or in the session object
     * 
     * @param string $_name
     * @param mixed  $_value
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
     * Write an entry in the session table
     * 
     * CAUTION: There must be the following model in your app:
     * \Application\modules\core\models\sessionModel
     * 
     * @param type $_area
     */
    public function track($_area = '')
    {

        $sessionModel = new \Application\modules\core\models\sessionModel($this->app);
        $sessionModel->update($_area);
    }
}
?>
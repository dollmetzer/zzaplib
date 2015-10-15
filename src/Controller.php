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
 * Main Controller class as a base for all application controllers
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2015 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Controller
{
    /**
     * @var Application $app Holds the instance of the application 
     */
    public $app;

    /**
     * @var array $accessGroups List of action names with lists of allowed groups
     */
    protected $accessGroups;

    /**
     * The constructor tries to execute init(), if it exists.
     * 
     * If permissions are set, they're checked. If no execution right is found,
     * the application jumps to the startpage
     *
     * @param Application $_app The application object
     */
    public function __construct($_app)
    {

        $this->app = $_app;
        $this->app->loadLanguage($this->app->controllerName);

        if (method_exists($this, 'init')) $this->init();
    }

    /**
     * Method is called before any controller action.
     * Overload in the application controller to use
     */
    public function preAction()
    {
        
    }

    /**
     * Method is called after any controller action.
     * Overload in the application controller to use
     */
    public function postAction()
    {
        
    }

    /**
     * Returns a text snippet in the current language.
     * 
     * If no snippet is found, the placeholder with leading and trailing tripple
     * hash and underscore is returned.
     * 
     * @param string  $_snippet A placeholder like ERROR_FORM_TOO_LONG 
     * @return string           The String in the current language
     */
    public function lang($_snippet)
    {

        return $this->app->lang($_snippet);
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

        return $this->app->buildURL($_path, $_attributes);
    }

    /**
     * Build a complete URL from a query string
     * 
     * @param string $_path       Path to the picture on the media server
     * @return string
     */
    public function buildMediaURL($_path)
    {

        return $this->app->buildMediaURL($_path);
    }

    /**
     * Forward to another page
     * 
     * @param string $_url         Target URL 
     * @param string $_message     (optional) flash message to be displayed on next page
     * @param string $_messageType (optinal) Type if flash message. Either 'error' or 'message'
     */
    public function forward($_url = '', $_message = '', $_messageType = '')
    {
        $this->app->forward($_url, $_message, $_messageType);
    }

    /**
     * Check, if call of a certain action is allowed
     * 
     * Checks, if the current user is in the group for the controller action.
     * If no entry is found, access is granted.
     * If an entry is found and the user is group member, access is granted.
     * If an entry is found and the user is not group meber, access is denied.
     *  
     * @param string $_actionName
     * @return boolean
     */
    public function isAllowed($_actionName)
    {

        // allowed, if no entry is found
        if (empty($this->accessGroups[$_actionName])) {
            return true;
        }
        // allowed, if user is group member
        $userGroups   = $this->app->session->groups;
        $intersection = array_intersect($userGroups,
            $this->accessGroups[$_actionName]);
        if (!empty($intersection)) {
            return true;
        }
        return false;
    }

    /**
     * checks, if user is in a group
     * 
     * @param string/integer $_group
     * @return boolean
     */
    public function inGroup($_group)
    {

        $userGroups = $this->app->session->groups;
        if (is_int($_group)) {
            if (in_array($_group, array_keys($userGroups))) {
                return true;
            }
        } else {
            if (in_array($_group, array_values($userGroups))) {
                return true;
            }
        }
        return false;
    }
}
?>

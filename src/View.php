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
 * View class
 * 
 * Manages the output of HTML pages in the web application
 * 
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2014 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class View {

    /**
     * @var array $content Page content elements
     */
    public $content;

    /**
     * @var string $template template file name
     */
    public $template;

    /**
     * @var array $lang language snippets
     */
    public $lang;

    /**
     * @var Application $app The application object
     */
    protected $app;

    /**
     * @var array $js Additional Javascript files to include in the page 
     */
    protected $js;

    /**
     * @var array $css Additional Stylesheet files to include in the page
     */
    protected $css;

    /**
     * Basic setting for the view
     *
     * @param Application $_app The application object
     */
    public function __construct($_app) {

        $this->app = $_app;
        $this->content = array();
        $this->template = '';
        $this->lang = array();
        $this->js = array();
        $this->css = array();
    }

    /**
     * Render the template
     * 
     * If $this->template is not empty, use this file (PATH_APP.$this->template).
     * Normally use the following template file:
     * PATH_APP/modules/MODULE_NAME/views/SESSION_THEME/CONTROLLER_NAME/ACTION_NAME.php
     */
    public function render() {

        $content = & $this->content;
        $lang = & $this->app->lang;
        if (!empty($this->template)) {
            $filename = PATH_APP . $this->template;
        } else {
            $filename = PATH_APP . 'modules/' . $this->app->moduleName;
            $filename .= '/views/' . $this->app->session->theme . '/' . $this->app->controllerName . '/';
            $filename .= $this->app->actionName . '.php';
        }
        include $filename;
    }

    /**
     * Add a javascript file to the page
     * 
     * @param string $_filename
     */
    public function addJS($_filename) {
        $this->js[] = $_filename;
    }

    /**
     * Add a CSS File to the page
     * 
     * @param string $_filename
     */
    public function addCSS($_filename) {
        $this->css[] = $_filename;
    }

    /**
     * checks, if the current user is in a certain group
     * 
     * @param string $_groupName
     * @return boolean
     */
    public function userInGroup($_groupName) {

        $userGroups = $this->app->session->groups;
        if (in_array($_groupName, $userGroups)) {
            return true;
        }
        return false;
    }

    /**
     * Build a URL with a query path considering server settings
     * 
     * @param string $_path Path part of the URL
     * @param boolean $_output  Direct output(default) in the template or return value for use in Controller 
     * @return string|null
     */
    public function buildURL($_path, $_output = true) {

        $url = $this->app->buildURL($_path);

        if ($_output === true) {
            echo $url;
            return;
        }
        return $url;
    }

    /**
     * Build a URL for media assets (e.g. on a external CDN)
     * 
     * @param string $_path Path part of the URL
     * @param boolean $_output  Direct output(default) in the template or return value for use in Controller 
     * @return string|null
     */
    public function buildMediaURL($_path, $_output = true) {

        $url = $this->app->buildMediaURL($_path);

        if ($_output === true) {
            echo $url;
            return;
        }
        return $url;
    }

    /**
     * Returns a text snippet in the current language.
     * If no snippet is found, the placeholder with leading and trailing tripple hash and underscore is returned
     * 
     * @param string  $_snippet A placeholder like ERROR_FORM_TOO_LONG 
     * @param boolean $_output  Direct output(default) in the template or return value for use in Controller 
     * @return string           The String in the current language
     */
    public function lang($_snippet, $_output = true) {

        $text = $this->app->lang($_snippet);

        if ($_output === true) {
            echo $text;
            return;
        }
        return $text;
    }

    /**
     * Returns a formatted date from a database datetime
     * 
     * @param string $_datetime Something like '2010-12-19 06:03_59'
     * @param boolean $_output  Direct output(default) in the template or return value for use in Controller 
     * @return string           formatted date. In this case '19.12.2010' (german format)
     */
    public function toDate($_datetime, $_output = true) {

        $text = strftime($this->lang('format_date', false), strtotime($_datetime));

        if ($_output === true) {
            echo $text;
            return;
        }
        return $text;
    }

    /**
     * Returns a formatted date and time stamp from a database datetime
     * 
     * @param string $_datetime Something like '2010-12-19 06:03:59'
     * @param boolean $_output  Direct output(default) in the template or return value for use in Controller 
     * @return string           formatted date. In this case '19.12.2010 06:03:59' (german format)
     */
    public function toDatetime($_datetime, $_output = true) {

        $text = strftime($this->lang('format_datetime', false), strtotime($_datetime));

        if ($_output === true) {
            echo $text;
            return;
        }
        return $text;
    }

    /**
     * Returns a formatted date and time stamp from a database datetime
     * 
     * @param string $_datetime Something like '2010-12-19 06:03:59'
     * @param boolean $_output  Direct output(default) in the template or return value for use in Controller 
     * @return string           formatted date. In this case '19.12.2010 06:03' (german format)
     */
    public function toDatetimeShort($_datetime, $_output = true) {

        $text = strftime($this->lang('format_datetime_short', false), strtotime($_datetime));

        if ($_output === true) {
            echo $text;
            return;
        }
        return $text;
    }

}

?>
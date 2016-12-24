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
 * View class
 * 
 * Manages the output of HTML pages in the web application
 * 
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2015 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class View
{
    /**
     * @var array $content Page content elements
     */
    public $content;

    /**
     * @var string $template template file name
     */
    public $template;

    /**
     * @var array $languageSnippets Language snippets
     */
    public $languageSnippets;

    /**
     * @var array $js Additional Javascript files to include in the page 
     */
    protected $js;

    /**
     * @var array $css Additional Stylesheet files to include in the page
     */
    protected $css;


    /**
     * @var Session $session
     */
    protected $session;

    /**
     * @var Request $request
     */
    protected $request;


    /**
     * Basic setting for the view
     *
     */
    public function __construct(Session $_session, Request $_request)
    {

        $this->session = $_session;
        $this->request = $_request;

        $this->content  = array();
        $this->template = '';
        $this->js       = array();
        $this->css      = array();
        $this->languageSnippets = array();

    }

    /**
     * Render the template
     * 
     * If $this->template is not empty, use this file (PATH_APP.$this->template).
     * Normally use the following template file:
     * PATH_APP/modules/MODULE_NAME/views/SESSION_THEME/CONTROLLER_NAME/ACTION_NAME.php
     * 
     * @param boolean $_capture If true, captures and returns result.
     * @param string $_template If given, it overrides the standard template and also $this->template
     * @return string
     * @throws \Exception
     */
    public function render($_capture = false, $_template = '')
    {

        $content = & $this->content;

        if (!empty($this->template)) {
            $filename = PATH_APP.$this->template;
        } else {
            $filename = PATH_APP.'modules/'.$this->request->moduleName;
            $filename .= '/views/'.$this->session->theme.'/'.$this->request->controllerName.'/';
            $filename .= $this->request->actionName.'.php';
        }
        if (!empty($_template)) {
            $filename = PATH_APP.$_template;
        }
        if (!file_exists($filename)) {
            throw new \Exception("View: Template $filename not found.");
        }

        // direct output...
        if ($_capture === false) {
            include $filename;
            return;
        }

        /// ... or capture rendering
        ob_start();
        include $filename;
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    /**
     * Add a javascript file to the page
     * 
     * @param string $_filename
     */
    public function addJS($_filename)
    {
        $this->js[] = $_filename;
    }

    /**
     * Add a CSS File to the page
     * 
     * @param string $_filename
     */
    public function addCSS($_filename)
    {
        $this->css[] = $_filename;
    }

    /**
     * checks, if the current user is in a certain group
     * 
     * @param string $_groupName
     * @return boolean
     */
    public function userInGroup($_groupName)
    {

        $userGroups = $this->session->groups;
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
    public function buildURL($_path, $_output = true)
    {

        $url = $this->request->buildURL($_path);

        if ($_output === true) {
            echo $url;
            return;
        }
        return $url;
    }

    /**
     * Returns a formatted date from a database datetime
     * 
     * @param string $_datetime Something like '2010-12-19 06:03_59'
     * @param boolean $_output  Direct output(default) in the template or return value for use in Controller 
     * @return string           formatted date. In this case '19.12.2010' (german format)
     */
    public function toDate($_datetime, $_output = true)
    {

        if ($_datetime == '0000-00-00 00:00:00') {
            $text = '-';
        } else {
            $text = strftime($this->lang('format_date', false),
                strtotime($_datetime));
        }

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
    public function toDatetime($_datetime, $_output = true)
    {


        if ($_datetime == '0000-00-00 00:00:00') {
            $text = '-';
        } else {
            $text = strftime($this->lang('format_datetime', false),
                strtotime($_datetime));
        }

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
    public function toDatetimeShort($_datetime, $_output = true)
    {


        if ($_datetime == '0000-00-00 00:00:00') {
            $text = '-';
        } else {
            $text = strftime($this->lang('format_datetime_short', false),
                strtotime($_datetime));
        }

        if ($_output === true) {
            echo $text;
            return;
        }
        return $text;
    }

    /**
     * Try to load language snippets and store them in $this->lang
     * The name of the file is [language]_[snippet].ini
     * E.g. 'de_account.ini' holds the snippets for the account controller in german.
     *
     * @param string $_snippet Name of the snippet - mostly the controller name
     * @param string $_module Name of the module, if language file shouldn't be for current module
     * @param string $_language two letter code of language, if not to use the user language in the session
     * @return boolean success
     */
    public function loadLanguage($_snippet = 'core', $_module = 'core', $_language = '')
    {

        $filename = PATH_APP . 'modules/' . $_module . '/data/' . $_snippet . '_' . $_language . '.ini';

        if (file_exists($filename)) {
            $lang = parse_ini_file($filename);
            $this->languageSnippets = array_merge($this->languageSnippets, $lang);
            return true;
        } else {
            $this->request->log('Language File ' . $filename . ' not found');
        }
        return false;
    }


    /**
     * Return a language snippet in the current language
     *
     * @param string $_snippet Name of the snippet
     * @return string either the snippet, or - if snippet wasn't defined - the name of the snippet, wrapped in ###_ _###
     */
    public function lang($_snippet, $_output = true)
    {

        if (isset($this->languageSnippets[$_snippet])) {
            $text = $this->languageSnippets[$_snippet];
        } else {
            $text = '###_'.$_snippet.'_###';
        }

        if ($_output === true) {
            echo $text;
            return;
        }
        return $text;

    }

}
?>
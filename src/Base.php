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
 * Base class for Application and Api class
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Base
{

    /**
     * @var array $config The configuration of the application
     */
    public $config;

    /**
     * @var \PDO $dbh Database handle
     */
    public $dbh;

    /**
     * @var array $languageSnippets Language snippets
     */
    public $languageSnippets;

    /**
     * Construct the application / api / cli
     *
     * @param array $config Configuration array
     */
    public function __construct($config)
    {

        $this->config = $config;
        $this->dbh = null;
        $this->languageSnippets = array();

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
     * If modules are not in the configuration, read list from filesystem
     * in app/modules.
     *
     * @return array
     */
    public function getModuleList()
    {

        if (empty($this->config['modules'])) {
            $list = array();
            if (file_exists(PATH_APP . 'modules/')) {
                $dir = opendir(PATH_APP . 'modules/');
                while ($file = readdir($dir)) {
                    if (!preg_match('/^\./', $file)) {
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
        sort($list);

        return $list;
    }

    /**
     * Get a list of available controllers for the module $this->moduleName
     * If modules are set in the configuration, get the list from the
     * configuration. Without configuration entry, get the list from the filesystem.
     *
     * @return array
     */
    public function getControllerList($_moduleName)
    {

        if (!empty($this->config['modules'][$_moduleName])) {

            $list = $this->config['modules'][$_moduleName];
        } else {

            $list = array();
            $controllerDir = PATH_APP . 'modules/' . $_moduleName . '/controllers/';
            $dir = opendir($controllerDir);
            while ($file = readdir($dir)) {
                if (preg_match('/Controller.php$/', $file)) {
                    $list[] = preg_replace('/Controller.php$/', '', $file);
                }
            }
            closedir($dir);
        }
        sort($list);

        return $list;
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
            $this->log('Language File ' . $filename . ' not found');
        }
        return false;
    }

    /**
     * Return a language snippet in the current language
     *
     * @param string $_snippet Name of the snippet
     * @return string either the snippet, or - if snippet wasn't defined - the name of the snippet, wrapped in ###_ _###
     */
    public function lang($_snippet)
    {

        if (isset($this->languageSnippets[$_snippet])) {
            $text = $this->languageSnippets[$_snippet];
        } else {
            $text = '###_'.$_snippet.'_###';
        }

        return $text;
    }

    /**
     * Write a log entry.
     *
     * The logfile is in PATH_LOGS and its name consist of type and
     * date (e.g. notice_2015_07_25.txt).
     *
     * @param string $_message
     * @param string $_type 'error'(default) or 'notice'
     */
    public function log($_message, $_type = 'error')
    {

        if (in_array($_type, array('error', 'notice'))) {
            $type = $_type;
        } else {
            $type = 'error';
        }

        $message = strftime('%d.%m.%Y %H:%M:%S', time());
        $message .= "\t" . $_message . "\n";

        $logfile = PATH_LOGS . $type . strftime('_%Y_%m_%d.txt', time());
        if (!file_exists($logfile)) {
            $fp = fopen($logfile, 'w+');
            fwrite($fp, "Logfile $logfile\n--------------------\n");
            fclose($fp);
            chmod($logfile, 0664);
        }
        $fp = fopen($logfile, 'a+');
        fwrite($fp, $message);
        fclose($fp);
    }

}
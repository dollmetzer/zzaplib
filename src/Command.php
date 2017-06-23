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
 * Base class for console commands
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Command
{

    /**
     * @var array $config Configuration array
     */
    public $config;


    /**
     * Constructor
     *
     * @param array $config Configuration array
     */
    public function __construct(array $config)
    {

        $this->config = $config;

    }

    // --------------------------------------------------
    // From here: Same methods, as in Request.
    // To be done: build base class
    // --------------------------------------------------

    /**
     * Build a complete URL from a query string
     *
     * @param string $_path Query string like controller/action/param_1/param_n
     * @param array $_attributes Additional Attributes. Array of key=>value pairs
     * @return string
     */
    public function buildURL($_path, $_attributes = array())
    {

        if (URL_HTTPS === true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $url = $protocol . URL_BASE;
        if (URL_REWRITE) {
            $url .= '/' . $_path;
        } else {
            if (!empty($_path)) {
                $url .= '/index.php?q=' . $_path;
            }
        }

        if (!empty($_attributes)) {
            $addition = array();
            foreach ($_attributes as $key => $val) {
                $addition[] = $key . '=' . urlencode($val);
            }
            $url .= '&' . join('&', $addition);
        }

        return $url;
    }

    /**
     * Build a complete URL for media files
     *
     * @param string $_path Path to media file
     * @return string
     */
    public function buildMediaURL($_path)
    {

        if (URL_HTTPS === true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $url = $protocol . URL_MEDIA . '/' . $_path;

        return $url;

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
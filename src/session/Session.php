<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
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

namespace dollmetzer\zzaplib\session;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\exception\ApplicationException;

/**
 * Class Session
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\session
 */
class Session implements SessionInterface
{
    const UNDEFINED_EXCEPTION_MESSAGE = 'Unknown session attribute';

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        if(!defined('UNIT_TEST')) {
            session_start();
        }
        if(empty($_SESSION['sessionHits'])) {
            $this->init();
        } else {
            $_SESSION['sessionHits']++;
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ApplicationException
     */
    public function get(string $name)
    {
        if(isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        throw new ApplicationException(self::UNDEFINED_EXCEPTION_MESSAGE);
    }

    /**
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function delete(string $name)
    {
        if(isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Initialize session and user data
     */
    protected function init()
    {
        $this->set('sessionId', session_id());
        $this->set('sessionStart', time());
        $this->set('sessionHits', 1);
        $this->set('userId', 0);
        $this->set('userHandle', 'guest');
        $this->set('userGroups', ['guest']);
        if($this->config->isSet('languages')) {
            $languages = $this->config->get('languages');
            $this->set('userLanguage', array_shift($languages));
        }
        if($this->config->isSet('countries')) {
            $countries = $this->config->get('countries');
            $this->set('userCountry', array_shift($countries));
        }
    }
}
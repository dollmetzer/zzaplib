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

namespace dollmetzer\zzaplib;

use dollmetzer\zzaplib\exception\ApplicationException;

/**
 * Class Config
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class Config
{

    const ERROR_CONFIG_NOT_FOUND = 'No configuration found';
    const ERROR_CONFIG_PARSING = 'Error parsing configuration file';
    const ERROR_CONFIG_VALUE_NOT_SET = 'The config value is not set';

    /**
     * @var array $configData
     */
    protected $configData;

    /**
     * Config constructor.
     *
     * @param string $configFile
     * @throws ApplicationException
     */
    public function __construct(string $configFile)
    {
        $this->loadConfig($configFile);
    }

    /**
     * @param string $key
     * @param string $domain
     * @return bool
     */
    public function isSet(string $key, string $domain='')
    {
        if(!empty($domain)) {
            return isset($this->configData[$domain][$key]);
        } else {
            return isset($this->configData[$key]);
        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ApplicationException
     */
    public function get(string $key, string $domain='')
    {
        if(!empty($domain)) {
            if (!isset($this->configData[$domain][$key])) {
                throw new ApplicationException(self::ERROR_CONFIG_VALUE_NOT_SET . ' ' . $domain. ' ' . $key);
            }
            $value = $this->configData[$domain][$key];

        } else {
            if (!isset($this->configData[$key])) {
                throw new ApplicationException(self::ERROR_CONFIG_VALUE_NOT_SET . ' ' . $key);
            }
            $value = $this->configData[$key];
        }

        return $value;
    }

    /**
     * @param string $configFile
     * @throws ApplicationException
     */
    protected function loadConfig(string $configFile)
    {

        if (!file_exists($configFile)) {
            throw new ApplicationException(self::ERROR_CONFIG_NOT_FOUND);
        }

        $this->configData = include($configFile);

        if ($this->configData === false) {
            throw new ApplicationException(self::ERROR_CONFIG_PARSING);
        }

    }

}
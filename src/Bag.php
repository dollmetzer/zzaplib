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

use dollmetzer\zzaplib\exception\BagException;

/**
 * Class Bag
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class Bag
{

    const EXCEPTION_MESSAGE_NO_CLASS = '';
    const EXCEPTION_MESSAGE_NOT_FOUND = '';

    private $repository = [];


    /**
     * @param string $name
     * @param object $object
     * @throws BagException
     */
    public function set(string $name, $object)
    {
        if (!is_object($object)) {
            throw new BagException(self::EXCEPTION_MESSAGE_NO_CLASS);
        }
        $this->repository[$name] = $object;
    }

    /**
     * @param string $name
     * @return object
     * @throws BagException
     */
    public function get(string $name)
    {
        if (empty($this->repository[$name])) {
            throw new BagException(self::EXCEPTION_MESSAGE_NOT_FOUND);
        }

        return $this->repository[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        if (empty($this->repository[$name])) {
            return false;
        }
        return true;
    }
}

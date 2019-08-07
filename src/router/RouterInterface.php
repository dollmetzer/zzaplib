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

namespace dollmetzer\zzaplib\router;

/**
 * Interface RouterInterface
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\router
 */
interface RouterInterface
{

    public function resolve();

    /**
     * Build a complete URL from a query string
     *
     * @param string $path Query string like controller/action/param_1/param_n
     * @param array $attributes Additional Attributes. Array of key=>value pairs
     * @return string
     */
    public function buildURL(string $path, array $attributes = []);

    /**
     * Build a complete URL for media files
     *
     * @param string $path Path to media file
     * @return string
     */
    public function buildMediaURL(string $path);

    /**
     * @return string
     */
    public function getModule() : string;

    /**
     * @return string
     */
    public function getController() : string;


    /**
     * @return string
     */
    public function getAction() : string;

    /**
     * @return array
     */
    public function getParams() : array;

}
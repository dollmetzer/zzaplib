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
 * Api Controller class as a base for all api controllers
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2017 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class ApiController
{

    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * The constructor tries to execute init(), if it exists.
     *
     * If permissions are set, they're checked. If no execution right is found,
     * the application jumps to the startpage
     *
     * @param array $_config
     * @param Request $_request
     */
    public function __construct($_config, Request $_request)
    {

        $this->config = $_config;
        $this->request = $_request;

        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

}
